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

    @if ($showWelcome)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-dismissible fade show border rounded" role="alert"
                    style="background-color: #76afe9; border-color: #dee2e6; color: #212121">
                    Selamat datang, {{ Auth::user()->name }}!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-3 col-6">
            @role('admin|ketua_tim')
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalProjects ?? 0 }}</h3>
                        <p>Total Proyek</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                </div>
            @else
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 class="text-white">{{ $totalTasks ?? 0 }}</h3>
                        <p class="text-white">Total Tugas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            @endrole
        </div>

        <div class="col-lg-3 col-6">
            @role('admin|ketua_tim')
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $completedProjects ?? 0 }}</h3>
                        <p>Proyek Selesai</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            @else
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $completedTasks ?? 0 }}</h3>
                        <p>Tugas Selesai</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            @endrole
        </div>

        <div class="col-lg-3 col-6">
            @role('admin|ketua_tim')
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 class="text-white">{{ $totalTasks ?? 0 }}</h3>
                        <p class="text-white">Total Tugas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            @else
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalProjects ?? 0 }}</h3>
                        <p>Total Proyek</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                </div>
            @endrole
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    @php
                        $totalTasks = $totalTasks ?? 0;
                        $completedTasks = $completedTasks ?? 0;
                        $percentage = $totalTasks > 0 ? number_format(($completedTasks / $totalTasks) * 100, 0) : 0;
                    @endphp
                    <h3>{{ $percentage }}%</h3>
                    <p>Penyelesaian</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-secondary">
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
                            @role('admin')
                                <a href="{{ route('projects.create') }}">Mulai buat sekarang!</a>
                            @endrole
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="thead-dark">
                                    <tr style="text-align:center">
                                        <th>Nama Proyek</th>
                                        <th>Status</th>
                                        <th>Jumlah Tugas</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentProjects as $project)
                                        <tr>
                                            <td>
                                                <a href="{{ route('projects.show', $project->id) }}">
                                                    {{ $project->name }}
                                                </a><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    Deadline:
                                                    {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}
                                                </small>
                                            </td>
                                            <td style="text-align:center">
                                                <span
                                                    class="badge badge-{{ $project->status === 'Completed' ? 'success' : ($project->status === 'In Progress' ? 'warning' : ($project->status === 'Planning' ? 'info' : 'secondary')) }}">
                                                    {{ $project->status }}
                                                </span>
                                            </td>
                                            <td style="text-align:center">
                                                <span class="badge badge-info">{{ $project->tasks->count() ?? 0 }}</span>
                                            </td>
                                            <td style="width: 20%; vertical-align:middle">
                                                @php
                                                    $progress = $project->progress ?? 0;
                                                @endphp
                                                <div class="progress rounded" style="height: 17px;">
                                                    <div class="progress-bar 
                                                        {{ $progress == 100 ? 'bg-success' : ($progress >= 50 ? 'bg-info' : 'bg-warning') }}"
                                                        role="progressbar" style="width: {{ $progress }}%;"
                                                        aria-valuenow="{{ $progress }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        <small class="font-weight-bold">{{ $progress }}%</small>
                                                    </div>
                                                </div>
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

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Distribusi Status Proyek</h3>
                </div>
                <div class="card-body">
                    <canvas id="projectsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('projectsChart');
            if (ctx) {
                const totalProjects = {{ $totalProjects ?? 0 }};
                const statuses = @json($projectStatuses ?? ['Planning' => 0, 'In Progress' => 0, 'Completed' => 0]);

                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(statuses),
                        datasets: [{
                            data: Object.values(statuses),
                            backgroundColor: [
                                '#17a2b8',
                                '#ffc107', 
                                '#28a745', 
                                '#dc3545'
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
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
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

            $('.small-box').hover(
                function() {
                    $(this).addClass('shadow-lg');
                },
                function() {
                    $(this).removeClass('shadow-lg');
                }
            );

            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).alert('close');
                });
            }, 3000);
        });
    </script>
@endpush
