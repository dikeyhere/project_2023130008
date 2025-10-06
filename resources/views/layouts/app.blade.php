<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inert="">{{ config('app.name', 'Manajemen Tugas Tim') }}</title>

    <!-- Font Awesome (untuk icons di navbar/sidebar) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Breeze CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        {{-- <!-- Navbar (Top Bar) - Hapus @include untuk hindari duplikasi --> --}}
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
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
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-user"></i>
                            <span class="d-none d-md-inline ml-1">{{ Auth::user()->name }}</span>
                            @if (Auth::user()->role)
                                <span class="badge badge-{{ Auth::user()->role === 'admin' ? 'danger' : (Auth::user()->role === 'ketua_tim' ? 'warning' : 'info') }} ml-1">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <span class="dropdown-item dropdown-header">Profile: {{ Auth::user()->name }}</span>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <!-- Logout Form (CSRF-protected) -->
                            <form method="POST" action="{{ route('logout') }}" class="d-inline" id="logout-form">
                                @csrf
                            </form>
                            <a href="#" class="dropdown-item" onclick="
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
                    <!-- Guest: Link Login/Register (Opsional, jika ingin tampil di navbar) -->
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
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Role-Based Menu: Projects (Admin & Ketua Tim) -->
                        @if (Auth::user()->role === 'admin' || Auth::user()->role === 'ketua_tim')
                        <li class="nav-item">
                            <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
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

                        <!-- Tasks (Semua Role: Index tersedia, CRUD granular via routes) -->
                        <li class="nav-item">
                            <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>Tugas</p>
                            </a>
                        </li>

                        <!-- Profile (Semua Role) -->
                        <li class="nav-item">
                            <a href="{{ route('profile.edit') }}" class="nav-link">
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

<!-- Scripts (Urutan: jQuery dulu, AdminLTE, lalu init) -->
<!-- jQuery (Versi stabil untuk AdminLTE) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>  <!-- Update ke 3.6.4 untuk compat -->

<!-- AdminLTE JS (Update CDN ke versi stabil) -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>  <!-- Tambah .0 untuk exact version -->

<!-- Safe Manual Init AdminLTE (Cek fungsi ada dulu) -->
<script>
    $(document).ready(function() {
        console.log('jQuery loaded:', typeof $ !== 'undefined');  // Debug: Konfirm jQuery OK
        
        // Delay init 100ms untuk pastikan AdminLTE loaded
        setTimeout(function() {
            // Init PushMenu (Sidebar toggle) - Hanya jika fungsi ada
            if (typeof $.fn.pushMenu !== 'undefined') {
                $('[data-widget="pushmenu"]').pushMenu();
                console.log('PushMenu init success');
            } else {
                console.warn('PushMenu not available - AdminLTE JS mungkin gagal load');
            }
            
            // Init Dropdown - Hanya jika fungsi ada (BS/jQuery compat)
            if (typeof $.fn.dropdown !== 'undefined') {
                $('.dropdown-toggle').dropdown();
                console.log('Dropdown init success');
            } else if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                // Fallback BS5
                var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl, { toggle: false });
                });
                console.log('BS5 Dropdown fallback init success');
            } else {
                console.warn('Dropdown not available');
            }
            
            console.log('AdminLTE Init complete');
        }, 100);  // Delay kecil untuk CDN load
    });
</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>  <!-- Update versi stabil -->

<!-- Bootstrap JS (Fallback untuk dropdown BS5) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>  <!-- Update ke 5.3 -->

<!-- Breeze JS (Terakhir, no conflict) -->
@vite(['resources/js/app.js'])
@yield('scripts')
</body>

</html>