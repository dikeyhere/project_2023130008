<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Log;
use App\Models\Project;
use App\Http\Controllers\PermissionManagementController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinancialController;

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:view dashboard');

    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index')
        ->middleware('permission:view reports');

    Route::get('/reports/export', [ReportController::class, 'exportPdf'])
        ->name('reports.export')
        ->middleware('permission:export reports');

    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])
        ->name('reports.export.excel')
        ->middleware('permission:export reports');

    Route::get('/projects', [ProjectController::class, 'index'])
        ->name('projects.index')
        ->middleware('permission:view projects');

    Route::get('/projects/create', [ProjectController::class, 'create'])
        ->name('projects.create')
        ->middleware('permission:create projects');

    Route::post('/projects', [ProjectController::class, 'store'])
        ->name('projects.store')
        ->middleware('permission:create projects');

    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
        ->name('projects.edit')
        ->middleware('permission:edit projects');

    Route::put('/projects/{project}', [ProjectController::class, 'update'])
        ->name('projects.update')
        ->middleware('permission:edit projects');

    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
        ->name('projects.destroy')
        ->middleware('permission:delete projects');

    Route::prefix('projects/{project}')->group(function () {

        Route::get('/tasks', [TaskController::class, 'index'])
            ->name('projects.tasks.index')
            ->middleware('permission:view tasks');

        Route::get('/tasks/{task}', [TaskController::class, 'show'])
            ->name('projects.tasks.show')
            ->middleware('permission:view tasks');

        Route::get('/tasks/create', [TaskController::class, 'create'])
            ->name('projects.tasks.create')
            ->middleware('permission:create tasks');

        Route::post('/tasks', [TaskController::class, 'store'])
            ->name('projects.tasks.store')
            ->middleware('permission:create tasks');

        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])
            ->name('projects.tasks.edit')
            ->middleware('permission:edit tasks');

        Route::put('/tasks/{task}', [TaskController::class, 'update'])
            ->name('projects.tasks.update')
            ->middleware('permission:edit tasks');

        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
            ->name('projects.tasks.destroy')
            ->middleware('permission:delete tasks');

        Route::post('/tasks/{task}/upload', [TaskController::class, 'upload'])
            ->name('projects.tasks.upload')
            ->middleware('permission:upload task file');

        Route::put('/tasks/{task}/progress', [TaskController::class, 'updateProgress'])
            ->name('projects.tasks.updateProgress')
            ->middleware('permission:update task progress');

        Route::post('/tasks/{task}/submit', [TaskController::class, 'submit'])
            ->name('projects.tasks.submit')
            ->middleware('permission:submit task');
    });

    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show')
        ->middleware('permission:view profile');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit')
        ->middleware('permission:edit profile');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update')
        ->middleware('permission:edit profile');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password')
        ->middleware('permission:edit profile');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy')
        ->middleware('permission:delete profile');

    Route::get('/financial', [FinancialController::class, 'index'])
        ->name('financial.index')
        ->middleware('permission:view financial');

    Route::post('/financial/expense', [ExpenseController::class, 'store'])
        ->name('financial.expense.store')
        ->middleware('permission:submit expense');

    Route::put('/financial/expense/{expense}/approve', [ExpenseController::class, 'approve'])
        ->name('financial.expense.approve')
        ->middleware('permission:approve expense');

    Route::put('/financial/expense/{expense}/reject', [ExpenseController::class, 'reject'])
        ->name('financial.expense.reject')
        ->middleware('permission:reject expense');

    Route::get('/access/permission', [PermissionManagementController::class, 'index'])
        ->name('access.permission')
        ->middleware('permission:manage permissions');

    Route::post('/access/permission/update', [PermissionManagementController::class, 'update'])
        ->name('access.permission.update')
        ->middleware('permission:manage permissions');
});

Route::middleware(['auth', 'permission:view tasks'])->prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'globalIndex'])->name('tasks.index');
    Route::get('/{task}', [TaskController::class, 'show'])->name('tasks.show');
});

Route::middleware(['auth', 'permission:view project detail'])
    ->get('/projects/{project}', function (Project $project) {
        return app(\App\Http\Controllers\ProjectController::class)->show($project);
    })
    ->name('projects.show');

Route::get('/', function () {
    return view('welcome');
});

Route::post('/toggle-darkmode', function () {
    session(['dark_mode' => !session('dark_mode', false)]);
    return response()->json(['status' => 'success']);
})->name('toggle.darkmode');

Route::get('refresh-captcha', function () {
    return response()->json(['captcha' => captcha_img('default')]);
})->name('refreshCaptcha');
