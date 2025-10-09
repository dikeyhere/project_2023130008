<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Manajemen Tugas Tim') }}</title>

    <!-- Font Awesome (untuk icons di navbar/sidebar) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Fonts: Figtree (Modern sans-serif untuk tampilan clean) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Breeze CSS ONLY (Comment JS untuk hindari BS5 konflik; CSS tetap untuk Tailwind jika perlu) -->
    @vite(['resources/css/app.css']) <!-- CSS OK, JS commented di bawah -->
    <!-- Custom CSS: Apply Figtree font global -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        /* Opsional: Enhance untuk AdminLTE elements (headings, cards) */
        .content-header h1,
        .card-title,
        .small-box .inner h3 {
            font-weight: 500;
        }

        .nav-link,
        .dropdown-item {
            font-weight: 400;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar (Top Bar) -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                </li>
            </ul>

            <!-- Right navbar links: Dropdown Profile + Logout -->
            <ul class="navbar-nav ml-auto">
                <!-- Profile Dropdown (Hanya untuk authenticated user) -->
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="userDropdown"
                            data-toggle="dropdown" aria-expanded="false"> <!-- BS4: data-toggle (AdminLTE standar) -->
                            <i class="far fa-user"></i>
                            <span class="d-none d-md-inline ml-1">{{ Auth::user()->name }}</span>
                            @if (Auth::user()->role)
                                <span
                                    class="badge badge-{{ Auth::user()->role === 'admin' ? 'danger' : (Auth::user()->role === 'ketua_tim' ? 'warning' : 'info') }} ml-1">
                                    {{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}
                                </span>
                            @endif
                        </a>
                        <!--Dropdown Menu (Header, Profile link, Logout) -->
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <span class="dropdown-item dropdown-header">{{ Auth::user()->name }}</span>
                            <!-- Header -->
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('profile.show') }}" class="dropdown-item">
                                <!-- Profile link -->
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <!-- Logout Form -->
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
                    <!-- Guest: Link Login/Register -->
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">Login</a>
                    </li>
                @endauth
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">Manajemen Tugas Tim</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Role-Based Menu: Projects (Admin & Ketua Tim) -->
                        @if (Auth::user()->role === 'admin' || Auth::user()->role === 'ketua_tim')
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
                        @endif

                        <!-- Tasks (Semua Role) -->
                        <li class="nav-item">
                            <a href="{{ route('tasks.index') }}"
                                class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>Tugas</p>
                            </a>
                        </li>

                        <!-- Profile (Semua Role) -->
                        <li class="nav-item">
                            <a href="{{ route('profile.show') }}" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Profile</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header (Breadcrumbs) -->
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

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Project 2023130008 &copy; 2024 Manajemen Tugas Tim.</strong>
            All rights reserved.
        </footer>
    </div>

    <!-- Scripts: Urutan Benar & Sekali Saja (No Duplikasi, No Custom Init) -->
    <!-- 1. jQuery (Versi stabil, sekali) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- 2. Bootstrap 4 Bundle (Include Popper untuk dropdowns/tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- <!-- 3. AdminLTE JS (Auto-init semua widgets: collapse, pushmenu, dropdown, alerts) -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script> --}}
    <!-- 3. AdminLTE JS (Local - No CDN Issue) -->
    {{-- <script src="{{ asset('js/adminlte.min.js') }}"></script> --}}
    {{-- <script src="https://unpkg.com/admin-lte@3.2.0/dist/js/adminlte.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

    <!-- 4. Chart.js (Opsional global, jika dipakai di banyak view; else pindah ke view-specific) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <!-- 5. Font Awesome JS (Opsional, jika butuh icons interaktif; CSS sudah cukup) -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script> -->

    <!-- 6. Yield & Stack Scripts (Untuk custom JS dari views, sekali saja) -->

    <!-- ... AdminLTE script ... -->

    <!-- Debug Script (Sementara: Hapus setelah fix) -->
    <script>
        $(document).ready(function() {
            // Check jQuery (sudah OK)
            console.log('jQuery Version:', $.fn ? $.fn.jquery : 'Missing');

            // Better AdminLTE Check: Pakai plugin spesifik (pushMenu/CardWidget proxy)
            const adminLTELoaded = typeof $.fn.pushMenu !== 'undefined' || typeof $.fn.cardWidget !== 'undefined';
            console.log('AdminLTE Loaded:', adminLTELoaded ? 'Yes (Plugins OK)' : 'No (Script Missing)');

            // Test Collapse Manual (Jika loaded, trigger satu collapse)
            if (adminLTELoaded) {
                console.log('Testing Collapse Widget...');
                // AdminLTE auto-init, tapi force re-init jika perlu
                if (typeof $.AdminLTE !== 'undefined') {
                    $.AdminLTE.init(); // Re-init global jika available
                }
            } else {
                console.warn('AdminLTE not loaded - Widgets (collapse, sidebar) will not work');
            }

            // Test Dropdown (BS4)
            console.log('Bootstrap Dropdown Available:', typeof $.fn.dropdown !== 'undefined');
        });
    </script>

    @yield('scripts')
    @stack('scripts')

</body>

</html>
