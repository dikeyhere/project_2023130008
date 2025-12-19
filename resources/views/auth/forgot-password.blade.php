<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | Manajemen Tugas Tim</title>
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
            width: 420px;
            background-color: #F4F6F9;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        h2 {
            font-weight: 700;
            color: #2f2f2f;
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            color: #3b3b3b;
            width: 100%;
            text-align: left;
        }

        input[type="email"] {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .btn-login {
            background-color: #343A40;
            color: #f0f0f0;
            border: 1px solid #d0d0d0;
            border-radius: 30px;
            text-transform: uppercase;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 10px 28px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }

        .btn-login:hover {
            background-color: #e8e8e8;
            color: #1f1f1f;
        }

        .link-back {
            margin-top: 15px;
            display: block;
            font-size: 0.9rem;
            color: #6b6b6b;
            text-decoration: none;
        }

        .link-back:hover {
            text-decoration: underline;
            color: #343A40;
        }

        footer {
            margin-top: 40px;
            font-size: 0.85rem;
            color: #a0a0a0;
        }

        .text-infoemail {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h2>Lupa Password</h2>

        <p class="text-infoemail mb-3" style="font-size: 0.9rem;" >
            Masukkan email Anda. Kami akan mengirimkan link untuk mengatur ulang password Anda.
        </p>

        @if (session('status'))
            <div class="alert alert-success py-2">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3 text-start">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" required autofocus>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <button type="submit" class="btn-login">Kirim Link Reset</button>
        </form>

        <a href="{{ route('login') }}" class="link-back">← Kembali ke halaman login</a>
    </div>

    <footer>
        Project Manajemen Tugas Tim - 2023130008 Andika
    </footer>

</body>

</html>
