@extends('layouts.app')

@section('title', 'Dashboard')  <!-- Ini akan muncul di header layouts (h1 + breadcrumb) -->

@section('content')
<!-- Role-based Greeting (Langsung di content, no wrapper tambahan) -->
<div class="row">
    <div class="col-12">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            Selamat datang, {{ Auth::user()->name }}! Role: <strong>{{ ucfirst($userRole ?? 'User ') }}</strong>
            @if($userRole === 'admin')
                <span class="badge badge-danger ml-2">Super Admin</span>
            @elseif($userRole === 'ketua_tim')
                <span class="badge badge-warning ml-2">Ketua Tim</span>
            @else
                <span class="badge badge-info ml-2">Anggota Tim</span>
            @endif
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>

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
                Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <!-- Total Tasks -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalTasks ?? 0 }}</h3>
                <p>Total Tasks</p>
            </div>
            <div class="icon">
                <i class="fas fa-tasks"></i>
            </div>
            <a href="{{ route('tasks.index') }}" class="small-box-footer" title="Lihat semua tasks">
                Lihat Semua <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <!-- Completed Tasks -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $completedTasks ?? 0 }}</h3>
                <p>Tasks Selesai</p>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <a href="{{ route('tasks.index') }}" class="small-box-footer" title="Lihat tasks selesai">
                Detail <i class="fas fa-arrow-circle-right"></i>
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
                </div>
            </div>
            <div class="card-body p-0">  <!-- No padding untuk table full-width -->
                @if (empty($dummyProjects) || count($dummyProjects) === 0)
                    <div class="alert alert-info m-3">Belum ada proyek. Mulai buat sekarang!</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Proyek</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dummyProjects as $project)
                                    <tr>
                                        <td>{{ $project['id'] }}</td>
                                        <td>
                                            <strong>{{ $project['name'] }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $project['status'] === 'Completed' ? 'success' : ($project['status'] === 'In Progress' ? 'warning' : ($project['status'] === 'Planning' ? 'info' : 'secondary')) }}">
                                                {{ $project['status'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('projects.show', $project['id']) }}" class="btn btn-sm btn-info" title="Lihat detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if (in_array($userRole, ['admin', 'ketua_tim']))
                                                <a href="{{ route('projects.edit', $project['id']) }}" class="btn btn-sm btn-warning ml-1" title="Edit">
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

<!-- Chart Section (Pie Chart Interaktif) -->
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
                <canvas id="projectsChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js (Sudah di layouts, tapi push untuk ensure) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Inisialisasi Chart (Interaktif: Hover tooltip, responsive)
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('projectsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: @json(array_keys($projectStatuses ?? [])),
                    datasets: [{
                        data: @json(array_values($projectStatuses ?? [])),
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'],  // Tambah ungu untuk On Hold
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
                            labels: { padding: 20 }
                        },
                        title: { 
                            display: true, 
                            text: 'Status Proyek (Total: {{ $totalProjects ?? 0 }})',
                            font: { size: 16 }
                        },
                        tooltip: {  // Interaktif: Hover show detail
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + ' (' + Math.round((context.parsed / {{ $totalProjects ?? 1 }} * 100)) + '%)';
                                }
                            }
                        }
                    },
                    animation: {  // Smooth animasi
                        animateRotate: true,
                        duration: 2000
                    }
                }
            });
        }
    });

    // Tambah interaksi: Hover cards (opsional, untuk lebih engaging)
    $('.small-box').hover(
        function() { $(this).addClass('shadow-lg'); },
        function() { $(this).removeClass('shadow-lg'); }
    );

    // Auto-dismiss alert setelah 5s
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush