@extends('layouts.app')

@section('title', 'Edit Tugas: ' . $task->name)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Edit Tugas</h3>
                </div>
                <form method="POST"
                    action="{{ route('projects.tasks.update', ['project' => $task->project_id, 'task' => $task->id]) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        <div class="form-group">
                            <label for="name">Nama Tugas <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-tasks"></i></span>
                                </div>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $task->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                </div>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="3">{{ old('description', $task->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="due_date">Deadline <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                                <input type="date" name="due_date" id="due_date"
                                    class="form-control @error('due_date') is-invalid @enderror"
                                    value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}"
                                    min="{{ date('Y-m-d') }}"
                                    max="{{ $task->project->deadline ? \Carbon\Carbon::parse($task->project->deadline)->format('Y-m-d') : '' }}"
                                    required>
                                @error('due_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small>Deadline Proyek:
                                {{ $task->project->deadline ? \Carbon\Carbon::parse($task->project->deadline)->format('d M Y') : '-' }}
                            </small>
                            <small id="deadline-warning" class="form-text text-danger d-none">
                                Deadline tugas tidak boleh melebihi deadline proyek.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                </div>
                                <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="">Pilih Status</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}"
                                            {{ old('status', $task->status) == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="priority">Prioritas <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                </div>
                                <select name="priority" id="priority"
                                    class="form-control @error('priority') is-invalid @enderror" required>
                                    <option value="">Pilih Prioritas</option>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority }}"
                                            {{ old('priority', $task->priority) == $priority ? 'selected' : '' }}>
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
                                <label for="assigned_to">Ditugaskan Kepada <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <select name="assigned_to" id="assigned_to" class="form-control" required>
                                        <option value="" disabled>Tidak Ditugaskan</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
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

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">Update Tugas</button>
                        <a href="{{ route('projects.tasks.index', ['project' => $task->project_id]) }}"
                            class="btn btn-secondary float-right">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dueDateInput = document.getElementById('due_date');
            const projectDeadline =
                '{{ $task->project->deadline ? \Carbon\Carbon::parse($task->project->deadline)->format('Y-m-d') : '' }}';
            const warning = document.getElementById('deadline-warning');

            dueDateInput.addEventListener('change', function() {
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
