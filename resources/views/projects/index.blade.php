@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Proyek</h3>
                    @if (in_array(Auth::user()->role, ['admin', 'ketua_tim']))
                        <div class="card-tools">
                            <a href="{{ route('projects.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Tambah Proyek
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (empty($projects) || count($projects) === 0)
                        <div class="alert alert-info">Belum ada proyek. 
                            @if (in_array(Auth::user()->role, ['admin', 'ketua_tim']))
                                <a href="{{ route('projects.create') }}">Buat yang pertama!</a>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Proyek</th>
                                        <th>Deskripsi</th>
                                        <th>Status</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projects as $project)
                                        <tr>
                                            <td>{{ $project['id'] ?? $loop->iteration }}</td>
                                            <td><strong>{{ $project['name'] }}</strong></td>
                                            <td>{{ Str::limit($project['description'] ?? '', 50) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $project['status'] === 'Completed' ? 'success' : ($project['status'] === 'In Progress' ? 'warning' : ($project['status'] === 'Planning' ? 'info' : 'secondary')) }}">
                                                    {{ $project['status'] ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ isset($project['created_at']) ? $project['created_at']->format('d M Y') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('projects.show', $project['id']) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if (in_array(Auth::user()->role, ['admin', 'ketua_tim']))
                                                    <a href="{{ route('projects.edit', $project['id']) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('projects.destroy', $project['id']) }}" class="d-inline" style="margin-left: 5px;" onsubmit="return confirm('Yakin hapus proyek ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total: {{ count($projects) }} proyek</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Opsional: Auto-dismiss alert setelah 5 detik
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush