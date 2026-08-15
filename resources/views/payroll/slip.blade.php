@extends('layouts.aplikasi')

@section('judul', 'Slip Gaji '.$slip->payroll->karyawan->nama)

@section('konten')
<div class="page-head">
    <div>
        <h1>Slip Gaji</h1>
        <p>{{ $slip->payroll->karyawan->nama }} · {{ $slip->payroll->periode->label() }}</p>
    </div>
    <button class="button primary" onclick="window.print()">Cetak Slip</button>
</div>

<article class="payslip card">
    <header>
        <h2>PT GADING JAYA PERKASA</h2>
        <h3>SLIP GAJI</h3>
        <p>Periode: {{ $slip->payroll->periode->label() }}</p>
    </header>

    <div class="employee-detail">
        Nama: {{ $slip->payroll->karyawan->nama }}<br>
        Jabatan: {{ $slip->payroll->karyawan->jabatan }}<br>
        NIK: {{ $slip->payroll->karyawan->nik }}
    </div>

    <div class="slip-heading">PENDAPATAN</div>
    @foreach([
        ['Gaji Pokok', $slip->payroll->gaji_pokok],
        ['Lembur', $slip->payroll->total_lembur],
        ['Ritase Coal', $slip->payroll->total_ritase_coal],
        ['Ritase Topsoil', $slip->payroll->total_ritase_topsoil],
        ['Tunjangan', $slip->payroll->total_tunjangan],
    ] as [$label, $nilai])
        <div class="slip-row">
            <span>{{ $label }}</span>
            <span>Rp {{ number_format($nilai, 0, ',', '.') }}</span>
        </div>
    @endforeach

    <div class="slip-heading">POTONGAN</div>
    @foreach($slip->payroll->potongan as $item)
        <div class="slip-row">
            <span>{{ $item->jenis_potongan }}</span>
            <span>Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
        </div>
    @endforeach

    <div class="slip-row slip-total slip-grand">
        <span>TOTAL GAJI BERSIH</span>
        <span>Rp {{ number_format($slip->payroll->gaji_bersih, 0, ',', '.') }}</span>
    </div>
</article>
@endsection
