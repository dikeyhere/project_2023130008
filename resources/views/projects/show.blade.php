@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card card-info">
                <div class="card-header" style="vertical-align:middle; text-align:center">
                    <h3 class="card-title pt-1">Detail Proyek</h3>
                    <div class="card-tools">
                        @if ($userRole === 'admin')
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-warning"><i
                                    class="fas fa-edit"></i> Edit Proyek</a>
                            <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline ml-2"
                                onsubmit="return confirm('Yakin hapus?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        @endif
                        <a href="{{ in_array(auth()->user()->role ?? '', ['admin', 'ketua_tim']) ? route('projects.index') : route('dashboard') }}"
                            class="btn btn-sm btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        @if (in_array($userRole, ['admin', 'ketua_tim']))
                            <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-sm btn-success ml-1"
                                title="Tambah Task">
                                <i class="fas fa-plus"></i> Tambah Tugas
                            </a>
                        @endif

                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        {{-- <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ $project->id }}</dd> --}}

                        {{-- <dt class="col-sm-3">Nama:</dt>
                        <dd class="col-sm-9"><strong>{{ $project->name }}</strong></dd> --}}

                        <dt class="col-sm-3">Deskripsi:</dt>
                        <dd class="col-sm-9">{{ $project->description ?? 'Tidak ada deskripsi' }}</dd>

                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            <span
                                class="badge badge-{{ $project->status === 'Completed' ? 'success' : ($project->status === 'In Progress' ? 'warning' : ($project->status === 'Planning' ? 'info' : 'secondary')) }}">
                                {{ $project->status }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Deadline:</dt>
                        <dd class="col-sm-9">
                            {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}</dd>

                        <dt class="col-sm-3">Ketua Tim:</dt>
                        <dd class="col-sm-9">{{ $project->teamLeader->name ?? 'Belum ditentukan' }}</dd>

                        <dt class="col-sm-3">Prioritas:</dt>
                        <dd class="col-sm-9">
                            @php
                                $priorityClass = match ($project->priority) {
                                    'high' => 'danger',
                                    'medium' => 'warning',
                                    'low' => 'success',
                                    default => 'secondary',
                                };
                            @endphp
                            <div>
                                <span class="badge badge-{{ $priorityClass }} mr-2">
                                    {{ $project->priority ? strtoupper($project->priority) : 'NONE' }}
                                </span>
                            </div>
                        </dd>

                        <dt class="col-sm-3">Dibuat Pada:</dt>
                        <dd class="col-sm-9">{{ $project->created_at->format('d M Y') }}</dd>

                        <dt class="col-sm-3">Diupdate Pada:</dt>
                        <dd class="col-sm-9">{{ $project->updated_at->format('d M Y') }}</dd>
                    </dl>
                </div>
            </div>

            @if ($project->tasks->count() > 0)
                <div class="table-responsive rounded">
                    <table class="table table-bordered table-striped">
                        <thead class="thead" style="background-color:cornflowerblue; text-align:center">
                            <tr>
                                <th>Nama Tugas</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th>Ditugaskan Kepada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($project->tasks as $task)
                                <tr>
                                    <td>
                                        <a
                                            href="{{ route('projects.tasks.show', ['project' => $task->project_id, 'task' => $task->id]) }}">
                                            <i class="fas fa-tasks mr-1"></i> {{ $task->name }}
                                        </a>
                                    </td>
                                    <td style="text-align:center; vertical-align:middle">
                                        <span
                                            class="badge badge-{{ $task->status === 'Completed' ? 'success' : ($task->status === 'In Progress' ? 'warning' : 'info') }}">
                                            {{ $task->status }}
                                        </span>
                                    </td>
                                    <td style="text-align:center; vertical-align:middle">
                                        {{ $task->due_date ? $task->due_date->format('d M Y') : 'Tidak ada' }}</td>
                                    <td style="text-align:center; vertical-align:middle">
                                        {{ $task->assignee->name ?? 'Tidak ditugaskan' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">Belum ada tugas untuk proyek ini.</div>
            @endif
        </div>
    </div>
@endsection
