@extends('layouts.app')

@section('title', 'Laporan Sistem')

@section('content')

    @can('view reports')
        <div class="container-fluid">
            <div class="content-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="m-0"><i class="fas fa-chart-bar"></i> Laporan Aktivitas Sistem</h2>
                    <div class="d-flex gap-2 mb-2">
                        @can('export reports')
                            <a href="{{ route('reports.export', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('reports.export.excel', ['start_date' => $start, 'end_date' => $end]) }}"
                                class="btn btn-success ms-3">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        @endcan
                    </div>
                </div>
                <hr>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white"><strong>Filter Periode</strong></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" id="start_date" name="start_date"
                                value="{{ request('start_date', $start ?? '') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date', $end ?? '') }}"
                                class="form-control">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i>
                                Tampilkan</button>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="{{ route('reports.index') }}" class="btn btn-secondary w-100"><i class="fas fa-undo"></i>
                                Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row text-center mb-0">
                <div class="col-md-4">
                    <div class="small-box bg-info p-3 text-white rounded shadow">
                        <h3>{{ $totalProjects }}</h3>
                        <p>Total Proyek</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-success p-3 text-white rounded shadow">
                        <h3>{{ $completedProjects }}</h3>
                        <p>Proyek Selesai</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-warning p-3 text-white rounded shadow">
                        <h3>{{ $ongoingProjects }}</h3>
                        <p>Proyek Berjalan</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-tasks me-2"></i> <strong>&nbsp;Statistik Tugas</strong>
                </div>
                <div class="card-body p-0">
                    <div class="row text-center g-3 justify-content-center">
                        @php
                            $taskStats = [
                                [
                                    'icon' => 'fa-list',
                                    'label' => 'Total',
                                    'value' => $totalTasks,
                                    'color' => 'text-secondary',
                                ],
                                [
                                    'icon' => 'fa-check-circle',
                                    'label' => 'Selesai',
                                    'value' => $completedTasks,
                                    'color' => 'text-success',
                                ],
                                [
                                    'icon' => 'fa-spinner',
                                    'label' => 'Dalam Proses',
                                    'value' => $inProgressTasks,
                                    'color' => 'text-info',
                                ],
                                [
                                    'icon' => 'fa-pause-circle',
                                    'label' => 'Tertunda',
                                    'value' => $pendingTasks,
                                    'color' => 'text-warning',
                                ],
                                [
                                    'icon' => 'fa-exclamation-triangle',
                                    'label' => 'Overdue',
                                    'value' => $overdueTasks,
                                    'color' => 'text-danger',
                                ],
                            ];
                        @endphp
                        @foreach ($taskStats as $stat)
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="stat-box">
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                        <i class="fas {{ $stat['icon'] }} fa-2x mb-2 {{ $stat['color'] }}"></i>
                                        <p class="mb-1 fw-bold">{{ $stat['label'] }}</p>
                                        <h4 class="{{ $stat['color'] }} fw-bold mb-0">{{ $stat['value'] }}</h4>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <strong><i class="fas fa-users"></i> Statistik Anggota</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-dark">
                            <tr class="text-center">
                                <th>Nama</th>
                                <th>Total Tugas</th>
                                <th>Tugas Selesai</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userStats as $user)
                                @php
                                    $percent =
                                        $user->total_tasks > 0
                                            ? round(($user->completed_tasks / $user->total_tasks) * 100)
                                            : 0;
                                @endphp
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td class="text-center">{{ $user->total_tasks }}</td>
                                    <td class="text-center">{{ $user->completed_tasks }}</td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: {{ $percent }}%;"></div>
                                        </div>
                                        <small>{{ $percent }}%</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white d-flex align-items-center">
                    <i class="fas fa-chart-line me-2"></i> <strong>&nbsp;Grafik Aktivitas Tugas</strong>
                </div>
                <div class="card-body">
                    <canvas id="tasksChart" height="120"></canvas>
                </div>
            </div>

        </div>

        <style>
            .stat-box {
                background: #f9fafc;
                border-radius: 8px;
                padding: 25px 10px;
                min-height: 150px;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                transition: all 0.25s ease-in-out;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .stat-box:hover {
                background: #eef2f7;
                transform: translateY(-4px);
                box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
            }

            @media (max-width: 992px) {
                .row.text-center.g-3>div {
                    flex: 1 1 45%;
                }
            }

            #tasksChart {
                max-height: 350px;
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('tasksChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Selesai', 'Dalam Proses', 'Tertunda', 'Lewat Deadline'],
                    datasets: [{
                        label: 'Jumlah Tugas',
                        data: [{{ $completedTasks ?? 0 }}, {{ $inProgressTasks ?? 0 }},
                            {{ $pendingTasks ?? 0 }}, {{ $overdueTasks ?? 0 }}
                        ],
                        backgroundColor: ['rgba(40,167,69,0.7)', 'rgba(23,162,184,0.7)', 'rgba(255,193,7,0.7)',
                            'rgba(220,53,69,0.7)'
                        ],
                        borderColor: ['rgba(40,167,69,1)', 'rgba(23,162,184,1)', 'rgba(255,193,7,1)',
                            'rgba(220,53,69,1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 0,
                        hoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#343a40',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 0,
                            padding: 10
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    }
                }
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($tasksByDate->pluck('date')) !!},
                    datasets: [{
                        label: 'Jumlah Tugas Dibuat',
                        data: {!! json_encode($tasksByDate->pluck('count')) !!},
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0,123,255,0.2)',
                        fill: true,
                        tension: 0.3,
                        pointStyle: 'circle',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    @endcan

@endsection
