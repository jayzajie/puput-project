@extends('layouts.aplikasi')

@section('judul', 'Dashboard - GJP Payroll')

@section('konten')
<div class="page-head">
    <div>
        <h1>Dashboard</h1>
        <p>Selamat datang, {{ auth()->user()->name }} 👋</p>
    </div>
    <div class="head-actions">
        <span class="field">
            Periode
            <strong class="select-field">{{ $periode?->label() ?? '-' }}</strong>
        </span>
    </div>
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
@endif
@endsection
