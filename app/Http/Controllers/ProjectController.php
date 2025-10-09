<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';
        $search = $request->query('search');

        // Role-based query base
        $query = Project::with(['creator', 'tasks']);  // Load relations

        if ($userRole === 'admin' || $userRole === 'ketua_tim') {
            // Admin & Ketua: Lihat semua projects (dibuat admin, untuk tim)
            $projects = $query;
        } else {
            // Anggota: Hanya projects dengan tasks assigned to them
            $projects = $query->whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }

        // Search by name
        if ($search) {
            $projects = $projects->where('name', 'like', '%' . $search . '%');
        }

        $projects = $projects->latest()->paginate(10);

        return view('projects.index', compact('projects', 'userRole', 'search'));
    }

    public function create()
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Hanya admin bisa create
        if ($userRole !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang bisa membuat proyek.');
        }

        $statuses = ['Planning', 'In Progress', 'Completed', 'On Hold'];

        return view('projects.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Hanya admin
        if ($userRole !== 'admin') {
            return redirect()->route('projects.index')->with('error', 'Akses ditolak.');
        }

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['Planning', 'In Progress', 'Completed', 'On Hold'])],
        ]);

        Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'created_by' => $user->id,
        ]);

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dibuat!');
    }

    public function show(Project $project)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Admin & Ketua: Bisa lihat semua
        // Anggota: Hanya jika ada tasks assigned
        if ($userRole === 'anggota' && !$project->tasks()->where('assigned_to', $user->id)->exists()) {
            abort(403, 'Anda tidak punya akses ke proyek ini.');
        }

        $project->load(['creator', 'tasks.assignee']);

        return view('projects.show', compact('project', 'userRole'));
    }

    public function edit(Project $project)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Admin & Ketua bisa edit semua projects
        if (!in_array($userRole, ['admin', 'ketua_tim'])) {
            abort(403, 'Akses ditolak.');
        }

        $statuses = ['Planning', 'In Progress', 'Completed', 'On Hold'];

        return view('projects.edit', compact('project', 'statuses'));
    }

    public function update(Request $request, Project $project)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Admin & Ketua
        if (!in_array($userRole, ['admin', 'ketua_tim'])) {
            return redirect()->route('projects.index')->with('error', 'Akses ditolak.');
        }

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['Planning', 'In Progress', 'Completed', 'On Hold'])],
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil diupdate!');
    }

    public function destroy(Project $project)
    {
        $user = Auth::user();
        $userRole = $user->role ?? 'anggota';

        // Hanya admin
        if ($userRole !== 'admin') {
            return redirect()->route('projects.index')->with('error', 'Akses ditolak.');
        }

        $project->delete();  // Cascade tasks

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dihapus!');
    }
}
