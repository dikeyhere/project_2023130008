@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content') <style>
        .profile-card {
            border-radius: 10px;
            overflow: hidden;
            transition: 0.3s;
        }

        .profile-card:hover {
            transform: translateY(-3px);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: white;
            text-align: center;
            padding: 40px 20px;
            position: relative;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            position: absolute;
            bottom: -60px;
            left: 50%;
            transform: translateX(-50%);
            background: #f8f9fa;
        }

        .profile-body {
            padding: 80px 30px 30px 30px;
            text-align: center;
        }

        .info-row {
            text-align: left;
            margin-bottom: 15px;
        }

        .info-row h6 {
            color: #555;
            font-weight: 600;
        }

        .flash-message {
            animation: fadeOut 1s ease-in-out forwards;
            animation-delay: 4s;
        }

        .form-control {
            border-color: #c4c6c7;
            border-radius: 3px;
        }

        body.dark-mode .info-row h6 {
            color: #ccc !important;
        }

        body.dark-mode .info-row p {
            color: #eaeaea !important;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateY(-10px);
                visibility: hidden;
            }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card profile-card shadow-sm mb-4">
                <div class="profile-header">
                    <img src="{{ $user->avatar ? asset('storage/avatars/' . $user->avatar) : asset('storage/images/default_profile.jpg') }}"
                        alt="Avatar" class="profile-avatar">
                    <h3 class="mt-2 mb-1" style="font-size: 23px; font-weight:800">{{ strtoupper($user->name) }}</h3>
                    <span
                        class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'ketua_tim' ? 'warning' : 'info') }} mb-5"
                        style="font-size: 13px; font-weight:500">
                        {{ ucwords(str_replace('_', ' ', $user->role)) }}
                    </span>
                </div>

                <div class="profile-body">
                    <div class="info-row">
                        <h6 class="text-muted fw-semibold">Email:</h6>
                        <p class="text-body mb-0">{{ $user->email }}</p>
                    </div>
                    <div class="info-row">
                        <h6 class="text-muted fw-semibold">Nomor Telepon:</h6>
                        <p class="text-body mb-0">{{ $user->phone ?? '-' }}</p>
                    </div>
                    <div class="info-row">
                        <h6 class="text-muted fw-semibold">Alamat:</h6>
                        <p class="text-body mb-0">{{ $user->address ?? '-' }}</p>
                    </div>
                    <div class="info-row">
                        <h6 class="text-muted fw-semibold">GitHub:</h6>
                        <p class="text-body mb-0">{{ $user->github ?? '-' }}</p>
                    </div>
                    <div class="info-row">
                        <h6 class="text-muted fw-semibold">Bergabung Sejak:</h6>
                        <p class="text-body mb-0">{{ $user->created_at->format('d M Y') }}</p>
                    </div>


                    <div class="mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary px-4">
                            <i class="fas fa-user-edit"></i> Edit Profil
                        </a>

                        <button type="button" class="btn btn-warning px-4" data-toggle="modal"
                            data-target="#resetPasswordModal">
                            <i class="fas fa-lock"></i> Ganti Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="resetPasswordModalLabel"><i class="fas fa-lock"></i> Reset Password</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @if (session('error') || $errors->any())
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
                                    modal.show();
                                });
                            </script>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show flash-message" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        @if (session('error') || $errors->any())
                            <div class="alert alert-danger alert-dismissible fade show flash-message" role="alert">
                                @if (session('error'))
                                    <div>{{ session('error') }}</div>
                                @endif

                                @if ($errors->any())
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="current_password">Password Lama</label>
                            <input type="password" name="current_password" id="current_password" class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Ubah Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const flashMessage = document.querySelector('.flash-message');
            if (flashMessage) {
                setTimeout(() => {
                    flashMessage.classList.add('fade');
                }, 4000);
            }
        });
    </script>

@endsection
