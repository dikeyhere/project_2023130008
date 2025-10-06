@extends('layouts.app')

@section('title', 'Daftar Tugas')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Tugas</h3>
        @if (Auth::user()->role === 'admin')
        <div class="card-tools">
            <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">Tambah Tugas</a>
        </div>
        @endif
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Prioritas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dummyTasks as $task)
                <tr>
                    <td>{{ $task['id'] }}</td>
                    <td>{{ $task['title'] }}</td>
                    <td>{{ Str::limit($task['description'], 50) }}</td>
                    <td>{{ $task['deadline'] }}</td>
                    <td><span class="badge badge-{{ $task['status'] == 'Completed' ? 'success' : ($task['status'] == 'In Progress' ? 'warning' : 'secondary') }}">{{ $task['status'] }}</span></td>
                    <td>
                        <span class="badge badge-{{ $task['priority'] == 'Urgent' ? 'danger' : ($task['priority'] == 'High' ? 'warning' : ($task['priority'] == 'Medium' ? 'info' : 'success')) }}">
                            {{ $task['priority'] }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('tasks.show', $task['id']) }}" class="btn btn-sm btn-info">Detail</a>
                        @if (Auth::user()->role === 'admin')
                        <a href="{{ route('tasks.edit', $task['id']) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="{{ route('tasks.destroy', $task['id']) }}" style="display:inline;" onsubmit="return confirm('Dummy: Hapus tugas?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                        @elseif (Auth::user()->role === 'ketua_tim')
                        <a href="{{ route('tasks.edit', $task['id']) }}" class="btn btn-sm btn-warning" onclick="alert('Ketua Tim: Monitor only - dummy edit')">Monitor/Edit</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">Tidak ada tugas dummy untuk role Anda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection