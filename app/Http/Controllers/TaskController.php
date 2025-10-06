<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaskUploadRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware(function ($request, $next) {
    //         if (in_array(Auth::user()->role, ['admin'])) {
    //             // Admin full access
    //         } elseif (Auth::user()->role === 'ketua_tim') {
    //             if (in_array($request->route()->getName(), ['tasks.create', 'tasks.store', 'tasks.edit', 'tasks.update', 'tasks.destroy'])) {
    //                 abort(403);  // Ketua tidak bisa create/edit/destroy
    //             }
    //         } elseif (Auth::user()->role === 'anggota_tim') {
    //             if (in_array($request->route()->getName(), ['tasks.create', 'tasks.store', 'tasks.edit', 'tasks.update', 'tasks.destroy'])) {
    //                 abort(403);  // Anggota hanya read/upload
    //             }
    //         }
    //         return $next($request);
    //     });
    // }

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $routeName = $request->route()->getName();

            if ($user->role === 'admin') {
                return $next($request);  // Admin full access
            } elseif ($user->role === 'ketua_tim') {
                // Ketua: Hanya read + upload (bukan create/edit/destroy)
                if (in_array($routeName, ['tasks.create', 'tasks.store', 'tasks.edit', 'tasks.update', 'tasks.destroy'])) {
                    abort(403);
                }
            } elseif ($user->role === 'anggota_tim') {
                // Anggota: Hanya read + upload di detail (bukan CRUD)
                if (in_array($routeName, ['tasks.create', 'tasks.store', 'tasks.edit', 'tasks.update', 'tasks.destroy'])) {
                    abort(403);
                }
            }
            return $next($request);
        });
    }


    public function index()
    {
        $user = Auth::user();
        $dummyTasks = [];
        if ($user->role === 'admin' || $user->role === 'ketua_tim') {
            $dummyTasks = [
                [
                    'id' => 1,
                    'title' => 'Tugas 1: Desain UI',
                    'description' => 'Buat mockup tampilan dashboard.',
                    'deadline' => '2024-01-15',
                    'status' => 'In Progress',
                    'priority' => 'High',
                    'subtasks' => ['Sub-tugas 1: Sketch', 'Sub-tugas 2: Review'],
                    'files' => ['desain_ui.pdf', 'mockup.jpg']
                ],
                [
                    'id' => 2,
                    'title' => 'Tugas 2: Testing',
                    'description' => 'Test fitur upload file.',
                    'deadline' => '2024-01-20',
                    'status' => 'Pending',
                    'priority' => 'Urgent',
                    'subtasks' => ['Sub-tugas 1: Unit test'],
                    'files' => ['test_report.pdf']
                ],
            ];
        } else {
            $dummyTasks = [
                [
                    'id' => 3,
                    'title' => 'Tugas Pribadi: Update Dokumen',
                    'description' => 'Update deskripsi tugas Anda.',
                    'deadline' => '2024-01-10',
                    'status' => 'Assigned',
                    'priority' => 'Medium',
                    'subtasks' => [],
                    'files' => []
                ],
            ];
        }
        return view('tasks.index', compact('dummyTasks'));
    }

    public function create()
    {
        $dummyTask = ['title' => '', 'description' => '', 'deadline' => '', 'priority' => ''];  // Empty dummy untuk form
        return view('tasks.create', compact('dummyTask'));
    }

    public function show($id)
    {
        $dummyTask = [
            'id' => $id,
            'title' => 'Detail Tugas ' . $id,
            'description' => 'Deskripsi lengkap tugas ini... Tenggat waktu: 2024-01-15. Status: In Progress. Prioritas: High.',
            'deadline' => '2024-01-15',
            'status' => 'In Progress',
            'priority' => 'High',
            'subtasks' => ['Sub1 dummy', 'Sub2 dummy'],
            'files' => ['file1.pdf', 'file2.png']
        ];
        return view('tasks.show', compact('dummyTask'));
    }

    public function edit($id)
    {
        $dummyTask = [
            'id' => $id,
            'title' => 'Edit Tugas ' . $id,
            'description' => 'Deskripsi lama...',
            'deadline' => '2024-01-15',
            'priority' => 'High'
        ];  // Dummy untuk form
        return view('tasks.edit', compact('dummyTask'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'priority' => 'required|in:Low,Medium,High,Urgent',
        ]);
        return redirect()->route('tasks.index')->with('success', 'Dummy: Tugas dibuat!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['title' => 'required']);
        return redirect()->route('tasks.show', $id)->with('success', 'Dummy: Tugas diupdate!');
    }

    public function destroy($id)
    {
        return redirect()->route('tasks.index')->with('success', 'Dummy: Tugas dihapus!');
    }

    public function upload(TaskUploadRequest $request, $taskId)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads', 'public');
            $subtask = $request->input('subtask', 'No sub-tugas');
            return redirect()->route('tasks.show', $taskId)->with('success', 'File diupload: ' . $file->getClientOriginalName() . '. Sub-tugas: ' . $subtask);
        }
        return redirect()->back()->withErrors(['file' => 'Upload gagal.']);
    }
}
