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
    @vite(['resources/css/app.css'])  <!-- CSS OK, JS commented di bawah -->
    <!-- Custom CSS: Apply Figtree font global -->
<style>
    body { 
        font-family: 'Figtree', sans-serif; 
    }
    /* Opsional: Enhance untuk AdminLTE elements (headings, cards) */
    .content-header h1, .card-title, .small-box .inner h3 {
        font-weight: 500;  /* Medium untuk headings */
    }
    .nav-link, .dropdown-item {
        font-weight: 400;  /* Regular untuk menu/text */
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
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="userDropdown" data-toggle="dropdown" aria-expanded="false">  <!-- BS4: data-toggle (AdminLTE standar) -->
                            <i class="far fa-user"></i>
                            <span class="d-none d-md-inline ml-1">{{ Auth::user()->name }}</span>
                            @if (Auth::user()->role)
                                <span class="badge badge-{{ Auth::user()->role === 'admin' ? 'danger' : (Auth::user()->role === 'ketua_tim' ? 'warning' : 'info') }} ml-1">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            @endif
                        </a>
                        <!-- DROPDOWN MENU: OPSI SESUAI KODE ASLI (Header, Profile link, Logout dengan confirm) -->
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <span class="dropdown-item dropdown-header">Profile: {{ Auth::user()->name }}</span>  <!-- Header sesuai asli -->
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">  <!-- Item 1: Profile link sesuai asli -->
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <!-- Logout Form (CSRF-protected) - Sesuai asli -->
                            <form method="POST" action="{{ route('logout') }}" class="d-inline" id="logout-form">
                                @csrf
                            </form>
                            <a href="#" class="dropdown-item" onclick="  <!-- Item 2: Logout dengan onclick confirm sesuai asli -->
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

    <!-- Scripts (Urutan: jQuery dulu, AdminLTE (include BS4), lalu Chart.js, stack child scripts) -->
    <!-- jQuery (Versi stabil untuk AdminLTE BS4) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- AdminLTE JS (Include Bootstrap 4 core + plugins, no need separate BS4 JS) -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
    
    <!-- Chart.js (Stabil, kompatibel) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <!-- Breeze JS: COMMENTED UNTUK HINDARI KONFLIK BS5 (uncomment jika butuh, tapi test dropdown dulu) -->
    {{-- @vite(['resources/js/app.js']) --}}

    <!-- Safe Manual Init AdminLTE (Enhanced: BS4 priority + BS5 fallback untuk dropdown) -->
    <script>
        $(document).ready(function() {
            console.log('jQuery loaded:', typeof $ !== 'undefined');  // Debug: Konfirm jQuery OK
            
            // Delay init 500ms untuk pastikan semua CDN + Vite load (jika uncomment Breeze JS)
            setTimeout(function() {
                // Init PushMenu (Sidebar toggle)
                if (typeof $.fn.pushMenu !== 'undefined') {
                    $('[data-widget="pushmenu"]').pushMenu();
                    console.log('PushMenu init success');
                } else {
                    console.warn('PushMenu not available');
                }
                
                // Init Dropdown: BS4 Priority (AdminLTE), Fallback BS5 jika Breeze force
                const dropdownToggle = document.querySelector('.dropdown-toggle');
                if (dropdownToggle) {
                    console.log('Dropdown element found, attempting init...');
                    
                    // BS4 jQuery way (AdminLTE default)
                    if (typeof $.fn.dropdown !== 'undefined') {
                        $('.dropdown-toggle').dropdown({
                            boundary: 'viewport'  // Prevent overflow
                        });
                        console.log('Dropdown init: BS4 jQuery success');
                    } 
                    // BS5 Vanilla fallback (jika Breeze JS active)
                    else if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                        const dropdown = new bootstrap.Dropdown(dropdownToggle, {
                            toggle: false  // Manual trigger
                        });
                        // Add click event manual
                        dropdownToggle.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            dropdown.toggle();
                        });
                        console.log('Dropdown init: BS5 vanilla success');
                    } 
                    // Pure JS fallback (no Bootstrap)
                    else {
                        // Manual toggle class on click
                        dropdownToggle.addEventListener('click', function(e) {
                            e.preventDefault();
                            const menu = this.nextElementSibling;
                            if (menu.classList.contains('show')) {
                                menu.classList.remove('show');
                            } else {
                                menu.classList.add('show');
                            }
                            console.log('Dropdown init: Pure JS fallback');
                        });
                        // Close on outside click
                        document.addEventListener('click', function(e) {
                            if (!dropdownToggle.contains(e.target) && !dropdownToggle.nextElementSibling.contains(e.target)) {
                                dropdownToggle.nextElementSibling.classList.remove('show');
                            }
                        });
                    }
                } else {
                    console.warn('No dropdown-toggle found');
                }
                
                // Init Alerts (BS4 dismiss events) - Global
                if (typeof $.fn.alert !== 'undefined') {
                    $('.alert').alert();
                    console.log('Alert dismiss init success');
                } else {
                    console.warn('Alert dismiss not available');
                }
                
                console.log('AdminLTE Init complete');
            }, 500);  // Delay lebih panjang untuk Vite/Breeze jika uncomment
        });
    </script>

    <!-- Stack untuk child scripts (Render @push('scripts') dari views seperti dashboard) -->
    @stack('scripts')
</body>

</html>