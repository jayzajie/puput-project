@extends('layouts.aplikasi')

@section('judul', 'Laporan - GJP Payroll')

@section('konten')
<div class="page-head">
    <div>
        <h1>Laporan & Export</h1>
        <p>Cetak dan export berbagai laporan</p>
    </div>
</div>

@php
    $jenisLaporan = [
        ['payroll', 'Laporan Payroll', 'Rekap payroll per periode'],
        ['lembur', 'Laporan Lembur', 'Rekap jam dan biaya lembur'],
        ['ritase', 'Laporan Ritase', 'Ritase coal dan topsoil'],
        ['potongan', 'Laporan Potongan', 'Rekap semua potongan'],
        ['karyawan', 'Laporan Karyawan', 'Data karyawan dan gaji'],
    ];
@endphp

<div class="report-cards">
    @foreach($jenisLaporan as [$jenis, $nama, $deskripsi])
        <form method="post" action="{{ route('laporan.ekspor') }}" class="report-card">
            @csrf
            <input type="hidden" name="jenis" value="{{ $jenis }}">
            <strong>{{ $nama }}</strong>
            <small>{{ $deskripsi }}</small>
            <select name="periode_id" aria-label="Periode {{ $nama }}">
                @foreach($periode as $item)
                    <option value="{{ $item->id }}">{{ $item->label() }}</option>
                @endforeach
            </select>
            <button class="button">Export CSV</button>
        </form>
    @endforeach
</div>

<h2 class="section-title">Riwayat Laporan</h2>
<section class="card table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Laporan</th>
                    <th>Periode</th>
                    <th>Dibuat Oleh</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $item)
                    <tr>
                        <td>{{ $laporan->firstItem() + $loop->index }}</td>
                        <td>{{ $item->nama_laporan }}</td>
                        <td>{{ $item->periode_dari->format('d M Y') }} – {{ $item->periode_sampai->format('d M Y') }}</td>
                        <td>{{ $item->pembuat->name }}</td>
                        <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <form method="post" action="{{ route('laporan.hapus', $item) }}" onsubmit="return confirm('Hapus riwayat laporan?')">
                                @csrf
                                @method('delete')
                                <button class="link-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-panel">Belum ada laporan diekspor.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
