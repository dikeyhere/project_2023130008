<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $showWelcome = !$request->session()->has('hasSeenWelcome');
        if ($showWelcome) {
            $request->session()->put('hasSeenWelcome', true);
        }

        $totalProjects     = 0;
        $completedProjects = 0;
        $totalTasks        = 0;
        $completedTasks    = 0;
        $recentProjects    = collect();
        $projectsQuery     = Project::query();

        if ($user->can('view projects')) {
            $projectsQuery = Project::query();

            $totalProjects     = Project::count();
            $completedProjects = Project::where('status', 'Completed')->count();
            $totalTasks        = Task::count();
            $completedTasks    = Task::where('status', 'Completed')->count();

            $recentProjects = Project::with(['creator', 'tasks'])
                ->latest()
                ->take(5)
                ->get();

        } elseif ($user->can('view own projects')) {
            $projectsQuery = Project::where('team_leader_id', $user->id);

            $totalProjects     = $projectsQuery->count();
            $completedProjects = (clone $projectsQuery)->where('status', 'Completed')->count();

            $totalTasks = Task::whereHas('project', fn($q) => $q->where('team_leader_id', $user->id))->count();
            $completedTasks = Task::whereHas('project', fn($q) => $q->where('team_leader_id', $user->id))
                ->where('status', 'Completed')->count();

            $recentProjects = Project::where('team_leader_id', $user->id)
                ->with(['creator', 'tasks'])
                ->latest()
                ->take(5)
                ->get();

        } elseif ($user->can('view assigned tasks')) {
            $projectsQuery = Project::whereHas('tasks', fn($q) => $q->where('assigned_to', $user->id));

            $totalProjects = $projectsQuery->count();

            $completedProjects = Project::whereHas('tasks', fn($q) => $q->where('assigned_to', $user->id))
                ->where('status', 'Completed')->count();

            $totalTasks = Task::where('assigned_to', $user->id)->count();
            $completedTasks = Task::where('assigned_to', $user->id)->where('status', 'Completed')->count();

            $recentProjects = Project::whereHas('tasks', fn($q) => $q->where('assigned_to', $user->id))
                ->with(['creator', 'tasks'])
                ->latest()
                ->take(5)
                ->get();
        }

        $projectStatuses = $projectsQuery
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $userRole = $user->getRoleNames()->first() ?? 'User';

        return view('dashboard', compact(
            'totalProjects',
            'completedProjects',
            'totalTasks',
            'completedTasks',
            'recentProjects',
            'projectStatuses',
            'userRole',
            'showWelcome'
        ));
    }
}
