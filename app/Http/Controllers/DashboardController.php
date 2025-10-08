<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Dummy data untuk dashboard (projects, tasks, users stats)
        $dummyProjects = [
            ['id' => 1, 'name' => 'Proyek Web Development', 'status' => 'In Progress'],
            ['id' => 2, 'name' => 'Proyek Mobile App', 'status' => 'Completed'],
            ['id' => 3, 'name' => 'Proyek Database Optimization', 'status' => 'Planning'],
            ['id' => 4, 'name' => 'Proyek AI Integration', 'status' => 'On Hold'],
        ];

        $dummyTasks = [
            ['id' => 1, 'title' => 'Design UI', 'status' => 'Completed'],
            ['id' => 2, 'title' => 'Backend API', 'status' => 'In Progress'],
            ['id' => 3, 'title' => 'Testing', 'status' => 'Planning'],
        ];

        // Stats summary (dummy)
        $totalProjects = count($dummyProjects);
        $completedProjects = collect($dummyProjects)->where('status', 'Completed')->count();
        $totalTasks = count($dummyTasks);
        $completedTasks = collect($dummyTasks)->where('status', 'Completed')->count();
        $userRole = Auth::user()->role;

        // Data untuk Chart.js (e.g., pie chart projects status)
        $projectStatuses = collect($dummyProjects)->groupBy('status')->map->count()->toArray();

        // Session untuk welcome alert: Hanya tampil sekali per login session
        $showWelcome = !session()->has('welcome_shown');  // True jika belum shown
        if ($showWelcome) {
            session(['welcome_shown' => true]);  // Set flag agar tidak muncul lagi di session ini
        }

        // Pass title untuk header
        return view('dashboard', compact(
            'dummyProjects',
            'dummyTasks',
            'totalProjects',
            'completedProjects',
            'totalTasks',
            'completedTasks',
            'userRole',
            'projectStatuses',
            'showWelcome'  // Tambah ini: Pass flag ke view
        ))->with('title', 'Dashboard');
    }
}
