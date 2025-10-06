@extends('layouts.app')

@section('title', 'Detail Proyek')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $dummyProject['name'] }}</h3>
    </div>
    <div class="card-body">
        <p><strong>Deskripsi:</strong> {{ $dummyProject['details'] }}</p>
        <p><strong>Status:</strong> <span class="badge badge-info">Active</span></p>
        <p><strong>Prioritas:</strong> <span class="badge badge-warning">High</span></p>
        <a href="{{ route('projects.index') }}" class="btn btn-primary">Kembali</a>
        @if (Auth::user()->role === 'admin')
        <a href="{{ route('projects.edit', $dummyProject['id']) }}" class="btn btn-warning">Edit</a>
        <form method="POST" action="{{ route('projects.destroy', $dummyProject['id']) }}" style="display:inline;" onsubmit="return confirm('Dummy: Hapus?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Hapus</button>
        </form>
        @endif
    </div>
</div>
@endsection