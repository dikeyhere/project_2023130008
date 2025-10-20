<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Project
    // Semua role bisa melihat daftar project
    Route::middleware('role:admin,ketua_tim,anggota_tim')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    });

    // Hanya admin bisa tambah project
    Route::middleware('role:admin')->group(function () {
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    });


    // CRUD hanya admin & ketua
    Route::middleware('role:admin,ketua_tim')->group(function () {
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    });

    // Task berdasarkan project
    Route::prefix('projects/{project}')->group(function () {
        // Hanya admin bisa membuat project
        Route::middleware('role:admin,ketua_tim')->group(function () {
            Route::get('/tasks/create', [TaskController::class, 'create'])->name('projects.tasks.create');
            Route::post('/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
        });

        // Semua role bisa lihat task
        Route::middleware('role:admin,ketua_tim,anggota_tim')->group(function () {
            Route::get('/tasks', [TaskController::class, 'index'])->name('projects.tasks.index');
            Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('projects.tasks.show');
        });

        // Hanya admin & ketua tim bisa CRUD task
        Route::middleware('role:admin,ketua_tim')->group(function () {
            Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('projects.tasks.edit');
            Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('projects.tasks.update');
            Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('projects.tasks.destroy');
        });

        // Anggota Tim bisa upload dan update progress
        Route::middleware('role:anggota_tim')->group(function () {
            Route::post('/tasks/{task}/upload', [TaskController::class, 'upload'])->name('projects.tasks.upload');
            Route::put('/tasks/{task}/progress', [TaskController::class, 'updateProgress'])->name('projects.tasks.updateProgress');
            Route::post('/tasks/{task}/submit', [TaskController::class, 'submit'])->name('projects.tasks.submit');
        });
    });

    // Profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin,ketua_tim,anggota_tim'])->prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'globalIndex'])->name('tasks.index');
    Route::get('/{task}', [TaskController::class, 'show'])->name('tasks.show');
});

Route::get('/', function () {
    return view('welcome');
});

Route::post('/toggle-darkmode', function () {
    session(['dark_mode' => !session('dark_mode', false)]);
    return response()->json(['status' => 'success']);
})->name('toggle.darkmode');

Route::middleware('auth')->get('/projects/{project}', function (Project $project) {
    Log::info('Hit projects.show', ['project_id' => $project->id, 'user_id' => auth()->id()]);
    return app(\App\Http\Controllers\ProjectController::class)->show($project);
})->name('projects.show');
