<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';
        $search = $request->query('search');
        $projectId = $request->query('project_id');  // Filter by project (dari show project)

        // Base query with relations
        $query = Task::with(['project', 'assignee', 'project.creator']);

        if ($userRole === 'admin' || $userRole === 'ketua_tim') {
            // Admin & Ketua: Semua tasks (atau filter by project)
            $tasks = $query;
            if ($projectId) {
                $tasks = $tasks->where('project_id', $projectId);
            }
        } else {
            // Anggota: Hanya own tasks
            $tasks = $query->where('assigned_to', $user->id);
            if ($projectId) {
                $tasks = $tasks->where('project_id', $projectId);
            }
        }

        // Search by name
        if ($search) {
            $tasks = $tasks->where('name', 'like', '%' . $search . '%');
        }

        $tasks = $tasks->latest()->paginate(10);

        // Get projects for filter (admin/ketua only)
        $projects = ($userRole === 'admin' || $userRole === 'ketua_tim') ? Project::all() : [];

        return view('tasks.index', compact('tasks', 'userRole', 'search', 'projectId', 'projects'));
    }

    public function create()
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Hanya admin & ketua
        if (!in_array($userRole, ['admin', 'ketua_tim'])) {
            abort(403, 'Akses ditolak.');
        }

        $projects = Project::all();  // Pilih project
        $users = User::whereIn('role', ['ketua_tim', 'anggota'])->get();  // Assign ke ketua/anggota
        $statuses = ['Pending', 'In Progress', 'Completed'];

        return view('tasks.create', compact('projects', 'users', 'statuses'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Hanya admin & ketua
        if (!in_array($userRole, ['admin', 'ketua_tim'])) {
            return redirect()->route('tasks.index')->with('error', 'Akses ditolak.');
        }

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['Pending', 'In Progress', 'Completed'])],
            'due_date' => 'nullable|date|after:now',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',  // Opsional jika tidak assign
        ]);

        Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'project_id' => $request->project_id,
            'assigned_to' => $request->assigned_to,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task berhasil dibuat!');
    }

    public function show(Task $task)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Admin & Ketua: Semua
        // Anggota: Hanya own
        if ($userRole === 'anggota' && $task->assigned_to != $user->id) {
            abort(403, 'Anda tidak punya akses ke task ini.');
        }

        $task->load(['project', 'assignee', 'project.creator']);

        return view('tasks.show', compact('task', 'userRole'));
    }

    public function edit(Task $task)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Admin & Ketua full; Anggota hanya own
        if ($userRole === 'anggota' && $task->assigned_to != $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $projects = ($userRole === 'admin' || $userRole === 'ketua_tim') ? Project::all() : [];  // Anggota no project select
        $users = ($userRole === 'admin' || $userRole === 'ketua_tim') ? User::whereIn('role', ['ketua_tim', 'anggota'])->get() : [];  // Anggota no user select
        $statuses = ['Pending', 'In Progress', 'Completed'];

        return view('tasks.edit', compact('task', 'projects', 'users', 'statuses', 'userRole'));
    }

    public function update(Request $request, Task $task)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Sama seperti edit
        if ($userRole === 'anggota' && $task->assigned_to != $user->id) {
            return redirect()->route('tasks.index')->with('error', 'Akses ditolak.');
        }

        // Validation (project_id & assigned_to optional untuk update, agar anggota bisa ubah status saja)
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['Pending', 'In Progress', 'Completed'])],
            'due_date' => 'nullable|date|after:now',
        ];
        if (in_array($userRole, ['admin', 'ketua_tim'])) {
            $rules['project_id'] = 'required|exists:projects,id';
            $rules['assigned_to'] = 'nullable|exists:users,id';
        }

        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
        ];
        if (in_array($userRole, ['admin', 'ketua_tim'])) {
            $updateData['project_id'] = $request->project_id;
            $updateData['assigned_to'] = $request->assigned_to;
        }

        $task->update($updateData);

        return redirect()->route('tasks.index')->with('success', 'Task berhasil diupdate!');
    }

    public function destroy(Task $task)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Hanya admin & ketua; Anggota no delete
        if (!in_array($userRole, ['admin', 'ketua_tim'])) {
            return redirect()->route('tasks.index')->with('error', 'Akses ditolak.');
        }

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task berhasil dihapus!');
    }
}
