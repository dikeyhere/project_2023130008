@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        .small-box-footer.text-white {
            color: white !important;
        }

        .small-box:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
    </style>

    <!-- Role-based Welcome Alert (Hanya pertama kali) -->
    @if ($showWelcome)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-{{ $userRole === 'admin' ? 'success' : ($userRole === 'ketua_tim' ? 'warning' : 'info') }} alert-dismissible fade show border rounded"
                    role="alert" style="background-color: #f8f9fa; border-color: #dee2e6;">
                    Selamat datang, {{ Auth::user()->name }}!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Info Cards (Stats) - Responsive grid -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- Total Projects Card -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalProjects ?? 0 }}</h3>
                    <p>Total Proyek</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="{{ route('projects.index') }}" class="small-box-footer" title="Lihat detail proyek">
                    Lihat Semua <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <!-- Total Tasks -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 class="text-white">{{ $totalTasks ?? 0 }}</h3>
                    <p class="text-white">Total Tugas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <a href="{{ route('tasks.index') }}" class="small-box-footer text-white" title="Lihat semua tasks">
                    Lihat Semua <i class="fas fa-arrow-circle-right text-white"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <!-- Completed Projects -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $completedProjects ?? 0 }}</h3>
                    <p>Proyek Selesai</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('projects.index') }}" class="small-box-footer" title="Lihat proyek selesai">
                    Lihat Semua <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <!-- Persentase Penyelesaian Tasks -->
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>
                        @php
                            $totalTasks = $totalTasks ?? 0;
                            $completedTasks = $completedTasks ?? 0;
                            $percentage = $totalTasks > 0 ? number_format(($completedTasks / $totalTasks) * 100, 0) : 0;
                        @endphp
                        {{ $percentage }}%
                    </h3>
                    <p>Penyelesaian</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <a href="{{ route('tasks.index') }}" class="small-box-footer" title="Lihat detail tasks dan progress">
                    Lihat Semua <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Projects Table (Recent) -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Proyek Terbaru</h3>
                    <div class="card-tools">
                        <a href="{{ route('projects.index') }}" class="btn btn-tool btn-sm" title="Lihat semua proyek">
                            <i class="fas fa-list"></i>
                        </a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (empty($recentProjects) || $recentProjects->count() === 0)
                        <div class="alert alert-info m-3">
                            Belum ada proyek.
                            @if ($userRole === 'admin')
                                <a href="{{ route('projects.create') }}">Mulai buat sekarang!</a>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        {{-- <th>ID</th> --}}
                                        <th>Nama Proyek</th>
                                        <th>Status</th>
                                        <th>Jumlah Tasks</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentProjects as $project)
                                        <tr>
                                            {{-- <td>{{ $project->id }}</td> --}}
                                            <td>
                                                <strong>{{ $project->name }}</strong>
                                                {{-- @if ($project->creator)
                                                    <br><small class="text-muted">Oleh:
                                                        {{ $project->creator->name }}</small>
                                                @endif --}}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $project->status === 'Completed' ? 'success' : ($project->status === 'In Progress' ? 'warning' : ($project->status === 'Planning' ? 'info' : 'secondary')) }}">
                                                    {{ $project->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $project->tasks->count() ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('projects.show', $project->id) }}"
                                                    class="btn btn-sm btn-info" title="Lihat detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if (in_array($userRole, ['admin', 'ketua_tim']))
                                                    <a href="{{ route('projects.edit', $project->id) }}"
                                                        class="btn btn-sm btn-warning ml-1" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section (Pie Chart) -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribusi Status Proyek</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if (empty($projectStatuses) || count($projectStatuses) === 0)
                        <div class="alert alert-warning text-center">Belum ada data status proyek untuk ditampilkan.</div>
                    @else
                        <canvas id="projectsChart" height="300"></canvas>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Inisialisasi Chart (Interaktif: Hover tooltip, responsive, real data dari $projectStatuses)
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('projectsChart');
            if (ctx) {
                const totalProjects = {{ $totalProjects ?? 0 }};
                const statuses = @json($projectStatuses ?? []);
                if (Object.keys(statuses).length > 0) {
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(statuses),
                            datasets: [{
                                data: Object.values(statuses),
                                backgroundColor: [
                                    '#17a2b8', // Planning (info cyan)
                                    '#ffc107', // In Progress (warning yellow)
                                    '#28a745', // Completed (success green)
                                    '#dc3545' // On Hold (danger red)
                                ],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Status Proyek (Total: ' + totalProjects + ')',
                                    font: {
                                        size: 16
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b,
                                                0);
                                            const percentage = total > 0 ? Math.round((context.parsed /
                                                total) * 100) : 0;
                                            return context.label + ': ' + context.parsed + ' (' +
                                                percentage + '%)';
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateRotate: true,
                                duration: 2000
                            }
                        }
                    });
                }
            }
        });

        // Hover effect pada cards (shadow + lift)
        $('.small-box').hover(
            function() {
                $(this).addClass('shadow-lg');
            },
            function() {
                $(this).removeClass('shadow-lg');
            }
        );

        // Auto-hide welcome alert setelah 3 detik
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).alert('close');
                });
            }, 3000);
        });
    </script>
@endpush
