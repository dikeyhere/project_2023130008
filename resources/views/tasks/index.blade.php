@extends('layouts.app')

@section('title', 'Daftar Tasks')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Daftar Tasks</h3>
                    <div class="card-tools">
                        @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                            <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-success" title="Tambah Task Baru">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search & Filter Form -->
                    <form method="GET" action="{{ route('tasks.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Cari task berdasarkan nama..." value="{{ $search ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit"><i
                                                class="fas fa-search"></i></button>
                                        @if ($search)
                                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary"><i
                                                    class="fas fa-times"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']) && !empty($projects))
                                <div class="col-md-4">
                                    <select name="project_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Projects</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}"
                                                {{ $projectId == $project->id ? 'selected' : '' }}>{{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-block">Reset Filter</a>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    @if ($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Task</th>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Ditugaskan Kepada</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tasks as $task)
                                        <tr>
                                            <td>{{ $task->id }}</td>
                                            <td><strong>{{ $task->name }}</strong></td>
                                            <td>{{ $task->project->name ?? 'Unknown' }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $task->status === 'Completed' ? 'success' : ($task->status === 'In Progress' ? 'warning' : 'info') }}">
                                                    {{ $task->status }}
                                                </span>
                                            </td>
                                            <td>{{ $task->due_date ? $task->due_date->format('d M Y') : 'Tidak ada' }}</td>
                                            <td>{{ $task->assignee->name ?? 'Tidak ditugaskan' }}</td>
                                            <td>
                                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-info"><i
                                                        class="fas fa-eye"></i></a>
                                                @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim', 'anggota']))
                                                    <a href="{{ route('tasks.edit', $task) }}"
                                                        class="btn btn-sm btn-warning ml-1"><i class="fas fa-edit"></i></a>
                                                @endif
                                                @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                                        class="d-inline ml-1"
                                                        onsubmit="return confirm('Yakin hapus task ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $tasks->appends(['search' => $search, 'project_id' => $projectId])->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">Belum ada tasks yang sesuai kriteria. @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                                <a href="{{ route('tasks.create') }}">Buat sekarang!</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
@endsection
