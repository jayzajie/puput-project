<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('judul', 'GJP Payroll')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@php($peran = auth()->user()->peran->nama)
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <img class="brand-logo" src="{{ asset('logo.png') }}" alt="Logo GJP">
            <strong>PT GADING<br>JAYA PERKASA</strong>
        </div>

        <nav id="main-nav" aria-label="Navigasi utama">
            <a class="nav-item {{ request()->routeIs('dasbor') ? 'active' : '' }}" href="{{ route('dasbor') }}">
                <span>⌂</span> Dashboard
            </a>

            @if($peran === 'admin')
                <a class="nav-item {{ request()->routeIs('karyawan.*') ? 'active' : '' }}" href="{{ route('karyawan.index') }}">
                    <span>♙</span> Karyawan
                </a>
            @endif

            <a class="nav-item {{ request()->routeIs('rekap.*') ? 'active' : '' }}" href="{{ route('rekap.index') }}">
                <span>▣</span> Rekap Kerja
            </a>

            @if(in_array($peran, ['admin', 'supervisor']))
                <a class="nav-item {{ request()->routeIs('ritase.*') ? 'active' : '' }}" href="{{ route('ritase.index') }}">
                    <span>▰</span> Ritase
                </a>
            @endif

            @if(in_array($peran, ['admin', 'karyawan']))
                <a class="nav-item {{ request()->routeIs('payroll.*', 'slip.*') ? 'active' : '' }}" href="{{ route('payroll.index') }}">
                    <span>▤</span> Payroll & Slip Gaji
                </a>
            @endif

            @if($peran === 'admin')
                <a class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                    <span>▥</span> Laporan
                </a>
                <a class="nav-item {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}" href="{{ route('pengaturan.index') }}">
                    <span>⚙</span> Pengaturan
                </a>
            @endif
        </nav>

        <div class="user-card">
            <a class="user-profile-link" href="{{ route('profil.index') }}" aria-label="Buka profil">
                @if(auth()->user()->foto_profil)
                    <img class="avatar" src="{{ asset('storage/'.auth()->user()->foto_profil) }}" alt="Foto profil {{ auth()->user()->name }}">
                @else
                    <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                @endif
                <span>
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->email }}</small>
                </span>
            </a>
            <form class="logout-form" method="post" action="{{ route('keluar') }}">
                @csrf
                <button class="icon-button logout-symbol" type="submit" aria-label="Keluar">↪</button>
            </form>
        </div>
    </aside>

    <header class="mobile-header">
        <button id="menu-toggle" class="icon-button" type="button" aria-label="Buka menu">☰</button>
        <img class="brand-logo" src="{{ asset('logo.png') }}" alt="Logo GJP">
        <strong>GJP Payroll</strong>
    </header>

    <main class="page">
        <div class="page-inner">
            @if(session('sukses'))
                <div class="alert success">{{ session('sukses') }}</div>
            @endif

            @if($errors->any())
                <div class="alert error">
                    <strong>Periksa kembali data:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('konten')
        </div>
    </main>
</div>
@stack('dialog')
</body>
</html>
