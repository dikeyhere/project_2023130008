@extends('layouts.app')

@section('title', 'Manajemen Role & Permission')

@section('content')
    <div class="container">

        <h3 class="mb-4">Manajemen Akses Pengguna</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="thead-dark">
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($users as $user)
                        @php
                            $currentRole = $user->roles->first()->name ?? 'anggota_tim';
                            $roleBadge = [
                                'admin' => 'danger',
                                'ketua_tim' => 'warning',
                                'anggota_tim' => 'info',
                            ][$currentRole];
                        @endphp

                        <tr>
                            <form action="{{ route('access.permission.update', $user) }}" method="POST">
                                @csrf

                                {{-- USER INFO --}}
                                <td>
                                    <strong>{{ $user->name }}</strong><br>
                                    <span class="text-muted">{{ $user->email }}</span><br>

                                    <span class="badge badge-{{ $roleBadge }}">
                                        {{ ucfirst(str_replace('_', ' ', $currentRole)) }}
                                    </span>
                                </td>

                                {{-- ROLE SELECT --}}
                                <td style="width: 180px;">
                                    <select name="role" class="form-control role-select" data-user="{{ $user->id }}">

                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ $currentRole === $role->name ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- PERMISSIONS --}}
                                <td>
                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="col-6">
                                                <label class="d-block permission-label user-{{ $user->id }}">
                                                    <input type="checkbox" name="permissions[]"
                                                        class="perm-checkbox user-id-{{ $user->id }}"
                                                        value="{{ $permission->name }}"
                                                        {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                    <span class="perm-text user-text-{{ $user->id }}">
                                                        {{ $permission->name }}
                                                    </span>

                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>


                                {{-- BUTTON --}}
                                <td style="width: 120px;">
                                    <button class="btn btn-success btn-block">
                                        Simpan
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            function applyRolePermissionUI(userId, role) {

                let checkboxes = document.querySelectorAll(".perm-checkbox.user-" + userId);

                checkboxes.forEach(cb => {
                    cb.disabled = false;
                    cb.parentElement.style.color = "#000";
                });

                if (role === "admin") {
                    return;
                }

                if (role === "ketua_tim") {

                    let allowed = [
                        "project.view",
                        "project.create",
                        "project.edit",
                        "task.view",
                        "task.create",
                        "task.edit"
                    ];

                    checkboxes.forEach(cb => {
                        if (!allowed.includes(cb.value)) {
                            cb.disabled = true;
                            cb.parentElement.style.color = "#999";
                        }
                    });

                    return;
                }

                if (role === "anggota_tim") {

                    let allowed = [
                        "task.view",
                        "task.submit",
                        "task.upload"
                    ];

                    checkboxes.forEach(cb => {
                        if (!allowed.includes(cb.value)) {
                            cb.disabled = true;
                            cb.parentElement.style.color = "#999";
                        }
                    });

                    return;
                }
            }

            document.querySelectorAll(".role-select").forEach(select => {
                select.addEventListener("change", function() {
                    let userId = this.dataset.user;
                    let role = this.value;

                    applyRolePermissionUI(userId, role);
                });
            });

            @foreach ($users as $user)
                applyRolePermissionUI({{ $user->id }}, "{{ $user->roles->first()->name ?? 'anggota_tim' }}");
            @endforeach

        });
    </script>

@endsection
