@extends('layouts.aplikasi')

@section('judul', 'Rekap Kerja - GJP Payroll')

@section('konten')
<div class="page-head">
    <div>
        <h1>Rekap Kerja & Lembur</h1>
        <p>Input dan verifikasi rekap kerja harian</p>
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

@unless(auth()->user()->berperanSebagai('karyawan'))
    <section class="card panel compact">
        <h2>Input Rekap</h2>
        <form method="post" action="{{ route('rekap.simpan') }}" class="entry-grid">
            @csrf
            <input type="hidden" name="karyawan_id" value="{{ $karyawanAktif->id }}">
            <label>Tanggal<input name="tanggal" type="date" value="{{ $periodeAktif->tanggal_mulai->format('Y-m-d') }}" required></label>
            <label>
                Status
                <select name="status">
                    <option>hadir</option>
                    <option>izin</option>
                    <option>sakit</option>
                    <option>mangkir</option>
                    <option>standby</option>
                </select>
            </label>
            <label>Total Jam<input name="total_jam" type="number" step=".5" min="0" value="8"></label>
            <label>x1,5<input name="x1_5" type="number" step=".5" min="0" value="0"></label>
            <label>x2<input name="x2" type="number" step=".5" min="0" value="0"></label>
            <label>x3<input name="x3" type="number" step=".5" min="0" value="0"></label>
            <label>x4<input name="x4" type="number" step=".5" min="0" value="0"></label>
            <label>BBL<input name="bbl" type="number" min="0" value="0"></label>
            <label>UJK<input name="ujk" type="number" min="0" value="0"></label>
            <label>Keterangan<input name="keterangan"></label>
            <button class="button primary">Simpan Rekap</button>
        </form>
    </section>
@endunless

<section class="card table-card">
    <div class="table-scroll">
        <table class="data-table work-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Total Jam</th>
                    <th>x1,5</th>
                    <th>x2</th>
                    <th>x3</th>
                    <th>x4</th>
                    <th>BBL</th>
                    <th>UJK</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekap as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>{{ $item->total_jam }}</td>
                        <td>{{ $item->x1_5 }}</td>
                        <td>{{ $item->x2 }}</td>
                        <td>{{ $item->x3 }}</td>
                        <td>{{ $item->x4 }}</td>
                        <td>Rp {{ number_format($item->bbl, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->ujk, 0, ',', '.') }}</td>
                        <td>
                            @if($item->diverifikasi_pada)
                                <span class="status">Terverifikasi</span>
                            @elseif(auth()->user()->berperanSebagai('admin', 'supervisor'))
                                <form method="post" action="{{ route('rekap.verifikasi', $item) }}" class="inline-form">
                                    @csrf
                                    @method('patch')
                                    <button class="button">Verifikasi</button>
                                </form>
                            @else
                                <span class="status gray">Menunggu</span>
                            @endif

                            @if(auth()->user()->berperanSebagai('admin', 'supervisor'))
                                <form method="post" action="{{ route('rekap.hapus', $item) }}" class="inline-form" onsubmit="return confirm('Hapus rekap ini?')">
                                    @csrf
                                    @method('delete')
                                    <button class="link-danger">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="empty-panel">Belum ada rekap pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
