<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:view projects')->only(['index']);
        $this->middleware('permission:view project detail')->only(['show']);
        $this->middleware('permission:create projects')->only(['create', 'store']);
        $this->middleware('permission:edit projects')->only(['edit', 'update']);
        $this->middleware('permission:delete projects')->only(['destroy']);
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $search = $request->query('search');
        $filterPriority = $request->query('filter_priority');
        $filterStatus = $request->query('filter_status');
        $sortBy = $request->query('sort_by');

        $projects = Project::query()->with(['tasks.assignee']);

        if ($user->hasRole('ketua_tim')) {
            $projects->where('team_leader_id', $user->id);
        } elseif ($user->hasRole('anggota_tim')) {
            $projects->whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }

        if ($filterStatus) $projects->where('status', $filterStatus);
        if ($filterPriority) $projects->where('priority', $filterPriority);
        if ($search) $projects->where('name', 'like', "%{$search}%");

        switch ($sortBy) {
            case 'priority_desc':
                $projects->orderByRaw("FIELD(priority, 'high','medium','low') desc");
                break;
            case 'priority_asc':
                $projects->orderByRaw("FIELD(priority, 'high','medium','low') asc");
                break;
            case 'deadline_asc':
                $projects->orderBy('deadline', 'asc');
                break;
            case 'deadline_desc':
                $projects->orderBy('deadline', 'desc');
                break;
            default:
                $projects->orderBy('created_at', 'desc');
        }

        $projects = $projects->get();
        $userRole = $user->getRoleNames()->first() ?? 'anggota_tim';

        return view('projects.index', compact('projects', 'search', 'filterPriority', 'filterStatus', 'sortBy', 'userRole'));
    }

    public function create()
    {
        $teamLeaders = User::role('ketua_tim')->orderBy('name')->get();
        $statuses = ['Planning', 'In Progress', 'Completed', 'On Hold'];

        return view('projects.create', compact('statuses', 'teamLeaders'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['Planning', 'In Progress', 'Completed', 'On Hold'])],
            'priority' => ['required', Rule::in(['high', 'medium', 'low'])],
            'deadline' => 'nullable|date|after_or_equal:today',
            'team_leader_id' => 'required|exists:users,id',
            'budget' => 'required|numeric|min:0',
        ]);

        $leader = User::findOrFail($request->team_leader_id);
        if (!$leader->hasRole('ketua_tim')) {
            return back()->withErrors(['team_leader_id' => 'User yang dipilih bukan Ketua Tim yang valid.']);
        }

        Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'created_by' => $user->id,
            'priority' => $request->priority,
            'deadline' => $request->deadline,
            'team_leader_id' => $request->team_leader_id,
            'budget' => $request->budget,
        ]);

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dibuat!');
    }

    public function show(Project $project)
    {
        /** @var User $user */
        $user = auth()->user();

        if (
            $user->can('view project detail') &&
            ($user->hasRole('admin') ||
                ($user->hasRole('ketua_tim') && $project->team_leader_id === $user->id) ||
                ($user->hasRole('anggota_tim') && $project->tasks()->where('assigned_to', $user->id)->exists()))
        ) {
            $userRole = $user->getRoleNames()->first() ?? 'anggota_tim';

            $expenses = $project->expenses()->with('user')->latest()->get();
            $approvedExpense = $project->expenses()->where('status', 'approved')->sum('amount');
            $remainingBudget = $project->budget - $approvedExpense;

            return view('projects.show', compact('project', 'userRole', 'expenses', 'approvedExpense', 'remainingBudget'));
        }

        abort(403, 'Anda tidak memiliki akses ke proyek ini.');
    }

    public function edit(Project $project)
    {
        $user = Auth::user();

        if (!$user->can('edit projects')) {
            abort(403, 'Akses ditolak.');
        }

        $statuses = ['Planning', 'In Progress', 'Completed', 'On Hold'];
        $teamLeaders = User::role('ketua_tim')->get();

        return view('projects.edit', compact('project', 'statuses', 'teamLeaders'));
    }

    public function update(Request $request, Project $project)
    {
        $user = Auth::user();

        if (!$user->can('edit projects')) {
            return redirect()->route('projects.index')->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['Planning', 'In Progress', 'Completed', 'On Hold'])],
            'priority' => ['required', Rule::in(['high', 'medium', 'low'])],
            'deadline' => 'nullable|date|after_or_equal:today',
            'team_leader_id' => 'required|exists:users,id',
            'budget' => 'required|numeric|min:0',
        ]);

        if ($request->status === 'Completed') {
            $incompleteTasks = $project->tasks()->where('status', '!=', 'Completed')->count();
            $progress = $project->progress;

            if ($incompleteTasks > 0 || $progress < 100) {
                return redirect()->back()->with('error', 'Proyek tidak bisa ditandai selesai karena masih ada tugas yang belum selesai atau progress belum 100%.');
            }
        }

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil diupdate!');
    }

    public function destroy(Project $project)
    {
        $user = Auth::user();

        if (!$user->can('delete projects')) {
            return redirect()->route('projects.index')->with('error', 'Akses ditolak.');
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dihapus!');
    }
}
