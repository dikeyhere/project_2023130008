<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Manajemen Tugas Tim</title>
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

        .reset-container {
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

        input {
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

        .btn-reset {
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

        .btn-reset:hover {
            background-color: #e8e8e8;
            color: #1f1f1f;
            border-color: #c8c8c8;
        }

        footer {
            margin-top: 40px;
            font-size: 0.85rem;
            color: #a0a0a0;
        }
    </style>
</head>

<body>

    <div class="reset-container">
        <h2>Reset Password</h2>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            {{-- TOKEN --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- EMAIL --}}
            <div class="mb-3 text-start">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                    autofocus>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- PASSWORD --}}
            <div class="mb-3 text-start">
                <label for="password">Password Baru</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- KONFIRMASI --}}
            <div class="mb-3 text-start">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="btn-reset">Reset Password</button>

        </form>
    </div>

    <footer>
        Project Manajemen Tugas Tim - 2023130008 Andika
    </footer>

</body>

</html>
