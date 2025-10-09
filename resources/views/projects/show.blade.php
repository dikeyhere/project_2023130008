@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Detail Project Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">{{ $project->name }}</h3>
                    {{-- <div class="card-tools">
                    @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-warning" title="Edit Proyek">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline ml-2" onsubmit="return confirm('Yakin hapus proyek ini? Tasks terkait akan ikut terhapus.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    @endif
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-secondary ml-2">Kembali</a>
                </div> --}}
                    <div class="card-tools">
                        @if (in_array($userRole, ['admin', 'ketua_tim']))
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-warning"><i
                                    class="fas fa-edit"></i></a>
                        @endif
                        @if ($userRole === 'admin')
                            <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline ml-2"
                                onsubmit="return confirm('Yakin hapus?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        @endif
                        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-secondary ml-2">Kembali</a>
                        @if (in_array($userRole, ['admin', 'ketua_tim']))
                            <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}"
                                class="btn btn-sm btn-primary ml-2">Kelola Tasks</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        {{-- <dt class="col-sm-3">ID:</dt>
                    <dd class="col-sm-9">{{ $project->id }}</dd> --}}

                        <dt class="col-sm-3">Nama:</dt>
                        <dd class="col-sm-9"><strong>{{ $project->name }}</strong></dd>

                        <dt class="col-sm-3">Deskripsi:</dt>
                        <dd class="col-sm-9">{{ $project->description ?? 'Tidak ada deskripsi' }}</dd>

                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            <span
                                class="badge badge-{{ $project->status === 'Completed' ? 'success' : ($project->status === 'In Progress' ? 'warning' : ($project->status === 'Planning' ? 'info' : 'secondary')) }}">
                                {{ $project->status }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Dibuat Oleh:</dt>
                        <dd class="col-sm-9">{{ $project->creator->name ?? 'Unknown' }}</dd>

                        <dt class="col-sm-3">Dibuat Pada:</dt>
                        <dd class="col-sm-9">{{ $project->created_at->format('d M Y H:i') }}</dd>

                        <dt class="col-sm-3">Diupdate Pada:</dt>
                        <dd class="col-sm-9">{{ $project->updated_at->format('d M Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Tasks Terkait (List sederhana) -->
            <div class="card card-secondary mt-4">
                <div class="card-header">
                    <h3 class="card-title">Tasks Terkait ({{ $project->tasks->count() }})</h3>
                </div>
                <div class="card-body">
                    @if ($project->tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Nama Task</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Ditugaskan Kepada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($project->tasks as $task)
                                        <tr>
                                            <td>{{ $task->name }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $task->status === 'Completed' ? 'success' : ($task->status === 'In Progress' ? 'warning' : 'info') }}">
                                                    {{ $task->status }}
                                                </span>
                                            </td>
                                            <td>{{ $task->due_date ? $task->due_date->format('d M Y') : 'Tidak ada' }}</td>
                                            <td>{{ $task->assignee->name ?? 'Tidak ditugaskan' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">Belum ada tasks untuk proyek ini.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages (Global, tapi jarang di show) -->
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
