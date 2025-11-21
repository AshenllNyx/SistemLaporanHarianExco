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
        <a href="">📄 Senarai Laporan</a>

        @if ( Auth::user()->level == 'admin' )
            <a href="{{ route('users.index') }}">📄 Senarai User</a>
            <a href="">📄 Senarai Dorm</a>
        @else
            <a href="">📝 Borang Laporan Harian</a> 
        @endif


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
