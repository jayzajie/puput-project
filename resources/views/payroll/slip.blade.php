@extends('layouts.aplikasi')

@section('judul', 'Slip Gaji '.$slip->payroll->karyawan->nama)

@section('konten')
@php
    $payroll = $slip->payroll;
    $karyawan = $payroll->karyawan;
    $periode = $payroll->periode;
    $nomorSlip = sprintf('SLIP/%d/%02d/%04d', $periode->tahun, $periode->bulan, $slip->id);
    $komponenPendapatan = [
        ['Gaji Pokok', 'Upah pokok / basic rate bulanan', $payroll->gaji_pokok],
        ['Lembur', 'Akumulasi jam lembur terverifikasi', $payroll->total_lembur],
        ['Ritase Coal', 'Insentif ritase angkutan batubara', $payroll->total_ritase_coal],
        ['Ritase Topsoil', 'Insentif ritase angkutan topsoil (BCM)', $payroll->total_ritase_topsoil],
        ['Tunjangan', 'Tunjangan operasional lapangan (BBL & UJK)', $payroll->total_tunjangan],
    ];
@endphp

<div class="page-head">
    <div>
        <h1>Slip Gaji</h1>
        <p>{{ $karyawan->nama }} · Periode {{ $periode->label() }}</p>
    </div>
    <div class="head-actions">
        <a class="button" href="{{ route('payroll.index', ['karyawan_id' => $karyawan->id, 'periode_id' => $periode->id]) }}">← Kembali</a>
        <button class="button primary" type="button" onclick="window.print()">🖨 Cetak Slip</button>
    </div>
</div>

<article class="payslip card">
    <header class="payslip-header">
        <div class="payslip-brand">
            <img class="payslip-logo" src="{{ asset('logo.png') }}" alt="Logo GJP" width="64" height="52">
            <div>
                <h2>PT GADING JAYA PERKASA</h2>
                <p>Sistem Informasi Penggajian & Operasional</p>
            </div>
        </div>
        <div class="payslip-doc-box">
            <span class="payslip-doc-title">SLIP GAJI KARYAWAN</span>
            <small>No: {{ $nomorSlip }}</small>
            <small>Periode: <strong>{{ $periode->label() }}</strong></small>
        </div>
    </header>

    <table class="payslip-meta-table" aria-label="Informasi Karyawan">
        <tbody>
            <tr>
                <th>Nama Karyawan</th>
                <td><strong>{{ $karyawan->nama }}</strong></td>
                <th>Periode Gaji</th>
                <td>{{ $periode->tanggal_mulai->format('d M Y') }} – {{ $periode->tanggal_selesai->format('d M Y') }}</td>
            </tr>
            <tr>
                <th>NIK</th>
                <td>{{ $karyawan->nik }}</td>
                <th>Total Jam Kerja</th>
                <td>{{ number_format((float) $payroll->total_jam, 1, ',', '.') }} Jam</td>
            </tr>
            <tr>
                <th>Jabatan</th>
                <td>{{ $karyawan->jabatan }}</td>
                <th>Tanggal Terbit</th>
                <td>{{ ($slip->tanggal_cetak ?? $payroll->difinalisasi_pada ?? now())->format('d M Y') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="payslip-section">
        <div class="slip-heading">A. RINCIAN PENDAPATAN</div>
        <table class="payslip-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Komponen Pendapatan</th>
                    <th>Keterangan</th>
                    <th class="col-money">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($komponenPendapatan as [$label, $keterangan, $nilai])
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td><strong>{{ $label }}</strong></td>
                        <td class="text-muted">{{ $keterangan }}</td>
                        <td class="col-money">Rp {{ number_format($nilai, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="subtotal-row">
                    <th colspan="3">Total Pendapatan (Gaji Kotor)</th>
                    <th class="col-money">Rp {{ number_format($payroll->gaji_kotor, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="payslip-section">
        <div class="slip-heading deduction-heading">B. RINCIAN POTONGAN</div>
        <table class="payslip-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Jenis Potongan</th>
                    <th>Keterangan</th>
                    <th class="col-money">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payroll->potongan as $item)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->jenis_potongan }}</strong></td>
                        <td class="text-muted">{{ $item->deskripsi ?: 'Potongan periode berjalan' }}</td>
                        <td class="col-money">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="col-no">-</td>
                        <td colspan="2" class="text-muted">Tidak ada potongan pada periode ini</td>
                        <td class="col-money">Rp 0</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="subtotal-row">
                    <th colspan="3">Total Potongan</th>
                    <th class="col-money">Rp {{ number_format($payroll->total_potongan, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <table class="payslip-grand-table" aria-label="Total Gaji Bersih">
        <tbody>
            <tr class="slip-total slip-grand">
                <td>
                    <strong>TOTAL GAJI BERSIH</strong>
                    <small>Take Home Pay (Total Pendapatan dikurangi Total Potongan)</small>
                </td>
                <td class="col-money">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <footer class="payslip-signatures">
        <div class="signature-box">
            <span>Diterima Oleh,</span>
            <div class="signature-space"></div>
            <strong>{{ $karyawan->nama }}</strong>
            <small>{{ $karyawan->jabatan }}</small>
        </div>
        <div class="signature-box">
            <span>Disahkan Oleh,</span>
            <div class="signature-space"></div>
            <strong>Admin HR & Payroll</strong>
            <small>PT Gading Jaya Perkasa</small>
        </div>
    </footer>
</article>
@endsection
