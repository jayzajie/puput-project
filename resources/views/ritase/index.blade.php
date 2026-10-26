@extends('layouts.aplikasi')

@section('judul', 'Ritase - GJP Payroll')

@section('konten')
<div class="page-head">
    <div>
        <h1>Ritase Coal & Topsoil</h1>
        <p>Input ritase dan hitung upah otomatis</p>
    </div>
    <form method="get" class="head-actions">
        <label class="field">
            Karyawan
            <select name="karyawan_id" class="select-field">
                @foreach($daftarKaryawan as $item)
                    <option value="{{ $item->id }}" @selected($item->is($karyawanAktif))>{{ $item->nama }}</option>
                @endforeach
            </select>
        </label>
        <label class="field">
            Periode
            <select name="periode_id" class="select-field">
                @foreach($daftarPeriode as $item)
                    <option value="{{ $item->id }}" @selected($item->is($periodeAktif))>{{ $item->label() }}</option>
                @endforeach
            </select>
        </label>
        <button class="button">Tampilkan</button>
    </form>
</div>

<div class="summary-layout">
    <section class="card summary-card">
        <h2>Input Ritase Coal</h2>
        <form method="post" action="{{ route('ritase.coal') }}" class="stack-form">
            @csrf
            <input type="hidden" name="karyawan_id" value="{{ $karyawanAktif->id }}">
            <label>Tanggal<input name="tanggal" type="date" value="{{ $periodeAktif->tanggal_mulai->format('Y-m-d') }}" required></label>
            <label>Jumlah Ritase<input name="jumlah_ritase" type="number" min="0" required></label>
            <button class="button primary">Simpan Coal</button>
        </form>

        <div class="metric-pair">
            <div class="metric-box">
                <small>Total Ritase</small>
                <strong>{{ $coal->sum('jumlah_ritase') }}</strong>
            </div>
            <div class="metric-box">
                <small>Upah Ritase</small>
                <strong>Rp {{ number_format($coal->sum('total_ritase'), 0, ',', '.') }}</strong>
            </div>
        </div>
    </section>

    <section class="card rates-card">
        <h2>Tarif Coal</h2>
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th>Rentang</th><th>Tarif</th></tr></thead>
                <tbody>
                    @foreach($tarifCoal as $item)
                        <tr>
                            <td>{{ $item->jarak_dari }} – {{ $item->jarak_sampai ?? '∞' }}</td>
                            <td>Rp {{ number_format($item->tarif, 0, ',', '.') }}/{{ $item->satuan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h2 class="spaced">Riwayat Coal</h2>
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th>Tanggal</th><th>Ritase</th><th>Total</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($coal as $item)
                        <tr>
                            <td>{{ $item->tanggal->format('d M Y') }}</td>
                            <td>{{ $item->jumlah_ritase }}</td>
                            <td>Rp {{ number_format($item->total_ritase, 0, ',', '.') }}</td>
                            <td>
                                <form method="post" action="{{ route('ritase.coal.hapus', $item) }}" onsubmit="return confirm('Hapus ritase coal?')">
                                    @csrf
                                    @method('delete')
                                    <button class="link-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="summary-layout">
    <section class="card summary-card">
        <h2>Input Ritase Topsoil</h2>
        <form method="post" action="{{ route('ritase.topsoil') }}" class="stack-form">
            @csrf
            <input type="hidden" name="karyawan_id" value="{{ $karyawanAktif->id }}">
            <label>Tanggal<input name="tanggal" type="date" value="{{ $periodeAktif->tanggal_mulai->format('Y-m-d') }}" required></label>
            <label>Jarak (KM)<input name="jarak_km" type="number" step=".1" min="0" max="12" required></label>
            <label>BCM<input name="bcm" type="number" step=".1" min="0" required></label>
            <label>Jumlah Ritase<input name="jumlah_ritase" type="number" min="0" required></label>
            <label>Bonus<input name="bonus" type="number" min="0" value="0"></label>
            <button class="button primary">Simpan Topsoil</button>
        </form>
    </section>

    <section class="card rates-card">
        <h2>Tarif Topsoil</h2>
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th>Jarak</th><th>Tarif / BCM</th></tr></thead>
                <tbody>
                    @foreach($tarifTopsoil as $item)
                        <tr>
                            <td>{{ $item->jarak_dari }} – {{ $item->jarak_sampai }} KM</td>
                            <td>Rp {{ number_format($item->tarif, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h2 class="spaced">Riwayat Topsoil</h2>
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th>Tanggal</th><th>BCM</th><th>Total</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($topsoil as $item)
                        <tr>
                            <td>{{ $item->tanggal->format('d M Y') }}</td>
                            <td>{{ $item->bcm }}</td>
                            <td>Rp {{ number_format($item->total_ritase, 0, ',', '.') }}</td>
                            <td>
                                <form method="post" action="{{ route('ritase.topsoil.hapus', $item) }}" onsubmit="return confirm('Hapus ritase topsoil?')">
                                    @csrf
                                    @method('delete')
                                    <button class="link-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
