<nav x-data="{ open: false }" class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="z-index: 1000;">
    <div class="container">

        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Project Management') }}
        </a>

        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-nav-link>

                @auth
                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'ketua_tim')
                        <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                            {{ __('Projects') }}
                        </x-nav-link>
                    @endif
                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'ketua_tim' || auth()->user()->role === 'anggota_tim')
                        <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
                            {{ __('Tasks') }}
                        </x-nav-link>
                    @endif
                @endauth
            </ul>

            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="userDropdown"
                            data-bs-toggle="dropdown" data-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-user"></i>
                            <span class="d-none d-md-inline ml-1">{{ Auth::user()->name }}</span>
                            @if (Auth::user()->role)
                                <span
                                    class="badge badge-{{ Auth::user()->role === 'admin' ? 'danger' : (Auth::user()->role === 'ketua_tim' ? 'warning' : 'info') }} ml-1">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" aria-labelledby="userDropdown">
                            <li><span class="dropdown-item dropdown-header">Profile: {{ Auth::user()->name }}</span></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>

                                <form method="POST" action="{{ route('logout') }}" class="dropdown-item p-0"
                                    style="display: inline-block; width: 100%;">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-link text-left w-100 p-3 border-0 bg-transparent text-decoration-none"
                                        onclick="return confirm('Apakah Anda yakin ingin logout?')">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">Login</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
