<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Laporan Harian EXCO')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="sidebar">
    <div class="brand-side">
        <div class="logo">Ex</div>

    </div>

    <nav>
        @if ( Auth::user()->level == 'admin' )
            <a href="{{ route('homepage.admin') }}" class="{{ Route::is('homepage.admin') ? 'active' : '' }}">🏠 Laman Utama Admin</a>
        @else
        <a href="{{ route('homepage') }}" class="{{ Route::is('homepage') ? 'active' : '' }}">📄 Senarai Laporan</a>
        @endif

         {{-- Navigation based on user level --}}

        @if ( Auth::user()->level == 'admin' )
            <a href="{{ route('users.index') }}" class="{{ Route::is('users.*') ? 'active' : '' }}">📄 Senarai User</a>
            <a href="{{ route('dorms.index') }}" class="{{ Route::is('dorms.*') ? 'active' : '' }}">🏠 Senarai Dorm</a>
        @else
            <a href="{{ route('laporan.create') }}" class="{{ Route::is('laporan.create') ? 'active' : '' }}">📝 Borang Laporan Harian</a> 
        @endif

        {{-- Tambah Dorm (boleh dicapai semua user, ikut keperluan) --}}
        <a href="{{ route('dorms.create') }}" class="{{ Route::is('dorms.create') ? 'active' : '' }}">➕ Tambah Dorm</a>


    </nav>

   <div class="user-box">
    <div class="avatar"></div>
    <p>{{ Auth::user()->name ?? 'username' }}</p>

    <form action="{{ route('logout') }}" method="POST" style="margin-top:12px;">
        @csrf
        <button class="logout-btn" type="submit">Log Out</button>
    </form>
</div>

</div>

<div class="main">
    <header>
        <h2>SISTEM LAPORAN HARIAN EXCO</h2>
    </header>

    <section class="container">
        @yield('content')
    </section>
</div>

</body>
</html>
