  <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\ProjectController;
    use App\Http\Controllers\TaskController;
    use App\Http\Controllers\ProfileController;

    require __DIR__ . '/auth.php';

    Route::middleware('auth')->group(function () {
        // Route::get('/dashboard', function () {
        //     return view('dashboard');
        // })->name('dashboard');
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        // ========== PROJECTS ROUTES ==========
        // Read-Only: Semua role bisa index/show (longgar middleware)
        Route::middleware('role:admin,ketua_tim,anggota_tim')->group(function () {
            Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
            Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');  // Tapi anggota akan 403 di controller jika perlu
            Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        });

        // Write-Only: Hanya admin & ketua (ketat middleware)
        Route::middleware('role:admin,ketua_tim')->group(function () {
            Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
            Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
            Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
            Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        });

        // ========== TASKS ROUTES ==========
        // Read-Only: Semua role bisa index/show
        Route::middleware('role:admin,ketua_tim,anggota_tim')->group(function () {
            Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
            Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
        });

        // Full CRUD: Hanya admin
        Route::middleware('role:admin')->group(function () {
            Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
            Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
            Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
            Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
            Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
        });

        // Ketua Tim: Read + Assign/Update (misal, limited write)
        Route::middleware('role:ketua_tim')->group(function () {
            Route::get('/tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');  // Custom jika perlu
            Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
        });

        // Anggota Tim: Read + Upload (khusus)
        Route::middleware('role:anggota_tim')->group(function () {
            Route::post('/tasks/{task}/upload', [TaskController::class, 'upload'])->name('tasks.upload');
            // Opsional: Update own tasks
            Route::put('/tasks/{task}/progress', [TaskController::class, 'updateProgress'])->name('tasks.updateProgress');
        });

        // Profile (Semua role)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Guest routes
    Route::get('/', function () {
        return view('welcome');
    });
