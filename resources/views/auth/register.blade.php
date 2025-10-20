<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Manajemen Tugas Tim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #343A40;
            color: #3b3b3b;
            font-family: 'Figtree', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        .register-container {
            width: 90%;
            max-width: 420px;
            background-color: #F4F6F9;
            padding: 40px 35px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            margin: auto;
        }

        h2 {
            font-weight: 700;
            color: #2f2f2f;
            margin-bottom: 25px;
        }

        label {
            font-weight: 500;
            color: #3b3b3b;
            text-align: left;
            width: 100%;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #6c757d;
        }

        .btn-register {
            background-color: #343A40;
            color: #f0f0f0;
            border: 1px solid #d0d0d0;
            border-radius: 30px;
            text-transform: uppercase;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            padding: 10px 28px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .btn-register:hover {
            background-color: #e8e8e8;
            color: #1f1f1f;
            border-color: #c8c8c8;
        }

        .link-login {
            display: block;
            font-size: 0.9rem;
            color: #6b6b6b;
            text-decoration: none;
            margin-top: 15px;
        }

        .link-login:hover {
            text-decoration: underline;
            color: #343A40;
        }

        footer {
            margin-top: 40px;
            font-size: 0.85rem;
            color: #a0a0a0;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Buat Akun Baru</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3 text-start">
                <label for="name">Nama</label>
                <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mb-3 text-start">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mb-3 text-start">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mb-3 text-start">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Role -->
            <div class="mb-3 text-start">
                <label for="role">Peran</label>
                <select id="role" name="role" required>
                    <option value="" selected disabled>Pilih Peran</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="ketua_tim" {{ old('role') == 'ketua_tim' ? 'selected' : '' }}>Ketua Tim</option>
                    <option value="anggota_tim" {{ old('role') == 'anggota_tim' ? 'selected' : '' }}>Anggota Tim</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <button type="submit" class="btn btn-register">Daftar</button>
        </form>

        <a href="{{ route('login') }}" class="link-login">Sudah punya akun? Masuk di sini</a>
    </div>

    <footer>
        Project Manajemen Tugas Tim - 2023130008 Andika
    </footer>
</body>

</html>
