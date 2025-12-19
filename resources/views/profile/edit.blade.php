@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
    <style>
        .profile-card {
            border-radius: 10px;
            overflow: hidden;
            transition: 0.3s;
        }


        .profile-header {
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: white;
            text-align: center;
            padding: 40px 20px;
            position: relative;
        }


        .profile-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            position: absolute;
            bottom: -70px;
            left: 50%;
            transform: translateX(-50%);
            background: #f8f9fa;
        }


        .edit-avatar-btn {
            position: absolute;
            bottom: -45px;
            left: 55%;
            transform: translateX(50%);
            background-color: #ffffff;
            border: none;
            color: #343434;
            border-radius: 50%;
            padding: 2px 5px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
            transition: all 0.3s ease;
            font-size: 17px;
        }


        .edit-avatar-btn:hover {
            background-color: #007bff;
            color: #ffffff;
            transform: translateX(50%) scale(1.1);
        }


        .profile-body {
            padding: 80px 30px 30px 30px;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card profile-card shadow-sm mb-4">
                <div class="profile-header">
                    <img id="profileImage"
                        src="{{ $user->avatar ? asset('storage/avatars/' . $user->avatar) : asset('storage/images/default_profile.jpg') }}"
                        alt="Avatar" class="profile-avatar">
                    @can('update profile')
                        <button type="button" class="edit-avatar-btn"><i class="fas fa-pencil-alt"></i></button>
                    @endcan
                    <h3 class="mt-2 mb-5" style="font-size: 23px; font-weight:800">{{ strtoupper($user->name) }}</h3>
                </div>

                <div class="profile-body">
                    @if (auth()->id() === $user->id)
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">

                            <div class="form-group mb-3">
                                <label>Nama Lengkap</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $user->phone) }}">
                            </div>

                            <div class="form-group mb-3">
                                <label>Alamat</label>
                                <textarea name="address" class="form-control">{{ old('address', $user->address) }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label>GitHub</label>
                                <input type="text" name="github" class="form-control"
                                    value="{{ old('github', $user->github) }}">
                            </div>

                            <div class="text-center mt-4">
                                <a href="{{ route('profile.show') }}" class="btn btn-secondary px-4"><i
                                        class="fas fa-arrow-left"></i> Kembali</a>
                                <button type="submit" class="btn btn-success px-4"><i class="fas fa-save"></i>
                                    Simpan</button>
                            </div>
                        </form>
                    @else
                        <p class="text-center text-muted">Anda tidak memiliki izin untuk mengedit profil ini.</p>
                    @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const avatarInput = document.getElementById('avatar');
        const editBtn = document.querySelector('.edit-avatar-btn');
        const profileImage = document.getElementById('profileImage');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                avatarInput.click();
            });
        }

        if (avatarInput) {
            avatarInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    profileImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>
@endsection
