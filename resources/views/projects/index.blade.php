@extends('layouts.app')

@section('title', 'Daftar Proyek')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Daftar Proyek</h3>
                    {{-- <div class="card-tools">
                    @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                        <a href="{{ route('projects.create') }}" class="btn btn-sm btn-success" title="Tambah Proyek Baru">
                            <i class="fas fa-plus"></i> Tambah
                        </a>
                    @endif
                </div> --}}
                    <div class="card-tools">
                        @if ($userRole === 'admin')
                            <a href="{{ route('projects.create') }}" class="btn btn-sm btn-success" title="Tambah Proyek Baru">
                                <i class="fas fa-plus"></i> Tambah
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('projects.index') }}" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari proyek berdasarkan nama..." value="{{ $search ?? '' }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                @if ($search)
                                    <a href="{{ route('projects.index') }}" class="btn btn-secondary"><i
                                            class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    @if ($projects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Proyek</th>
                                        <th>Deskripsi</th>
                                        <th>Status</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Jumlah Tasks</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projects as $project)
                                        <tr>
                                            <td>{{ $project->id }}</td>
                                            <td><strong>{{ $project->name }}</strong></td>
                                            <td>{{ Str::limit($project->description, 50) }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $project->status === 'Completed' ? 'success' : ($project->status === 'In Progress' ? 'warning' : ($project->status === 'Planning' ? 'info' : 'secondary')) }}">
                                                    {{ $project->status }}
                                                </span>
                                            </td>
                                            <td>{{ $project->creator->name ?? 'Unknown' }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $project->tasks->count() }}</span>
                                            </td>
                                            {{-- <td>
                                                <a href="{{ route('projects.show', $project) }}"
                                                    class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                                @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                                                    <a href="{{ route('projects.edit', $project) }}"
                                                        class="btn btn-sm btn-warning ml-1"><i class="fas fa-edit"></i></a>
                                                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Yakin hapus proyek ini? Tasks terkait akan ikut terhapus.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger ml-1"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </td> --}}
                                            <td>
                                                <a href="{{ route('projects.show', $project) }}"
                                                    class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                                @if (in_array($userRole, ['admin', 'ketua_tim']))
                                                    <a href="{{ route('projects.edit', $project) }}"
                                                        class="btn btn-sm btn-warning ml-1"><i class="fas fa-edit"></i></a>
                                                @endif
                                                @if ($userRole === 'admin')
                                                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                                        class="d-inline ml-1" onsubmit="return confirm('Yakin hapus?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $projects->appends(['search' => $search])->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">Belum ada proyek yang sesuai kriteria. @if (in_array($userRole ?? 'anggota', ['admin', 'ketua_tim']))
                                @if ($userRole === 'admin')
                                    <a href="{{ route('projects.create') }}">Buat sekarang!</a>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
@endsection
