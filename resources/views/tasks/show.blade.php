@extends('layouts.app')

@section('title', $task->name)

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Detail Task Card -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">{{ $task->name }}</h3>
                <div class="card-tools">
                    @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim', 'anggota']))
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning" title="Edit Task">
                            <i class="fas fa-edit"></i>
                        </a>
                    @endif
                    @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline ml-2" onsubmit="return confirm('Yakin hapus task ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    @endif
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-secondary ml-2">Kembali</a>
                    <a href="{{ route('projects.show', $task->project) }}" class="btn btn-sm btn-primary ml-2">Lihat Project</a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID:</dt>
                    <dd class="col-sm-9">{{ $task->id }}</dd>

                    <dt class="col-sm-3">Nama:</dt>
                    <dd class="col-sm-9"><strong>{{ $task->name }}</strong></dd>

                    <dt class="col-sm-3">Deskripsi:</dt>
                    <dd class="col-sm-9">{{ $task->description ?? 'Tidak ada deskripsi' }}</dd>

                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9">
                        <span class="badge badge-{{ $task->status === 'Completed' ? 'success' : ($task->status === 'In Progress' ? 'warning' : 'info') }}">
                            {{ $task->status }}
                        </span>
                    </dd>

                    <dt class="col-sm-3">Due Date:</dt>
                    <dd class="col-sm-9">{{ $task->due_date ? $task->due_date->format('d M Y') : 'Tidak ada' }}</dd>

                    <dt class="col-sm-3">Project:</dt>
                    <dd class="col-sm-9"><a href="{{ route('projects.show', $task->project) }}">{{ $task->project->name ?? 'Unknown' }}</a></dd>

                    <dt class="col-sm-3">Ditugaskan Kepada:</dt>
                    <dd class="col-sm-9">{{ $task->assignee->name ?? 'Tidak ditugaskan' }}</dd>

                    <dt class="col-sm-3">Dibuat Pada:</dt>
                    <dd class="col-sm-9">{{ $task->created_at->format('d M Y H:i') }}</dd>

                    <dt class="col-sm-3">Diupdate Pada:</dt>
                    <dd class="col-sm-9">{{ $task->updated_at->format('d M Y H:i') }}</dd>
                </dl>
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