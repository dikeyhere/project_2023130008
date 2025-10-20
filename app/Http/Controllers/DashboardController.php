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
        $userRole = $user->role ?? 'anggota_tim';

        $showWelcome = !$request->session()->has('hasSeenWelcome');
        if ($showWelcome) {
            $request->session()->put('hasSeenWelcome', true);
        }

        if ($userRole === 'admin') {
            $totalProjects = Project::count();
            $completedProjects = Project::where('status', 'Completed')->count();
            $totalTasks = Task::count();
            $completedTasks = Task::where('status', 'Completed')->count();

            $recentProjects = Project::with(['creator', 'tasks'])
                ->latest()
                ->take(5)
                ->get();

            $projectQuery = Project::query();
        } elseif ($userRole === 'ketua_tim') {
            $totalProjects = Project::where('team_leader_id', $user->id)->count();
            $completedProjects = Project::where('team_leader_id', $user->id)
                ->where('status', 'Completed')
                ->count();

            $totalTasks = Task::whereHas('project', function ($q) use ($user) {
                $q->where('team_leader_id', $user->id);
            })->count();
            $completedTasks = Task::whereHas('project', function ($q) use ($user) {
                $q->where('team_leader_id', $user->id);
            })->where('status', 'Completed')->count();

            $recentProjects = Project::where('team_leader_id', $user->id)
                ->with(['creator', 'tasks'])
                ->latest()
                ->take(5)
                ->get();

            $projectQuery = Project::where('team_leader_id', $user->id);
        } else {
            $totalProjects = Project::whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->count();

            $completedProjects = Project::whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->where('status', 'Completed')->count();

            $totalTasks = Task::where('assigned_to', $user->id)->count();
            $completedTasks = Task::where('assigned_to', $user->id)
                ->where('status', 'Completed')
                ->count();

            $recentProjects = Project::whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })
                ->with(['creator', 'tasks'])
                ->latest()
                ->take(5)
                ->get();

            $projectQuery = Project::whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }

        $projectStatuses = $projectQuery
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

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
