@extends('layouts.aplikasi')

@section('judul', 'Payroll & Slip Gaji - GJP Payroll')

@section('konten')
<div class="page-head">
    <div>
        <h1>Payroll & Slip Gaji</h1>
        <p>Perhitungan payroll dan publikasi slip gaji</p>
    </div>

    @unless(auth()->user()->berperanSebagai('karyawan'))
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
    @endunless
</div>

@if(auth()->user()->berperanSebagai('admin'))
    <div class="action-bar">
        <form method="post" action="{{ route('payroll.hitung') }}">
            @csrf
            <input type="hidden" name="karyawan_id" value="{{ $karyawanAktif->id }}">
            <input type="hidden" name="periode_id" value="{{ $periodeAktif->id }}">
            <button class="button primary">Hitung / Hitung Ulang Payroll</button>
        </form>

        @if($payroll && $payroll->status === 'draft')
            <form method="post" action="{{ route('payroll.finalisasi', $payroll) }}">
                @csrf
                @method('patch')
                <button class="button">Finalisasi & Publikasikan Slip</button>
            </form>
        @endif

        @if($payroll)
            <form method="post" action="{{ route('payroll.hapus', $payroll) }}" onsubmit="return confirm('Hapus payroll dan slip terkait?')">
                @csrf
                @method('delete')
                <button class="button danger">Hapus Payroll</button>
            </form>
        @endif
    </div>
@endif

@if($payroll)
    <div class="payroll-grid">
        <section class="card calculation">
            <h2>Detail Perhitungan</h2>
            <div class="calc-section">
                <h3>PENDAPATAN</h3>
                @foreach([
                    ['Gaji Pokok', $payroll->gaji_pokok],
                    ['Lembur', $payroll->total_lembur],
                    ['Ritase Coal', $payroll->total_ritase_coal],
                    ['Ritase Topsoil', $payroll->total_ritase_topsoil],
                    ['Tunjangan BBL & UJK', $payroll->total_tunjangan],
                ] as [$label, $nilai])
                    <div class="money-row">
                        <span>{{ $loop->iteration }}.</span>
                        <span>{{ $label }}</span>
                        <strong>Rp {{ number_format($nilai, 0, ',', '.') }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="calc-section">
                <h3>POTONGAN</h3>
                @forelse($payroll->potongan as $item)
                    <div class="money-row">
                        <span>{{ $loop->iteration }}.</span>
                        <span>{{ $item->jenis_potongan }}</span>
                        <strong>Rp {{ number_format($item->nominal, 0, ',', '.') }}</strong>
                    </div>
                @empty
                    <p class="subtext">Tidak ada potongan.</p>
                @endforelse
            </div>

            <div class="calc-grand">
                <span>Gaji Kotor</span>
                <span>Rp {{ number_format($payroll->gaji_kotor, 0, ',', '.') }}</span>
                <span>Total Potongan</span>
                <span>Rp {{ number_format($payroll->total_potongan, 0, ',', '.') }}</span>
                <span>Gaji Bersih</span>
                <span>Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</span>
            </div>
        </section>

        <section class="card payslip-wrap">
            <h2>Ringkasan</h2>
            <div class="summary-list">
                <p><span>Karyawan</span><strong>{{ $payroll->karyawan->nama }}</strong></p>
                <p><span>Periode</span><strong>{{ $payroll->periode->label() }}</strong></p>
                <p><span>Total jam</span><strong>{{ $payroll->total_jam }} jam</strong></p>
                <p>
                    <span>Status</span>
                    <strong class="status {{ $payroll->status === 'draft' ? 'gray' : '' }}">{{ ucfirst($payroll->status) }}</strong>
                </p>
            </div>

            @if($payroll->slipGaji)
                <a class="button primary wide" href="{{ route('slip.show', $payroll->slipGaji) }}">Lihat / Cetak Slip Gaji</a>

                @if(auth()->user()->berperanSebagai('admin'))
                    <form method="post" action="{{ route('slip.hapus', $payroll->slipGaji) }}" onsubmit="return confirm('Hapus slip dan kembalikan payroll ke draft?')">
                        @csrf
                        @method('delete')
                        <button class="button danger wide spaced">Hapus Slip</button>
                    </form>
                @endif
            @endif
        </section>
    </div>
@else
    <section class="card empty-panel">
        {{ auth()->user()->berperanSebagai('karyawan') ? 'Slip gaji periode ini belum dipublikasikan.' : 'Payroll belum dihitung. Pastikan rekap kerja telah diverifikasi.' }}
    </section>
@endif

@if(auth()->user()->berperanSebagai('admin'))
    <section class="card panel spaced">
        <h2>Tambah Potongan</h2>
        <form method="post" action="{{ route('potongan.simpan') }}" class="entry-grid">
            @csrf
            <input type="hidden" name="karyawan_id" value="{{ $karyawanAktif->id }}">
            <input type="hidden" name="periode_gaji_id" value="{{ $periodeAktif->id }}">
            <label>
                Jenis
                <select name="jenis_potongan">
                    <option>Mangkir</option>
                    <option>Izin Tanpa Upah</option>
                    <option>Sakit Tanpa Surat</option>
                    <option>BPJS</option>
                    <option>Pajak PPh 21</option>
                </select>
            </label>
            <label>Nominal<input name="nominal" type="number" min="0" required></label>
            <label>Deskripsi<input name="deskripsi"></label>
            <button class="button">Tambah Potongan</button>
        </form>

        @if($payroll && $payroll->status === 'draft' && $payroll->potongan->isNotEmpty())
            <h2 class="spaced">Kelola Potongan</h2>
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>Jenis</th><th>Nominal</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach($payroll->potongan as $item)
                            <tr>
                                <td><input name="jenis_potongan" form="potongan-{{ $item->id }}" value="{{ $item->jenis_potongan }}" required></td>
                                <td><input name="nominal" form="potongan-{{ $item->id }}" type="number" min="0" value="{{ $item->nominal }}" required></td>
                                <td><input name="deskripsi" form="potongan-{{ $item->id }}" value="{{ $item->deskripsi }}"></td>
                                <td>
                                    <div class="row-actions">
                                        <form id="potongan-{{ $item->id }}" method="post" action="{{ route('potongan.ubah', $item) }}">
                                            @csrf
                                            @method('put')
                                            <button class="button">Ubah</button>
                                        </form>
                                        <form method="post" action="{{ route('potongan.hapus', $item) }}" onsubmit="return confirm('Hapus potongan?')">
                                            @csrf
                                            @method('delete')
                                            <button class="link-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endif
@endsection
