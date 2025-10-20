@extends('layouts.app')

@section('title', $task->name)

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title pt-1">Detail Tugas</h3>
                    <div class="card-tools">
                        @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim', 'anggota']))
                            <a href="{{ route('projects.tasks.edit', ['project' => $task->project_id, 'task' => $task->id]) }}"
                                class="btn btn-sm btn-warning" title="Edit Task">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif
                        @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                            <form method="POST"
                                action="{{ route('projects.tasks.destroy', ['project' => $task->project_id, 'task' => $task->id]) }}"
                                class="d-inline ml-2" onsubmit="return confirm('Yakin hapus tugas ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        @endif
                        <a href="{{ route('projects.show', $task->project) }}" class="btn btn-sm btn-secondary ml-2"><i
                                class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        {{-- <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ $task->id }}</dd> --}}

                        {{-- <dt class="col-sm-3">Nama:</dt>
                        <dd class="col-sm-9"><strong>{{ $task->name }}</strong></dd> --}}

                        <dt class="col-sm-3">Deskripsi:</dt>
                        <dd class="col-sm-9">{{ $task->description ?? 'Tidak ada deskripsi' }}</dd>

                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            <span
                                class="badge badge-{{ $task->status === 'Completed' ? 'success' : ($task->status === 'In Progress' ? 'warning' : 'info') }}">
                                {{ $task->status }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Deadline Tugas:</dt>
                        <dd class="col-sm-9">{{ $task->due_date ? $task->due_date->format('d M Y') : 'Tidak ada' }}</dd>

                        <dt class="col-sm-3">Ditugaskan Kepada:</dt>
                        <dd class="col-sm-9">{{ $task->assignee->name ?? 'Tidak ditugaskan' }}</dd>

                        <dt class="col-sm-3">Prioritas:</dt>
                        <dd class="col-sm-9">
                            @php
                                $priorityColor = match (strtolower($task->priority)) {
                                    'high' => 'danger',
                                    'medium' => 'warning',
                                    'low' => 'success',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge badge-{{ $priorityColor }}">
                                {{ strtoupper($task->priority) }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Project:</dt>
                        <dd class="col-sm-9"><a
                                href="{{ route('projects.show', $task->project) }}">{{ $task->project->name ?? 'Unknown' }}</a>
                        </dd>

                        {{-- <dt class="col-sm-3">Deadline Proyek:</dt>
                        <dd class="col-sm-9">
                            {{ $task->project->deadline ? \Carbon\Carbon::parse($task->project->deadline)->format('d M Y') : 'Belum ditentukan' }}
                        </dd> --}}

                        <dt class="col-sm-3">Dibuat Pada:</dt>
                        <dd class="col-sm-9">{{ $task->created_at->format('d M Y') }}</dd>

                        <dt class="col-sm-3">Diupdate Pada:</dt>
                        <dd class="col-sm-9">{{ $task->updated_at->format('d M Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @if (in_array(Auth::user()->role, ['ketua_tim', 'anggota_tim']) &&
            Auth::id() === $task->assigned_to &&
            $task->status !== 'Completed')
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h3 class="card-title pt-1"><i class="fas fa-upload mr-2"></i>Lampiran</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('projects.tasks.upload', [$project, $task]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="submission_file"><i class="fas fa-file"></i> Pilih File</label>
                        <input type="file" name="submission_file" id="submission_file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success mt-2">
                        <i class="fas fa-paper-plane"></i> Kirim Hasil
                    </button>
                </form>
            </div>
        </div>
    @endif

    @if ($task->submission_file)
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <i class="fas fa-file-alt mr-1"></i> Hasil Pekerjaan
            </div>
            <div class="card-body">
                <p>
                    <strong>File:</strong>
                    <a href="{{ asset('storage/' . $task->submission_file) }}" target="_blank" class="text-info">
                        Lihat Lampiran
                    </a>
                </p>
                <p class="mb-0 text-muted">
                    <small>Diupload pada {{ \Carbon\Carbon::parse($task->completed_at)->format('d M Y H:i') }}</small>
                </p>
            </div>
        </div>
    @endif
@endsection
