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
        $userRole = $user->role ?? 'anggota_tim';

        $search = $request->query('search');

        $query = Project::query()->with(['tasks.assignee']);

        if ($userRole === 'admin') {
        } elseif ($userRole === 'ketua_tim') {
            $query->where('team_leader_id', $user->id);
        } elseif ($userRole === 'anggota_tim') {
            $query->whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        } else {
            abort(403, 'Anda tidak memiliki akses ke proyek.');
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $projects = $query->get();

        return view('projects.index', compact(
            'projects',
            'search',
            'userRole'
        ));
    }

    public function create(Project $project)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota_tim';

        if (!in_array($userRole, ['admin', 'ketua_tim'])) {
            abort(403, 'Akses ditolak.');
        }

        $users = \App\Models\User::whereIn('role', ['ketua_tim', 'anggota_tim'])->get();

        $statuses = ['Pending', 'In Progress', 'Completed'];
        $priorities = ['low', 'medium', 'high'];

        return view('tasks.create', compact('project', 'users', 'statuses', 'priorities', 'userRole'));
    }

    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota_tim';

        if (!in_array($userRole, ['admin', 'ketua_tim'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['Pending', 'In Progress', 'Completed'])],
            'due_date' => 'nullable|date|after:now|before_or_equal:' . ($project->deadline ?? now()->addYears(1)),
            'assigned_to' => ['nullable', 'exists:users,id'],
            'priority' => 'required|string',
        ]);

        $project->tasks()->create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
        ]);

        return redirect()->route('projects.show', $project)->with('success', 'Task berhasil dibuat.');
    }

    public function show(Project $project, Task $task)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota_tim';

        if ($task->project_id !== $project->id) {
            abort(404, 'Tugas tidak ditemukan dalam proyek ini.');
        }

        $task->load(['project', 'assignee', 'project.creator']);

        return view('tasks.show', compact('project', 'task', 'userRole'));
    }

    public function edit(Project $project, Task $task)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota_tim';

        if ($userRole === 'anggota_tim' && $task->assigned_to != $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $users = User::whereIn('role', ['ketua_tim', 'anggota_tim'])->get();

        $statuses = ['Pending', 'In Progress', 'Completed'];
        $priorities = ['low', 'medium', 'high'];

        return view('tasks.edit', compact('project', 'task', 'users', 'statuses', 'priorities', 'userRole'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota_tim';

        if ($userRole === 'anggota_tim' && $task->assigned_to != $user->id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['Pending', 'In Progress', 'Completed'])],
            'due_date' => 'nullable|date|after:now',
            'assigned_to' => ['nullable', 'exists:users,id'],
            'priority' => 'required|string',
        ];

        $request->validate($rules);

        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'assigned_to' => in_array($userRole, ['admin', 'ketua_tim']) ? $request->assigned_to : $task->assigned_to,
            'priority' => $request->priority,
        ]);

        return redirect()->route('projects.show', $task->project)
            ->with('success', 'Task berhasil diupdate.');
    }

    public function destroy(Project $project, Task $task)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota_tim';

        if (!in_array($userRole, ['admin', 'ketua_tim'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $task->delete();

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Task berhasil dihapus.');
    }

    public function upload(Request $request, Project $project, Task $task)
    {
        if (auth()->id() !== $task->assigned_to) {
            abort(403, 'Anda tidak memiliki izin untuk mengunggah tugas ini.');
        }

        $request->validate([
            'submission_file' => 'required|file|max:10240',
        ]);

        if ($request->hasFile('submission_file')) {
            $file = $request->file('submission_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('submissions', $filename, 'public');

            $task->update([
                'submission_file' => $path,
                'completed_at' => now(),
                'status' => 'Completed',
            ]);
        }

        return redirect()
            ->route('projects.tasks.show', [$project, $task])
            ->with('success', 'Hasil tugas berhasil diunggah dan ditandai sebagai selesai.');
    }

    public function globalIndex(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role ?? 'anggota_tim';

        $query = \App\Models\Task::with('project', 'assignee');

        if ($userRole === 'ketua_tim') {
            $query->whereHas('project', function ($q) use ($user) {
                $q->where('team_leader_id', $user->id);
            });
        } elseif ($userRole === 'anggota_tim') {
            $query->where('assigned_to', $user->id);
        }

        $search = $request->input('search');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(10);

        return view('tasks.index', compact('tasks', 'search', 'userRole'));
    }
}
