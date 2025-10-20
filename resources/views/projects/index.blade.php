@extends('layouts.app')

@section('title', 'Proyek')

@section('content')
    <div class="row mb-3">
        <div class="col-md-6">

        </div>
        <div class="col-md-6 text-right">
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Tambah Proyek
                </a>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('projects.index') }}">
                <div class="form-row align-items-end">

                    <div class="col-md-3 mb-2">
                        <label for="search">Cari Proyek</label>
                        <input type="text" name="search" id="search" class="form-control rounded"
                            style="border-color:#CED4DA" value="{{ request('search') }}" placeholder="Cari proyek...">
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="filter_status">Filter Status</label>
                        <select name="filter_status" id="filter_status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="Planning" {{ request('filter_status') == 'Planning' ? 'selected' : '' }}>Planning
                            </option>
                            <option value="On Going" {{ request('filter_status') == 'On Going' ? 'selected' : '' }}>On Going
                            </option>
                            <option value="On Hold" {{ request('filter_status') == 'On Hold' ? 'selected' : '' }}>On Hold
                            </option>
                            <option value="Completed" {{ request('filter_status') == 'Completed' ? 'selected' : '' }}>
                                Completed</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label for="filter_priority">Filter Prioritas</label>
                        <select name="filter_priority" id="filter_priority" class="form-control">
                            <option value="">Semua Prioritas</option>
                            <option value="high" {{ request('filter_priority') == 'high' ? 'selected' : '' }}>High
                            </option>
                            <option value="medium" {{ request('filter_priority') == 'medium' ? 'selected' : '' }}>Medium
                            </option>
                            <option value="low" {{ request('filter_priority') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="sort_by">Urutkan</label>
                        <select name="sort_by" id="sort_by" class="form-control">
                            <option value="">Default</option>
                            <option value="priority_asc" {{ request('sort_by') == 'priority_asc' ? 'selected' : '' }}>
                                Prioritas Tertinggi</option>
                            <option value="priority_desc" {{ request('sort_by') == 'priority_desc' ? 'selected' : '' }}>
                                Prioritas Terendah</option>
                            <option value="deadline_asc" {{ request('sort_by') == 'deadline_asc' ? 'selected' : '' }}>
                                Deadline Terdekat</option>
                            <option value="deadline_desc" {{ request('sort_by') == 'deadline_desc' ? 'selected' : '' }}>
                                Deadline Terlama</option>
                        </select>
                    </div>

                    <div class="col-md-1 mb-2">
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>

                    <div class="col-md-1 mb-2">
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse ($projects as $project)
            @php
                $cardColors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                $cardColor = $cardColors[$project->id % count($cardColors)];
            @endphp
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card card-{{ $cardColor }} card-outline">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 pl-2" style="font-size:1.4rem; font-weight:900;">{{ $project->name }}</h2>
                            <h4 class="mb-0  pl-2 text-muted" style="font-size:0.9rem; font-weight:400;">
                                {{ $project->description ?? '-' }}</h4>
                        </div>

                        <div class="d-flex align-items-center ml-auto pr-2">
                            @php
                                switch ($project->status) {
                                    case 'Completed':
                                        $statusClass = 'success';
                                        break;
                                    case 'On Hold':
                                        $statusClass = 'secondary';
                                        break;
                                    case 'On Going':
                                        $statusClass = 'info';
                                        break;
                                    case 'Planning':
                                        $statusClass = 'warning';
                                        break;
                                    default:
                                        $statusClass = 'dark';
                                }
                            @endphp

                            <span class="badge badge-{{ $statusClass }} mr-2">
                                {{ $project->status }}
                            </span>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            @php
                                $projectDeadline = $project->deadline ?? null;
                            @endphp

                            @if ($projectDeadline)
                                <div>
                                    <strong><i class="fas fa-calendar-alt mr-1"></i> Deadline Proyek:</strong>
                                    {{ \Carbon\Carbon::parse($projectDeadline)->format('d M Y') }}
                                </div>
                            @endif

                            <div>
                                <span class="badge badge-info">
                                    <i class="fas fa-tasks mr-1"></i> Tugas: {{ $project->tasks->count() }}
                                    ({{ $project->tasks->where('status', 'Completed')->count() }} Selesai)
                                </span>
                            </div>
                        </div>

                        @php
                            $progress = $project->progress ?? 0;
                            $progressColor = $progress < 50 ? 'danger' : ($progress < 80 ? 'warning' : 'success');
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0">Progress</h6>
                                <small class="font-weight-bold">{{ $progress }}%</small>
                            </div>

                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-{{ $progressColor }} progress-bar progress-bar-animated rounded"
                                    role="progressbar" style="width: {{ $progress }}%; transition: width 0.5s;">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <div class="d-flex flex-wrap">
                                    @php
                                        $assignedUsers = $project->tasks
                                            ->pluck('assignee')
                                            ->filter()
                                            ->unique('id')
                                            ->map(function ($user) use ($project) {
                                                $taskCount = $project->tasks->where('assigned_to', $user->id)->count();
                                                $user->task_count = $taskCount;
                                                return $user;
                                            })
                                            ->sortBy(function ($user) {
                                                return match ($user->role) {
                                                    'admin' => 2,
                                                    'ketua_tim' => 1,
                                                    default => 3,
                                                };
                                            });
                                    @endphp

                                    @forelse ($assignedUsers as $user)
                                        <img src="{{ $user->avatar_url ?? asset('storage/images/default_profile.jpg') }}"
                                            alt="{{ $user->name }}" class="img-circle elevation-2 mr-2"
                                            style="width:30px; height:30px; border-radius:50%; object-fit:cover; cursor:pointer;"
                                            data-toggle="tooltip"
                                            title="{{ $user->name }} - {{ $user->task_count }} Tugas"
                                            onerror="this.src='{{ asset('storage/images/default_profile.jpg') }}'; this.onerror=null;">
                                    @empty
                                        <span class="text-muted">Belum ada anggota yang ditugaskan.</span>
                                    @endforelse
                                </div>
                            </div>

                            @php
                                $priorityClass = match ($project->priority) {
                                    'high' => 'danger',
                                    'medium' => 'warning',
                                    'low' => 'success',
                                    default => 'secondary',
                                };
                            @endphp
                            <div>
                                <span class="badge badge-{{ $priorityClass }} mr-2">
                                    {{ $project->priority ? strtoupper($project->priority) : 'NONE' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer" style="margin-top: -10px">
                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye mr-1"></i> Lihat Detail
                        </a>
                        @if (in_array(Auth::user()->role, ['admin']))
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-sm ml-2">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">Belum ada proyek yang tersedia.</div>
            </div>
        @endforelse
    </div>

@endsection

@push('scripts')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
