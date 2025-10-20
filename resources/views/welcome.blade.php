<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang | Manajemen Tim</title>
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

        .welcome-container {
            text-align: center;
            width: 800px;
            height: 300px;
            padding: 40px;
            align-content: center;
            background-color: #F4F6F9;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #2f2f2f;
            margin-bottom: 1px;
        }

        h2 {
            font-size: 2.5rem;
            font-weight: 1000;
            margin-bottom: 30px;
        }

        p {
            font-size: 1rem;
            color: #6b6b6b;
            margin-bottom: 30px;
        }

        .btn-custom {
            padding: 10px 28px;
            border-radius: 30px;
            text-transform: uppercase;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login {
            background-color: #343A40;
            color: #f0f0f0;
            border: 1px solid #d0d0d0;
        }

        .btn-login:hover {
            background-color: #e8e8e8;
            color: #1f1f1f;
            border-color: #c8c8c8;
        }

        .btn-register {
            border: 1px solid #d0d0d0;
            color: #f0f0f0;
            background-color: #343A40;
            margin-left: 10px;
        }

        .btn-register:hover {
            background-color: #e8e8e8;
            border-color: #c8c8c8;
            color: #1f1f1f;
        }

        footer {
            margin-top: 40px;
            font-size: 0.85rem;
            color: #a0a0a0;
        }
    </style>
</head>

<body>
    <div class="welcome-container">
        <h1>Selamat Datang di</h1>
        <h2>Manajemen Tugas Tim</h2>
        <div class="d-flex justify-content-center">
            <a href="{{ route('login') }}" class="btn btn-custom btn-login">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-custom btn-register">Daftar</a>
        </div>
    </div>

    <footer>
        Project Manajemen Tugas Tim - 2023130008 Andika
    </footer>
</body>

</html>
