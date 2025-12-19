@extends('layouts.app')

@section('title', $task->name)

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title pt-1">Detail Tugas</h3>
                    <div class="card-tools">

                        @role('admin|ketua_tim')
                            <a href="{{ route('projects.tasks.edit', ['project' => $task->project_id, 'task' => $task->id]) }}"
                                class="btn btn-sm btn-warning" title="Edit Task">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form method="POST"
                                action="{{ route('projects.tasks.destroy', ['project' => $task->project_id, 'task' => $task->id]) }}"
                                class="d-inline ml-2" onsubmit="return confirm('Yakin hapus tugas ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        @endrole

                        <a href="{{ route('projects.show', $task->project) }}" class="btn btn-sm btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <dl class="row">
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
                                {{ strtoupper($task->priority) ?? 'Tidak Ditentukan' }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Project:</dt>
                        <dd class="col-sm-9">
                            <a href="{{ route('projects.show', $task->project) }}">
                                {{ $task->project->name ?? 'Unknown' }}
                            </a>
                        </dd>

                        <dt class="col-sm-3">Dibuat Pada:</dt>
                        <dd class="col-sm-9">{{ $task->created_at->format('d M Y') }}</dd>

                        <dt class="col-sm-3">Diupdate Pada:</dt>
                        <dd class="col-sm-9">{{ $task->updated_at->format('d M Y, H:m') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @role('ketua_tim|anggota_tim')
        @if (Auth::id() === $task->assigned_to && $task->status !== 'Completed')
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title pt-1"><i class="fas fa-upload mr-2"></i>Lampiran</h3>
                </div>

                <div class="card-body">
                    <form id="uploadForm"
                        action="{{ route('projects.tasks.upload', ['project' => $task->project, 'task' => $task]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="project_id" value="{{ $task->project_id }}">
                        <input type="hidden" name="task_id" value="{{ $task->id }}">

                        <div class="form-group">
                            <label><i class="fas fa-list"></i> Pilih Jenis Lampiran</label>
                            <select class="form-control" id="inputType" name="input_type">
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="file">File</option>
                                <option value="text">Teks</option>
                            </select>
                        </div>

                        <div class="form-group d-none" id="fileField">
                            <label for="submission_file"><i class="fas fa-file"></i> Pilih File</label>
                            <input type="file" name="submission_file" id="submission_file" class="form-control">
                        </div>

                        <div class="form-group d-none" id="textField">
                            <label for="submission_text"><i class="fas fa-keyboard"></i> Ketik Lampiran Teks</label>
                            <textarea name="submission_text" id="submission_text" class="form-control" rows="5"
                                placeholder="Tulis hasil pekerjaan Anda di sini..."></textarea>
                        </div>

                        <p>User Login ID: {{ Auth::id() }}</p>
                        <p>Task Assigned_to: {{ $task->assigned_to }}</p>
                        <p>Task ID: {{ $task->id }}</p>


                        <button type="submit" class="btn btn-success mt-3">
                            <i class="fas fa-paper-plane"></i> Kirim Hasil
                        </button>
                    </form>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const inputType = document.getElementById('inputType');
                    const fileField = document.getElementById('fileField');
                    const textField = document.getElementById('textField');

                    const fileInput = document.getElementById('submission_file');
                    const textInput = document.getElementById('submission_text');

                    let lastValue = "";

                    inputType.addEventListener('change', function(e) {
                        const currentValue = e.target.value;
                        const hasUnsavedData =
                            (fileInput.files.length > 0 || textInput.value.trim() !== '');

                        if (hasUnsavedData && lastValue !== "" && lastValue !== currentValue) {
                            const confirmChange = confirm(
                                "Data yang belum dikirim akan hilang. Lanjutkan?"
                            );
                            if (!confirmChange) {
                                inputType.value = lastValue;
                                return;
                            }

                            fileInput.value = "";
                            textInput.value = "";
                        }

                        if (currentValue === 'file') {
                            fileField.classList.remove('d-none');
                            textField.classList.add('d-none');
                        } else if (currentValue === 'text') {
                            textField.classList.remove('d-none');
                            fileField.classList.add('d-none');
                        } else {
                            fileField.classList.add('d-none');
                            textField.classList.add('d-none');
                        }

                        lastValue = currentValue;
                    });
                });
            </script>
        @endif

        @if (Auth::id() === $task->assigned_to && ($task->submission_file || $task->submission_text))
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-file-alt mr-1"></i> Hasil Pekerjaan
                </div>

                <div class="card-body">

                    @if ($task->submission_file)
                        <p>
                            <strong>File:</strong>
                            <a href="{{ asset('storage/' . $task->submission_file) }}" target="_blank" class="text-info">
                                Lihat Lampiran
                            </a>
                        </p>
                    @endif
                    
                    @if ($task->submission_text)
                        <p>
                            <strong>Teks:</strong>
                        <div class="border p-2 bg-light" style="white-space: pre-wrap;">
                            {{ $task->submission_text }}
                        </div>
                        </p>
                    @endif

                    <p class="mb-0 text-muted">
                        <small>Diupload pada {{ \Carbon\Carbon::parse($task->completed_at)->format('d M Y H:i') }}</small>
                    </p>
                </div>
            </div>
        @endif
    @endrole


@endsection
