@extends('layouts.app')

@section('title', 'Tambah Tugas')

@section('content')
    <style>
        html,
        body {
            height: 100%;
        }

        .full-page-container {
            min-height: 100vh;
            padding-right: 40px;
            padding-bottom: 30px;
            padding-left: 40px;
        }

        .full-card {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        textarea {
            resize: vertical;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: .25rem;
        }
    </style>

    <div class="full-page-container">
        <div class="full-card">
            <div class="card card-primary card-outline shadow mb-2">
                <form method="POST" action="{{ route('projects.tasks.store', ['project' => $project->id]) }}">
                    @csrf
                    <div class="card-body">

                        <div class="form-group">
                            <label for="name">Nama Tugas <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-tasks"></i></span>
                                </div>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    required placeholder="Masukkan nama task">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                rows="4" placeholder="Deskripsi task (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="due_date">Deadline Tugas <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" id="due_date"
                                class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}"
                                min="{{ date('Y-m-d') }}" required>
                            <small>Deadline Proyek:
                                {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}</small>
                            <small id="deadline-warning" class="form-text text-danger d-none">
                                Deadline tugas tidak boleh melebihi deadline proyek.
                            </small>
                            @error('due_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror"
                                required>
                                <option value="">Pilih Status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="priority">Prioritas <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                </div>
                                <select name="priority" id="priority"
                                    class="form-control @error('priority') is-invalid @enderror" required>
                                    <option value="" disabled selected>Pilih Prioritas</option>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority }}"
                                            {{ old('priority') == $priority ? 'selected' : '' }}>
                                            {{ ucfirst($priority) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="project_name">Project</label>
                            <input type="text" class="form-control" value="{{ $project->name }}" readonly>
                        </div>

                        @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']) && !empty($users))
                            <div class="form-group">
                                <label for="assigned_to">Ditugaskan Kepada</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <select name="assigned_to" id="assigned_to" class="form-control">
                                        <option value="" disabled selected>Tidak Ditugaskan</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ isset($task) && $task->assigned_to == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ ucwords(str_replace('_', ' ', $user->role)) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer d-flex justify-content-center flex-wrap">
                        <a href="{{ route('projects.tasks.index', $project) }}"
                            class="btn btn-secondary mr-2 mb-1">Batal</a>
                        <button type="submit" class="btn btn-primary mb-1">Simpan Tugas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const projectSelect = document.getElementById('project_id');
            const dueDateInput = document.getElementById('due_date');
            const warning = document.getElementById('deadline-warning');

            projectSelect.addEventListener('change', function() {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                const projectDeadline = selectedOption.getAttribute('data-deadline');

                if (projectDeadline) {
                    dueDateInput.max = projectDeadline;
                } else {
                    dueDateInput.removeAttribute('max');
                }

                dueDateInput.value = '';
                warning.classList.add('d-none');
            });

            dueDateInput.addEventListener('change', function() {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                const projectDeadline = selectedOption.getAttribute('data-deadline');

                if (projectDeadline && dueDateInput.value > projectDeadline) {
                    warning.classList.remove('d-none');
                    dueDateInput.value = projectDeadline;
                } else {
                    warning.classList.add('d-none');
                }
            });
        });
    </script>
@endsection
