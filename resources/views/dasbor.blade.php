@extends('layouts.aplikasi')

@section('judul', 'Dashboard - GJP Payroll')

@section('konten')
<div class="page-head">
    <div>
        <h1>Dashboard</h1>
        <p>Selamat datang, {{ auth()->user()->name }} 👋</p>
    </div>
    <form method="get" class="head-actions">
        <label class="field">
            Periode
            <select name="periode_id" class="select-field" onchange="this.form.submit()">
                @forelse($daftarPeriode as $item)
                    <option value="{{ $item->id }}" @selected($periode && $item->is($periode))>{{ $item->label() }}</option>
                @empty
                    <option value="">-</option>
                @endforelse
            </select>
        </label>
    </form>
</div>

@php
    $statistik = [
        ['Total Karyawan', $jumlahKaryawan, 'Orang'],
        ['Total Lembur', number_format($totalLembur, 1, ',', '.'), 'Jam'],
        ['Total Ritase Coal', number_format($totalCoal, 0, ',', '.'), 'Ritase'],
        ['Total Ritase Topsoil', number_format($totalTopsoil, 0, ',', '.'), 'BCM'],
    ];

    if ($mode !== 'supervisor') {
        array_splice($statistik, 1, 0, [['Total Gaji Kotor', 'Rp '.number_format($totalGaji, 0, ',', '.'), '']]);
    }
@endphp

<div class="stats-grid">
    @foreach($statistik as [$label, $nilai, $satuan])
        <article class="card stat">
            <div class="stat-label">{{ $label }}</div>
            <div class="stat-value">{{ $nilai }}</div>
            <small>{{ $satuan }}</small>
        </article>
    @endforeach
</div>

@if($mode !== 'supervisor')
    <section class="card table-card">
        <h2 class="section-title table-title">Payroll Terakhir</h2>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Karyawan</th>
                        <th>Gaji Kotor</th>
                        <th>Potongan</th>
                        <th>Gaji Bersih</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payroll as $item)
                        <tr>
                            <td>{{ $item->periode->label() }}</td>
                            <td>{{ $item->karyawan->nama }}</td>
                            <td>Rp {{ number_format($item->gaji_kotor, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->total_potongan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->gaji_bersih, 0, ',', '.') }}</td>
                            <td><span class="status {{ $item->status === 'draft' ? 'gray' : '' }}">{{ ucfirst($item->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-panel">Belum ada payroll yang dihitung.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@else
    <section class="card table-card">
        <h2 class="section-title table-title">Daftar Karyawan</h2>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarKaryawan as $item)
                        <tr>
                            <td>{{ $daftarKaryawan->firstItem() + $loop->index }}</td>
                            <td>{{ $item->nik }}</td>
                            <td>
                                <strong>{{ $item->nama }}</strong>
                                <small class="subtext">{{ $item->email }}</small>
                            </td>
                            <td>{{ $item->jabatan }}</td>
                            <td>
                                <span class="status {{ $item->aktif ? '' : 'gray' }}">{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a class="button" href="{{ route('rekap.index', ['karyawan_id' => $item->id]) }}">Rekap Kerja</a>
                                    <a class="button" href="{{ route('ritase.index', ['karyawan_id' => $item->id]) }}">Ritase</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-panel">Belum ada data karyawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="pagination">{{ $daftarKaryawan->links() }}</div>
@endif
@endsection
