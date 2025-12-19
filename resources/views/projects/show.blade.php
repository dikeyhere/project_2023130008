@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card card-info">
                <div class="card-header text-center">
                    <h3 class="card-title pt-1">Detail Proyek</h3>

                    <div class="card-tools">
                        <a href="{{ auth()->user()->hasAnyRole(['admin', 'ketua_tim'])
                            ? route('projects.index')
                            : route('dashboard') }}"
                            class="btn btn-sm btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>

                        @can('edit projects')
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-warning ml-1">
                                <i class="fas fa-edit"></i> Edit Proyek
                            </a>
                        @endcan

                        @can('delete projects')
                            <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline ml-2"
                                onsubmit="return confirm('Yakin hapus?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger ml-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endcan

                        @can('create tasks')
                            <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-sm btn-success ml-1">
                                <i class="fas fa-plus"></i> Tambah Tugas
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <dl class="row">
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
                            {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}
                        </dd>

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
                            <span class="badge badge-{{ $priorityClass }}">
                                {{ $project->priority ? strtoupper($project->priority) : 'NONE' }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Dibuat Pada:</dt>
                        <dd class="col-sm-9">{{ $project->created_at->format('d M Y') }}</dd>

                        <dt class="col-sm-3">Diupdate Pada:</dt>
                        <dd class="col-sm-9">{{ $project->updated_at->format('d M Y') }}</dd>
                    </dl>
                </div>
            </div>

            @can('view expenses')
                @if ($project->budget)
                    <div class="card card-warning mt-3">
                        <div class="card-header">
                            <h3 class="card-title pt-1">
                                <i class="mr-1"></i> Ringkasan Keuangan Proyek
                            </h3>

                            <div class="card-tools">
                                @can('create expenses')
                                    <div class="text-right">
                                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#expenseModal">
                                            <i class="fas fa-money-bill-wave"></i> Ajukan Pengeluaran
                                        </button>
                                    </div>
                                @endcan
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h6>Total Budget</h6>
                                    <h5 class="text-primary">
                                        Rp {{ number_format($project->budget, 0, ',', '.') }}
                                    </h5>
                                </div>

                                <div class="col-md-4">
                                    <h6>Terpakai (Approved)</h6>
                                    <h5 class="text-danger">
                                        Rp {{ number_format($approvedExpense, 0, ',', '.') }}
                                    </h5>
                                </div>

                                <div class="col-md-4">
                                    <h6>Sisa Budget</h6>
                                    <h5 class="{{ $remainingBudget < 0 ? 'text-danger' : 'text-success' }}">
                                        Rp {{ number_format($remainingBudget, 0, ',', '.') }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endcan

            @can('view expenses')
                @if (isset($expenses) && $expenses->count() > 0)
                    <div class="card card-success mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-invoice-dollar mr-1"></i> Daftar Pengeluaran
                            </h3>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-striped">
                                <thead style="background-color:cadetblue; text-align:center">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Diajukan Oleh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $expense)
                                        <tr>
                                            <td class="text-center">{{ $expense->created_at->format('d M Y') }}</td>
                                            <td>{{ $expense->category }}</td>
                                            <td class="text-center">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge badge-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ strtoupper($expense->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $expense->user->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mt-3">
                        Belum ada pengeluaran untuk proyek ini.
                    </div>
                @endif
            @endcan

            @if ($project->tasks->count() > 0)
                <div class="table-responsive rounded mt-3">
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
                                    <td class="text-center">
                                        <span
                                            class="badge badge-{{ $task->status === 'Completed' ? 'success' : ($task->status === 'In Progress' ? 'warning' : 'info') }}">
                                            {{ $task->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ $task->due_date ? $task->due_date->format('d M Y') : 'Tidak ada' }}</td>
                                    <td class="text-center">{{ $task->assignee->name ?? 'Tidak ditugaskan' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning mt-3">Belum ada tugas untuk proyek ini.</div>
            @endif

        </div>
    </div>

    @can('create expenses')
        <div class="modal fade" id="expenseModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form method="POST" action="{{ route('financial.expense.store') }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title"><i class="fas fa-file-invoice-dollar mr-1"></i> Pengajuan Pengeluaran</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label>Kategori <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i
                                                class="fas fa-tags"></i></span></div>
                                    <input type="text" name="category" class="form-control rounded"
                                        placeholder="Tujuan pengajuan" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Nominal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="text" id="amount_display" class="form-control" placeholder="0"
                                        autocomplete="off" required>
                                    <input type="hidden" name="amount" id="amount_raw">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Pengeluaran <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i
                                                class="fas fa-calendar-alt"></i></span></div>
                                    <input type="date" name="expense_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i
                                                class="fas fa-align-left"></i></span></div>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i>
                                Ajukan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    @push('scripts')
        <script>
            const amountDisplay = document.getElementById('amount_display');
            const amountRaw = document.getElementById('amount_raw');

            function formatRupiah(value) {
                return value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            if (amountDisplay) {
                amountDisplay.addEventListener('input', function() {
                    let numericValue = this.value.replace(/\D/g, '');
                    this.value = formatRupiah(numericValue);
                    amountRaw.value = numericValue;
                });
            }
        </script>
    @endpush

@endsection
