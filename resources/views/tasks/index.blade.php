@extends('layouts.app')

@section('title', 'Daftar Tugas')

@section('content')
    <div class="row">
        <div class="col-12">

            <form method="GET" action="{{ route('tasks.index') }}" class="mb-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between">

                    <div class="d-flex flex-wrap align-items-center" style="flex: 1;">

                        <div class="input-group mr-2 mb-2" style="min-width: 350px; flex: 1;">
                            <input type="text" name="search" class="form-control rounded"
                                placeholder="Cari tugas berdasarkan nama..." value="{{ $search ?? '' }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if ($search)
                                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']) && !empty($projects))
                            <div class="mb-2">
                                <select name="project_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua Projects</option>
                                    @foreach ($projects as $proj)
                                        <option value="{{ $proj->id }}" {{ $projectId == $proj->id ? 'selected' : '' }}>
                                            {{ $proj->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="mb-2 ml-auto d-flex justify-content-end" style="gap: 10px;">
                        @if (isset($project))
                            <a href="{{ url()->previous() ?? route('tasks.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        @endif

                        @if (isset($project))
                            <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset Filter
                            </a>
                        @endif

                        @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                            @if (isset($project))
                                <a href="{{ route('projects.tasks.create', ['project' => $project->id]) }}"
                                    class="btn btn-success">
                                    <i class="fas fa-plus"></i> Tambah Tugas
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </form>

            @if ($tasks->count() > 0)
                <div class="table-responsive rounded">
                    <table class="table table-bordered table-striped rounded">
                        <thead class="thead-dark" style="text-align: center">
                            <tr>
                                {{-- <th>ID</th> --}}
                                <th>Nama Tugas</th>
                                @if (in_array($userRole ?? 'anggota_tim', ['admin', 'ketua_tim']))
                                    <th>Ditugaskan Kepada</th>
                                @else
                                    <th>Prioritas</th>
                                @endif
                                <th>Project</th>
                                <th>Status</th>
                                {{-- <th>Deadline</th> --}}

                                @if (in_array($userRole ?? 'anggota_tim', ['admin', 'ketua_tim']))
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    {{-- <td>{{ $task->id }}</td> --}}
                                    <td>
                                        <strong>{{ $task->name }}</strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt"></i>
                                            Deadline:
                                            {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}
                                        </small>
                                    </td>
                                    <td style="text-align:center; vertical-align:middle">
                                        @if (in_array($userRole ?? 'anggota_tim', ['admin', 'ketua_tim']))
                                            {{ $task->assignee->name ?? 'Tidak ditugaskan' }}
                                        @else
                                            @php
                                                $priorityClass = match ($task->priority) {
                                                    'high' => 'badge-danger',
                                                    'medium' => 'badge-warning',
                                                    'low' => 'badge-success',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp

                                            <span class="badge {{ $priorityClass }} text-uppercase">
                                                {{ $task->priority ?? 'Tidak Ditentukan' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align:center; vertical-align:middle">
                                        {{ $task->project->name ?? 'Unknown' }}</td>

                                    <td style="text-align:center; vertical-align:middle">
                                        <span
                                            class="badge badge-{{ $task->status === 'Completed' ? 'success' : ($task->status === 'In Progress' ? 'warning' : 'info') }}">
                                            {{ $task->status }}
                                        </span>
                                    </td>
                                    {{-- <td>{{ $task->due_date ? $task->due_date->format('d M Y') : 'Tidak ada' }}</td> --}}

                                    @if (in_array($userRole ?? 'anggota_tim', ['admin', 'ketua_tim']))
                                        <td style="text-align:center; vertical-align:middle">
                                            @if (in_array($userRole ?? 'anggota_tim', ['admin', 'ketua_tim']))
                                                <a href="{{ route('projects.tasks.show', ['project' => $task->project_id, 'task' => $task->id]) }}"
                                                    class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                            @endif
                                            @if (in_array($userRole ?? 'anggota_tim', ['admin', 'ketua_tim']))
                                                <a href="{{ route('projects.tasks.edit', ['project' => $task->project_id, 'task' => $task->id]) }}"
                                                    class="btn btn-sm btn-warning ml-1"><i class="fas fa-edit"></i></a>
                                            @endif
                                            @if (in_array($userRole ?? 'anggota_tim', ['admin', 'ketua_tim']))
                                                <form method="POST"
                                                    action="{{ route('projects.tasks.destroy', ['project' => $task->project_id, 'task' => $task->id]) }}"
                                                    class="d-inline ml-1"
                                                    onsubmit="return confirm('Yakin hapus task ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $tasks->appends([
                            'search' => $search ?? '',
                            'project_id' => $projectId ?? null,
                        ])->links() }}

                </div>
            @else
                <div class="alert alert-info text-center">Belum ada tugas yang sesuai kriteria. @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                        <a href="{{ route('projects.tasks.create', $project) }}">Buat sekarang!</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
