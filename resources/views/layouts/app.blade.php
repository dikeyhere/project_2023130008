<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Manajemen Tugas Tim') }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        .content-header h1,
        .card-title {
            font-weight: 500;
        }

        .dark-mode {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .navbar {
            background-color: #1f1f1f;
            color: #f1f1f1;
        }

        body.dark-mode .brand-link {
            color: #fff;
        }
    </style>
</head>

<body class="{{ session('dark_mode') ? 'dark-mode' : '' }} hold-transition sidebar-mini">
    <div class="wrapper">

        <nav class="main-header navbar navbar-expand">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <button id="darkModeToggle" class="btn btn-secondary btn-sm mt-1">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown">
                            <i class="far fa-user"></i>
                            <span class="d-none d-md-inline ml-1">{{ Auth::user()->name }}</span>

                            @php
                                $role = Auth::user()->getRoleNames()->first();
                            @endphp

                            @if ($role)
                                <span
                                    class="badge badge-{{ $role === 'admin' ? 'danger' : ($role === 'ketua_tim' ? 'warning' : 'info') }} ml-1">
                                    {{ ucwords(str_replace('_', ' ', $role)) }}
                                </span>
                            @endif

                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <span class="dropdown-item dropdown-header">{{ Auth::user()->name }}</span>

                            <div class="dropdown-divider"></div>

                            <a href="{{ route('profile.show') }}" class="dropdown-item">
                                <i class="fas fa-user mr-2"></i> Profil
                            </a>

                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                @csrf
                            </form>

                            <a href="#" class="dropdown-item"
                                onclick="event.preventDefault(); if(confirm('Yakin logout?')) document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </li>
                @endauth
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">

            <a href="{{ route('dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">Manajemen Tugas Tim</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column">

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

                        @role('admin')
                            <li class="nav-item">
                                <a href="{{ route('projects.create') }}" class="nav-link">
                                    <i class="nav-icon fas fa-plus"></i>
                                    <p>Tambah Proyek</p>
                                </a>
                            </li>
                        @endrole

                        <li class="nav-item">
                            <a href="{{ route('tasks.index') }}"
                                class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>Tugas</p>
                            </a>
                        </li>

                        @role('admin')
                            <li class="nav-item">
                                <a href="{{ route('reports.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-file"></i>
                                    <p>Laporan Kinerja</p>
                                </a>
                            </li>
                        @endrole

                        @role('admin|ketua_tim')
                            <li class="nav-item">
                                <a href="{{ route('financial.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-coins"></i>
                                    <p>Keuangan</p>
                                </a>
                            </li>
                        @endrole

                        @role('admin')
                            @if (Auth::user()->email === 'admin@example.com')
                                <li class="nav-item">
                                    <a href="{{ route('access.permission') }}" class="nav-link">
                                        <i class="nav-icon fas fa-info"></i>
                                        <p>Akses Akun</p>
                                    </a>
                                </li>
                            @endif
                        @endrole

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

        <div class="content-wrapper">

            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="m-0">@yield('title', 'Dashboard')</h1>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">

                    @if (session('success'))
                        <div class="alert alert-success flash-message">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger flash-message">{{ session('error') }}</div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </div>

        <footer class="main-footer text-center">
            Manajemen Tugas Tim — 2023130008 Andika
        </footer>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

    <script>
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');

            fetch("{{ route('toggle.darkmode') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        });
    </script>

    <script>
        setTimeout(() => $('.flash-message').fadeOut(), 3000);
    </script>

    @yield('scripts')
    @stack('scripts')

</body>

</html>
