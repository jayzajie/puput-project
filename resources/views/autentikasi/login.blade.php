<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - GJP Payroll</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<section class="login-screen">
    <div class="login-brand">
        <img class="login-logo" src="{{ asset('logo.png') }}" alt="Logo GJP" width="210" height="170">
        <h1>PT GADING JAYA PERKASA</h1>
        <p>Sistem Informasi Penggajian</p>
        <div class="site-silhouette" aria-hidden="true">
            <span class="excavator">◢</span>
            <span class="truck">▰</span>
        </div>
    </div>

    <div class="login-side">
        <form method="post" action="{{ route('masuk') }}" class="login-card">
            @csrf
            <img class="login-card-logo" src="{{ asset('logo.png') }}" alt="Logo GJP" width="92" height="74">
            <h2>Login</h2>
            <p>Masuk untuk melanjutkan</p>

            <label>
                Email
                <input name="email" type="email" placeholder="Masukkan email Anda" value="{{ old('email', 'admin@gjp.co.id') }}" required autofocus>
            </label>

            <label>
                Password
                <span class="password-wrap">
                    <input id="password" name="password" type="password" placeholder="Masukkan password Anda" value="password" required>
                    <button class="icon-button" type="button" data-toggle-password aria-label="Tampilkan password">◉</button>
                </span>
            </label>

            @error('email')
                <p class="field-error">{{ $message }}</p>
            @enderror

            <div class="login-options">
                <label class="check">
                    <input name="ingat" type="checkbox" value="1"> Ingat saya
                </label>
                <span></span>
            </div>

            <button class="button primary wide" type="submit">Login</button>
            <p class="login-hint">Admin: admin@gjp.co.id · Supervisor: supervisor@gjp.co.id · Karyawan: karyawan@gjp.co.id</p>
        </form>
        <p class="copyright">© 2026 PT Gading Jaya Perkasa</p>
    </div>
</section>
</body>
</html>
