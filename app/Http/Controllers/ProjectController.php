<?php

namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class ProjectController extends Controller
    {
        /**
         * Display a listing of the resource.
         */
        public function index()
        {
            // Dummy data: Gunakan $projects (bukan $dummyProjects)
            $projects = [
                ['id' => 1, 'name' => 'Proyek Web Development', 'description' => 'Bangun website perusahaan', 'status' => 'In Progress', 'created_at' => now()],
                ['id' => 2, 'name' => 'Proyek Mobile App', 'description' => 'Aplikasi Android/iOS', 'status' => 'Completed', 'created_at' => now()->subDays(5)],
                ['id' => 3, 'name' => 'Proyek Database Optimization', 'description' => 'Optimasi performa DB', 'status' => 'Planning', 'created_at' => now()],
            ];

            return view('projects.index', compact('projects'));  // Pass $projects ke view
        }

        /**
         * Show the form for creating a new resource.
         */
        public function create()
        {
            // Role check: Block anggota (meski route di read-group, controller handle)
            if (Auth::user()->role === 'anggota_tim') {
                return redirect()->route('projects.index')->with('error', 'Hanya admin/ketua tim yang bisa membuat proyek.');
            }
            return view('projects.create');
        }

        /**
         * Store a newly created resource in storage.
         */
        public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:Planning,In Progress,Completed,On Hold',
            ]);

            // Real: Project::create($request->all() + ['user_id' => Auth::id()]);
            // Dummy: Simpan ke session atau log
            session()->flash('status', 'Proyek "' . $request->name . '" berhasil dibuat! (Dummy)');

            return redirect()->route('projects.index');
        }

        /**
         * Display the specified resource.
         */
        public function show($id)  // Dummy ID, bukan model binding jika model belum ada
        {
            // Dummy project berdasarkan ID
            $project = collect([
                ['id' => 1, 'name' => 'Proyek Web Development', 'description' => 'Bangun website perusahaan', 'status' => 'In Progress'],
                ['id' => 2, 'name' => 'Proyek Mobile App', 'description' => 'Aplikasi Android/iOS', 'status' => 'Completed'],
                ['id' => 3, 'name' => 'Proyek Database Optimization', 'description' => 'Optimasi performa DB', 'status' => 'Planning'],
            ])->firstWhere('id', $id);

            if (!$project) {
                abort(404, 'Proyek tidak ditemukan.');
            }

            return view('projects.show', compact('project'));
        }

        /**
         * Show the form for editing the specified resource.
         */
        public function edit($id)
        {
            $project = collect([
                ['id' => 1, 'name' => 'Proyek Web Development', 'description' => 'Bangun website perusahaan', 'status' => 'In Progress'],
                ['id' => 2, 'name' => 'Proyek Mobile App', 'description' => 'Aplikasi Android/iOS', 'status' => 'Completed'],
            ])->firstWhere('id', $id);

            if (!$project) {
                abort(404);
            }

            return view('projects.edit', compact('project'));
        }

        /**
         * Update the specified resource in storage.
         */
        public function update(Request $request, $id)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            // Dummy update
            session()->flash('status', 'Proyek ID ' . $id . ' berhasil diupdate! (Dummy)');

            return redirect()->route('projects.index');
        }

        /**
         * Remove the specified resource from storage.
         */
        public function destroy($id)
        {
            // Dummy delete
            session()->flash('status', 'Proyek ID ' . $id . ' berhasil dihapus! (Dummy)');

            return redirect()->route('projects.index');
        }
    }
