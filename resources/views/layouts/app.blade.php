<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Laporan Harian Exco Aspura KVDSAZI')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="mobile-header">
    <div class="brand-mobile">
        <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="logo-sm">
        <span>E-lhea</span>
    </div>
    <button class="menu-toggle" onclick="toggleSidebar()">
        <span></span>
        <span></span>
        <span></span>
    </button>
</div>

<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

<div class="sidebar" id="sidebar">
    <div class="brand-side">
		<img class="logo" src="{{ asset("images/logo.jpeg") }}" alt="">
    </div>

    <nav>
        @if ( Auth::user()->level == 'admin' )
            
            {{-- Dropdown Parent --}}
            @php
                $isActiveDashboard = Route::is('homepage.admin') || Route::is('semakan.Laporan') || Route::is('kehadiran.index');
            @endphp

            <div class="nav-group {{ $isActiveDashboard ? 'active' : '' }}">
                <a href="#" class="nav-link dropdown-toggle" onclick="toggleNav(event, 'dashboard-menu')">
                    <span>📊 Dashboard</span>
                    <span class="arrow">▼</span>
                </a>
                <div class="nav-children" id="dashboard-menu" style="{{ $isActiveDashboard ? 'display:block' : 'display:none' }}">
                    <a href="{{ route('homepage.admin') }}" class="{{ Route::is('homepage.admin') ? 'active' : '' }}">
                        🏠 Statistik
                    </a>
                    <a href="{{ route('kehadiran.index') }}" class="{{ Route::is('kehadiran.index') ? 'active' : '' }}">
                        📋 Bilangan Kehadiran
                    </a>
                    <a href="{{ route('semakan.Laporan') }}" class="{{ Route::is('semakan.Laporan') ? 'active' : '' }}">
                        📄 Semakan Laporan
                    </a>
                </div>
            </div>

        @else
        <a href="{{ route('homepage') }}" class="{{ Route::is('homepage') ? 'active' : '' }}">📄 Senarai Laporan</a>
        @endif

         {{-- Navigation based on user level --}}

        @if ( Auth::user()->level == 'admin' )
            <a href="{{ route('users.index') }}" class="{{ Route::is('users.*') ? 'active' : '' }}">� Senarai Pengguna</a>
            <a href="{{ route('dorms.index') }}" class="{{ Route::is('dorms.*') ? 'active' : '' }}">🏠 Senarai Dorm</a>
        @else
            <a href="{{ route('laporan.create') }}" class="{{ Route::is('laporan.create') ? 'active' : '' }}">📝 Borang Laporan Harian</a>
            <a href="{{ route('dorms.userlist') }}" class="{{ Route::is('dorms.*') ? 'active' : '' }}">🏢 Senarai Dorm</a>
        @endif


    </nav>

   <div class="user-box">
    <div class="avatar"></div>
    <p>{{ Auth::user()->name ?? 'username' }}</p>

    <a href="{{ Auth::user()->level == 'admin' ? route('profile.editAdmin') : route('profile.edit') }}" style="display:inline-block; margin-top:8px; margin-bottom:8px; padding:8px 12px; background:#f3f4f6; color:#374151; text-decoration:none; border-radius:6px; font-size:13px; font-weight:600; width:100%; text-align:center; transition:background 0.2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">⚙️ Edit Profil</a>

    <form action="{{ route('logout') }}" method="POST" style="margin-top:8px;">
        @csrf
        <button class="logout-btn" type="submit">Log Out</button>
    </form>
</div>

</div>

<div class="main">
    <header>
        <h2>Sistem Laporan Harian Exco Aspura KVDSAZI</h2>
    </header>

    <section class="container">
        @yield('content')
    </section>
</div>

{{-- Inline Script & Styles for Dropdown --}}
<style>
    /* Dropdown Styles */
    .nav-group {
        margin-bottom: 4px;
    }
    .nav-link.dropdown-toggle {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    .nav-children {
        padding-left: 10px; /* Indent children */
        background: rgba(0,0,0,0.03);
        border-radius: 8px;
        margin-top: 4px;
        overflow: hidden;
    }
    .nav-children a {
        font-size: 0.9em;
        padding: 8px 12px;
        border-radius: 6px;
        display: block;
        color: #4b5563;
        text-decoration: none;
        margin-bottom: 2px;
    }
    .nav-children a:hover {
        background: #e5e7eb;
        color: #1f2937;
    }
    .nav-children a.active {
        background: #dbeafe;
        color: #1e40af;
        font-weight: 600;
    }
    .arrow {
        font-size: 10px;
        transition: transform 0.2s;
    }
    .nav-group.active .dropdown-toggle .arrow {
        transform: rotate(180deg);
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Restore state for dashboard menu - DEFAULT IS OPEN
        const dashboardMenu = document.getElementById('dashboard-menu');
        if (dashboardMenu) {
            const savedState = localStorage.getItem('nav_dashboard-menu');
            const parent = dashboardMenu.parentElement;

            // Only close if user explicitly closed it before
            if (savedState === 'closed') {
                dashboardMenu.style.display = 'none';
                parent.classList.remove('active');
            } else {
                // Default: Always open
                dashboardMenu.style.display = 'block';
                parent.classList.add('active');
            }
        }
    });

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggle = document.querySelector('.menu-toggle');
        sidebar.classList.toggle('active');
        backdrop.classList.toggle('active');
        toggle.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }

    function toggleNav(e, id) {
        e.preventDefault();
        const menu = document.getElementById(id);
        const parent = menu.parentElement; // .nav-group
        
        if (menu.style.display === 'none' || menu.style.display === '') {
            menu.style.display = 'block';
            parent.classList.add('active');
            localStorage.setItem('nav_' + id, 'open');
        } else {
            menu.style.display = 'none';
            parent.classList.remove('active');
            localStorage.setItem('nav_' + id, 'closed');
        }
    }
</script>

</body>
</html>
