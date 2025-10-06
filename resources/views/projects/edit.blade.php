@extends('layouts.app')

@section('title', 'Edit Proyek')

@section('content')
<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title">Edit Proyek</h3>
    </div>
    <form method="POST" action="{{ route('projects.update', $dummyProject['id']) }}">
        @csrf @method('PUT')
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
                <label for="name">Nama Proyek</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $dummyProject['name']) }}" required>
                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" class="form-control">{{ old('description', $dummyProject['name']) }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning">Update (Dummy)</button>
            <a href="{{ route('projects.show', $dummyProject['id']) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection