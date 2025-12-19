<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:view tasks')->only(['index', 'show', 'globalIndex']);
        $this->middleware('permission:create tasks')->only(['create', 'store']);
        $this->middleware('permission:edit tasks')->only(['edit', 'update']);
        $this->middleware('permission:delete tasks')->only(['destroy']);
        $this->middleware('permission:submit tasks')->only(['upload']);
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $search = $request->query('search', '');

        $query = Project::with(['tasks.assignee']);

        if ($user->hasRole('ketua_tim')) {
            $query->where('team_leader_id', $user->id);
        } elseif ($user->hasRole('anggota_tim')) {
            $query->whereHas('tasks', fn($q) => $q->where('assigned_to', $user->id));
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $projects = $query->get();

        return view('projects.index', compact('projects', 'search'));
    }

    public function create(Project $project)
    {
        $users = User::role(['ketua_tim', 'anggota_tim'])->get();
        $statuses = ['Pending', 'In Progress', 'Completed'];
        $priorities = ['low', 'medium', 'high'];

        return view('tasks.create', compact('project', 'users', 'statuses', 'priorities'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['Pending', 'In Progress', 'Completed'])],
            'due_date' => 'nullable|date|after:now|before_or_equal:' . ($project->deadline ?? now()->addYear()),
            'assigned_to' => ['nullable', 'exists:users,id'],
            'priority' => 'required|string',
        ]);

        $project->tasks()->create($request->only(
            'name',
            'description',
            'status',
            'due_date',
            'assigned_to',
            'priority'
        ));

        return redirect()->route('projects.show', $project)
            ->with('success', 'Task berhasil dibuat.');
    }

    public function show(Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            abort(404, 'Task tidak ditemukan dalam proyek ini.');
        }

        $task->load(['project', 'assignee', 'project.creator']);

        return view('tasks.show', compact('project', 'task'));
    }

    public function edit(Project $project, Task $task)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->hasRole('anggota_tim') && $task->assigned_to !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $users = User::role(['ketua_tim', 'anggota_tim'])->get();
        $statuses = ['Pending', 'In Progress', 'Completed'];
        $priorities = ['low', 'medium', 'high'];

        return view('tasks.edit', compact('project', 'task', 'users', 'statuses', 'priorities'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->hasRole('anggota_tim') && $task->assigned_to !== $user->id) {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['Pending', 'In Progress', 'Completed'])],
            'due_date' => 'nullable|date|after:now',
            'assigned_to' => ['nullable', 'exists:users,id'],
            'priority' => 'required|string',
        ]);

        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'assigned_to' => $user->hasAnyRole(['admin', 'ketua_tim'])
                ? $request->assigned_to
                : $task->assigned_to,
            'priority' => $request->priority,
        ]);

        return redirect()->route('projects.show', $task->project)
            ->with('success', 'Task berhasil diupdate.');
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Task berhasil dihapus.');
    }

    public function upload(Request $request, Project $project, Task $task)
    {
        $user = auth()->user();

        if ($user->id !== $task->assigned_to) {
            abort(403, 'Anda tidak memiliki izin untuk mengunggah tugas ini.');
        }

        $request->validate([
            'submission_file' => 'nullable|file|max:10240',
            'submission_text' => 'nullable|string',
        ]);

        if ($request->hasFile('submission_file')) {
            $file = $request->file('submission_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('submissions', $filename, 'public');
            $task->submission_file = $path;
        }

        if ($request->filled('submission_text')) {
            $task->submission_text = $request->submission_text;
        }

        $task->status = 'Completed';
        $task->completed_at = now();
        $task->save();

        return redirect()
            ->route('projects.tasks.show', [$project, $task])
            ->with('success', 'Hasil tugas berhasil dikirim.');
    }

    public function globalIndex(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $query = Task::with('project', 'assignee');

        if ($user->hasRole('ketua_tim')) {
            $query->whereHas('project', fn($q) => $q->where('team_leader_id', $user->id));
        } elseif ($user->hasRole('anggota_tim')) {
            $query->where('assigned_to', $user->id);
        }
        $search = $request->query('search');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(10);

        return view('tasks.index', compact('tasks', 'search'));
    }
}
