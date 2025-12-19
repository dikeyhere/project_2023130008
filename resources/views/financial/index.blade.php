@extends('layouts.app')

@section('title', 'Keuangan')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="fas fa-chart-line"></i> Manajemen Keuangan</h3>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Total Budget</h6>
                        <h4>Rp {{ number_format($totalBudget ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6>Total Pengeluaran</h6>
                        <h4>Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Sisa Budget</h6>
                        <h4>Rp {{ number_format($remainingBudget ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <strong>Daftar Pengeluaran</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>Tanggal</th>
                            <th>Proyek</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Pengaju</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="text-center">{{ $expense->expense_date }}</td>
                                <td>{{ $expense->project->name }}</td>
                                <td>{{ $expense->category }}</td>
                                <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                <td>{{ $expense->user->name }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge badge-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ strtoupper($expense->status) }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if ($expense->status === 'pending' && (auth()->user()->can('approve expense') || auth()->user()->can('reject expense')))
                                        @if (auth()->user()->can('approve expense'))
                                            <form action="{{ route('financial.expense.approve', $expense) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if (auth()->user()->can('reject expense'))
                                            <form action="{{ route('financial.expense.reject', $expense) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Tolak pengeluaran ini?')">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada data pengeluaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
