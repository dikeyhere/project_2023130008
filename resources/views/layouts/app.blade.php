<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Manajemen Tugas Tim') }}</title>

    <!-- Font Awesome (icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Fonts: Figtree -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        .content-header h1,
        .card-title,
        .small-box .inner h3 {
            font-weight: 500;
        }

        .nav-link,
        .dropdown-item {
            font-weight: 400;
        }

        .dark-mode {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
        }

        .dark-mode .card-header {
            background-color: #2c2c2c !important;
            color: #fff !important;
        }

        .dark-mode .table {
            color: #e0e0e0 !important;
        }

        body.dark-mode .navbar {
            background-color: #1f1f1f;
            color: #f1f1f1;
        }

        body.dark-mode .navbar a {
            color: #f1f1f1;
        }

        .navbar a,
        .navbar .nav-link,
        .navbar .brand-link {
            color: inherit;
            transition: color 0.2s;
        }

        .navbar a:hover,
        .navbar .nav-link:hover,
        .navbar .brand-link:hover {
            color: inherit !important;
            text-decoration: none;
        }

        body.dark-mode .brand-link {
            color: #ffffff;
        }

        body.dark-mode .brand-link:hover {
            color: #ffffff !important;
            text-decoration: none;
        }

        .form-control {
            border-color: #c4c6c7;
            border-radius: 3px;
        }
    </style>
</head>

<body class="{{ session('dark_mode') ? 'dark-mode' : '' }} hold-transition sidebar-mini">
    <div class="wrapper">
        {{-- Navbar Atas --}}
        <nav class="main-header navbar navbar-expand">

            {{-- Navbar Kiri --}}
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>

            {{-- Navbar Kanan --}}
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <button id="darkModeToggle" class="btn btn-secondary btn-sm align-items-center mt-1">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="userDropdown"
                            data-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-user"></i>
                            <span class="d-none d-md-inline ml-1">{{ Auth::user()->name }}</span>
                            @if (Auth::user()->role)
                                <span
                                    class="badge badge-{{ Auth::user()->role === 'admin' ? 'danger' : (Auth::user()->role === 'ketua_tim' ? 'warning' : 'info') }} ml-1">
                                    {{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}
                                </span>
                            @endif
                        </a>

                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <span class="dropdown-item dropdown-header">{{ Auth::user()->name }}</span>

                            <div class="dropdown-divider"></div>
                            <a href="{{ route('profile.show') }}" class="dropdown-item">

                                <i class="fas fa-user mr-2"></i>Profil
                            </a>
                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}" class="d-inline" id="logout-form">
                                @csrf
                            </form>
                            <a href="#" class="dropdown-item"
                                onclick="  <!-- Item 2: Logout dengan onclick confirm sesuai asli -->
                                event.preventDefault();
                                if(confirm('Apakah Anda yakin ingin logout?')) {
                                    document.getElementById('logout-form').submit();
                                }
                            ">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">Login</a>
                    </li>
                @endauth
            </ul>
        </nav>

        {{-- Main Sidebar --}}
        <aside class="main-sidebar sidebar-dark-primary elevation-4">

            <a href="{{ route('dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">Manajemen Tugas Tim</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('projects.index') }}"
                                class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-project-diagram"></i>
                                <p>Proyek</p>
                            </a>
                        </li>
                        @if (Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a href="{{ route('projects.create') }}" class="nav-link">
                                    <i class="nav-icon fas fa-plus"></i>
                                    <p>Tambah Proyek</p>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            @isset($project)
                                <a href="{{ route('tasks.index') }}"
                                    class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tasks"></i>
                                    <p>Tugas</p>
                                </a>
                            @else
                                <a href="{{ route('tasks.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-tasks"></i>
                                    <p>Tugas</p>
                                </a>
                            @endisset
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('profile.show') }}" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Profil</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Content Wrapper  --}}
        <div class="content-wrapper">
            {{-- Breadcrumbs --}}
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">@yield('title', 'Dashboard')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main content --}}
            <section class="content">
                <div class="container-fluid">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show flash-message" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show flash-message" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                    @if (!request()->routeIs('profile.show'))
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show flash-message" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    @endif
                    @yield('content')
                </div>
            </section>
        </div>

        {{-- Footer --}}
        <footer class="main-footer" style="text-align: center">
            Project Manajemen Tugas Tim - 2023130008 Andika
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('darkModeToggle');

            toggleBtn.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');

                fetch("{{ route('toggle.darkmode') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.flash-message').fadeOut('slow');
            }, 3000);
        });
    </script>

    @yield('scripts')
    @stack('scripts')

</body>

</html>
