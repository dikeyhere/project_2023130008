<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Manajemen Tugas Tim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #343A40;
            color: #3b3b3b;
            font-family: 'Figtree', sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 400px;
            background-color: #F4F6F9;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
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

        input[type="email"],
        input[type="password"] {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        input:focus {
            outline: none;
            border-color: #6c757d;
        }

        .btn-login {
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

        .btn-login:hover {
            background-color: #e8e8e8;
            color: #1f1f1f;
            border-color: #c8c8c8;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #5a5a5a;
        }

        .link-register,
        .link-forgot {
            display: block;
            font-size: 0.9rem;
            color: #6b6b6b;
            text-decoration: none;
        }

        .link-forgot {
            text-align: right;
            margin-bottom: 15px;
        }

        .link-register {
            margin-top: 10px;
        }

        .link-register:hover,
        .link-forgot:hover {
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
    <div class="login-container">
        <h2>Masuk ke Akun</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3 text-start">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus
                    autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mb-0 text-start">
                <label for="password">Password</label>
                <input class="mb-1" id="password" type="password" name="password" required
                    autocomplete="current-password">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            @if (Route::has('password.request'))
                <div class="text-end mt-0">
                    <a href="{{ route('password.request') }}" class="link-forgot mb-0">Lupa Password?</a>
                </div>
            @endif

            <div class="mb-3 mt-4 text-start">
                <label for="captcha">Masukkan kode CAPTCHA</label>
                <div class="d-flex align-items-center gap-2">
                    <span id="captcha-img">{!! captcha_img('default') !!}</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="refreshCaptcha()">↻</button>
                </div>
                <input type="text" name="captcha" class="form-control mt-1" required>
                @error('captcha')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>




            <div class="form-check text-start mb-3">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-login">Masuk</button>
        </form>

        <a href="{{ route('register') }}" class="link-register">Belum punya akun? Daftar di sini</a>
    </div>

    <footer>
        Project Manajemen Tugas Tim - 2023130008 Andika
    </footer>
    <script>
        function refreshCaptcha() {
            fetch('{{ route('refreshCaptcha') }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('captcha-img').innerHTML = data.captcha;
                });
        }
    </script>

</body>


</html>
