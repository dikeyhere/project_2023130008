@extends('layouts.app')

@section('title', 'Detail Tugas')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $dummyTask['title'] }}</h3>
            </div>
            <div class="card-body">
                <p><strong>Deskripsi:</strong> {{ $dummyTask['description'] }}</p>
                <p><strong>Tenggat Waktu:</strong> {{ $dummyTask['deadline'] }}</p>
                <p><strong>Status:</strong> <span class="badge badge-{{ $dummyTask['status'] == 'Completed' ? 'success' : ($dummyTask['status'] == 'In Progress' ? 'warning' : 'secondary') }}">{{ $dummyTask['status'] }}</span></p>
                <p><strong>Prioritas:</strong> 
                    <span class="badge badge-{{ $dummyTask['priority'] == 'Urgent' ? 'danger' : ($dummyTask['priority'] == 'High' ? 'warning' : ($dummyTask['priority'] == 'Medium' ? 'info' : 'success')) }}">
                        {{ $dummyTask['priority'] }}
                    </span>
                </p>

                <!-- Sub-tugas Dummy -->
                <div class="mt-4">
                    <h5>Sub-tugas:</h5>
                    <ul class="list-group">
                        @forelse($dummyTask['subtasks'] as $sub)
                        <li class="list-group-item">{{ $sub }}</li>
                        @empty
                        <li class="list-group-item text-muted">Belum ada sub-tugas.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Lampiran File Dummy -->
                <div class="mt-4">
                    <h5>Lampiran File:</h5>
                    <ul class="list-group">
                        @forelse($dummyTask['files'] as $file)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ asset('storage/uploads/' . $file) }}" target="_blank" class="text-decoration-none">{{ $file }}</a>
                            <span class="badge badge-secondary">Downloaded</span>
                        </li>
                        @empty
                        <li class="list-group-item text-muted">Belum ada file lampiran.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Form Upload File & Sub-tugas (Hanya untuk Anggota/Ketua, validasi server) -->
        @if (Auth::user()->role === 'anggota_tim' || Auth::user()->role === 'ketua_tim')
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Tambah Lampiran / Sub-tugas (Dummy)</h3>
            </div>
            <form method="POST" action="{{ route('tasks.upload', $dummyTask['id']) }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="file">Upload File (jpg, png, pdf - max 2MB)</label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                        @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="subtask">Tambah Sub-tugas</label>
                        <input type="text" name="subtask" class="form-control @error('subtask') is-invalid @enderror" placeholder="Masukkan sub-tugas baru...">
                        @error('subtask') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Upload & Tambah (Dummy)</button>
                </div>
            </form>
        </div>
        @endif

        <!-- Aksi -->
        <div class="card">
            <div class="card-body text-center">
                <a href="{{ route('tasks.index') }}" class="btn btn-primary">Kembali ke Daftar</a>
                @if (Auth::user()->role === 'admin')
                <a href="{{ route('tasks.edit', $dummyTask['id']) }}" class="btn btn-warning">Edit Tugas</a>
                <form method="POST" action="{{ route('tasks.destroy', $dummyTask['id']) }}" style="display:inline;" onsubmit="return confirm('Dummy: Hapus tugas?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
@endif
@endsection