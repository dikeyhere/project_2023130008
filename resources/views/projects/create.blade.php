@extends('layouts.app')

@section('title', 'Tambah Proyek')

@section('content')

    @can('create projects')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Proyek Baru</h3>
                    </div>
                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Nama Proyek <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-project-diagram"></i></span>
                                    </div>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        required placeholder="Masukkan nama proyek">
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
                                        rows="4" placeholder="Deskripsi proyek (opsional)">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="team_leader_id">Ketua Tim <span class="text-danger">*</span></label>
                                <select name="team_leader_id" id="team_leader_id" class="form-control" required>
                                    <option value="" selected disabled>-- Pilih Ketua Tim --</option>
                                    @foreach ($teamLeaders as $leader)
                                        <option value="{{ $leader->id }}"
                                            {{ old('team_leader_id') == $leader->id ? 'selected' : '' }}>
                                            {{ $leader->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_leader_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="deadline"><i class="fas fa-calendar-alt mr-1"></i> Deadline Proyek <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="deadline" id="deadline" class="form-control"
                                    value="{{ old('deadline', $project->deadline ?? '') }}" required>
                                @error('deadline')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                    </div>
                                    <select name="status" id="status"
                                        class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="" selected disabled>Pilih Status</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}"
                                                {{ old('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="priority">Prioritas <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="form-control" required>
                                    <option value="" selected disabled>Pilih Prioritas</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                                @error('priority')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="budget">Budget Proyek <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" id="budget"
                                        class="form-control @error('budget') is-invalid @enderror" placeholder="0"
                                        value="{{ old('budget') }}" autocomplete="off">
                                    <input type="hidden" name="budget" id="budget_raw">
                                </div>
                                @error('budget')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan Proyek</button>
                            <a href="{{ route('projects.index') }}" class="btn btn-secondary float-right">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            Anda tidak memiliki izin untuk menambahkan proyek.
        </div>
    @endcan

    @push('scripts')
        <script>
            const budgetInput = document.getElementById('budget');
            const budgetRaw = document.getElementById('budget_raw');

            function formatRupiah(value) {
                return value.replace(/\D/g, '')
                    .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            budgetInput.addEventListener('input', function() {
                let numericValue = this.value.replace(/\D/g, '');
                this.value = formatRupiah(numericValue);
                budgetRaw.value = numericValue;
            });

            document.addEventListener('DOMContentLoaded', () => {
                if (budgetInput.value) {
                    let numericValue = budgetInput.value.replace(/\D/g, '');
                    budgetInput.value = formatRupiah(numericValue);
                    budgetRaw.value = numericValue;
                }
            });
        </script>
    @endpush

@endsection
