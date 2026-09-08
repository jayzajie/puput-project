@extends('layouts.aplikasi')

@section('judul', 'Profil - GJP Payroll')

@section('konten')
<div class="page-head">
    <div>
        <h1>Profil Saya</h1>
        <p>Kelola foto profil, password, dan keamanan akun Anda.</p>
    </div>
</div>

<div class="profile-grid">
    <section class="card profile-panel">
        <h2>Foto Profil</h2>
        <form class="profile-photo-form" method="post" action="{{ route('profil.foto') }}" enctype="multipart/form-data">
            @csrf
            @method('patch')
            @if(auth()->user()->foto_profil)
                <img class="profile-photo-preview large" src="{{ asset('storage/'.auth()->user()->foto_profil) }}" alt="Foto profil {{ auth()->user()->name }}">
            @else
                <span class="profile-photo-preview profile-photo-fallback large">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
            @endif
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->email }}</small>
            <input name="foto_profil" type="file" accept="image/jpeg,image/png,image/webp" required>
            <button class="button primary" type="submit">Simpan Foto</button>
            <small>JPG, PNG, atau WebP. Maksimal 2 MB.</small>
        </form>
    </section>

    <section class="card profile-panel">
        <h2>Ganti Password</h2>
        <form class="stack-form" method="post" action="{{ route('profil.password') }}">
            @csrf
            @method('patch')
            <label>Password Saat Ini<input name="password_saat_ini" type="password" required autocomplete="current-password"></label>
            <label>Password Baru<input name="password" type="password" required minlength="8" autocomplete="new-password"></label>
            <label>Konfirmasi Password Baru<input name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"></label>
            <button class="button primary" type="submit">Ubah Password</button>
        </form>
    </section>
</div>

<section class="card table-card login-history">
    <h2 class="section-title table-title">Riwayat Login</h2>
    <div class="table-scroll">
        <table class="data-table">
            <thead><tr><th>Waktu</th><th>Alamat IP</th><th>Perangkat / Browser</th></tr></thead>
            <tbody>
                @forelse($riwayatLogin as $riwayat)
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($riwayat->masuk_pada)->format('d M Y, H:i') }}</td>
                        <td>{{ $riwayat->ip_address ?? '-' }}</td>
                        <td class="user-agent">{{ $riwayat->user_agent ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-panel">Belum ada riwayat login.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
