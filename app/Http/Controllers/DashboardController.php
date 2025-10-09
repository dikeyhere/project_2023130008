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
        $userRole = $user->role ?? 'anggota';

        // Logic Alert: Hanya muncul pertama kali login (per session)
        $showWelcome = !$request->session()->has('hasSeenWelcome');  // True jika belum ada key
        if ($showWelcome) {
            $request->session()->put('hasSeenWelcome', true);  // Set persist untuk seluruh session
        }

        // Base queries role-based
        if ($userRole === 'admin') {
            // Admin: Lihat semua
            $totalProjects = Project::count();
            $completedProjects = Project::where('status', 'Completed')->count();
            $totalTasks = Task::count();
            $completedTasks = Task::where('status', 'Completed')->count();
            $recentProjects = Project::with(['creator', 'tasks'])->latest()->take(5)->get();
            $projectQuery = Project::query();  // Untuk statuses
        } elseif ($userRole === 'ketua_tim') {
            // Ketua: Lihat semua projects (dibuat admin, untuk tim) & semua tasks
            $totalProjects = Project::count();  // Semua projects
            $completedProjects = Project::where('status', 'Completed')->count();
            $totalTasks = Task::count();  // Semua tasks
            $completedTasks = Task::where('status', 'Completed')->count();
            $recentProjects = Project::with(['creator', 'tasks'])->latest()->take(5)->get();  // Semua recent
            $projectQuery = Project::query();  // Semua untuk statuses
        } else {
            // Anggota: Projects/tasks assigned to them
            $totalProjects = Project::whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->count();
            $completedProjects = Project::whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->where('status', 'Completed')->count();  // Fix: Projects completed yang punya own tasks
            $totalTasks = Task::where('assigned_to', $user->id)->count();
            $completedTasks = Task::where('assigned_to', $user->id)->where('status', 'Completed')->count();
            $recentProjects = Project::whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->with(['creator', 'tasks'])->latest()->take(5)->get();
            $projectQuery = Project::whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });  // Filtered untuk statuses
        }

        // Project statuses untuk chart (role-based)
        $projectStatuses = $projectQuery->selectRaw('status, count(*) as count')
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
