@extends('layouts.aplikasi')

@section('judul', 'Pengaturan - GJP Payroll')

@section('konten')
<div class="page-head">
    <div>
        <h1>Pengaturan</h1>
        <p>Kelola tarif ritase dan periode gaji</p>
    </div>
</div>

<section class="card panel compact">
    <h2>Tambah Tarif Ritase</h2>
    <form method="post" action="{{ route('tarif.simpan') }}" class="entry-grid">
        @csrf
        <label>
            Jenis
            <select name="jenis">
                <option value="coal">Coal</option>
                <option value="topsoil">Topsoil</option>
            </select>
        </label>
        <label>Jarak Dari<input name="jarak_dari" type="number" step=".01" min="0"></label>
        <label>Jarak Sampai<input name="jarak_sampai" type="number" step=".01" min="0"></label>
        <label>Tarif<input name="tarif" type="number" min="0" required></label>
        <label>
            Satuan
            <select name="satuan">
                <option value="ritase">Ritase</option>
                <option value="bcm">BCM</option>
            </select>
        </label>
        <input type="hidden" name="aktif" value="1">
        <button class="button primary">Tambah Tarif</button>
    </form>
</section>

<section class="card table-card compact">
    <h2 class="section-title table-title">Tarif Ritase</h2>
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th>Dari</th>
                    <th>Sampai</th>
                    <th>Tarif</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tarif as $item)
                    <tr>
                        <td>
                            <select name="jenis" form="tarif-{{ $item->id }}">
                                <option value="coal" @selected($item->jenis === 'coal')>Coal</option>
                                <option value="topsoil" @selected($item->jenis === 'topsoil')>Topsoil</option>
                            </select>
                        </td>
                        <td><input name="jarak_dari" form="tarif-{{ $item->id }}" type="number" step=".01" min="0" value="{{ $item->jarak_dari }}"></td>
                        <td><input name="jarak_sampai" form="tarif-{{ $item->id }}" type="number" step=".01" min="0" value="{{ $item->jarak_sampai }}"></td>
                        <td><input name="tarif" form="tarif-{{ $item->id }}" type="number" min="0" value="{{ $item->tarif }}" required></td>
                        <td>
                            <select name="satuan" form="tarif-{{ $item->id }}">
                                <option value="ritase" @selected($item->satuan === 'ritase')>Ritase</option>
                                <option value="bcm" @selected($item->satuan === 'bcm')>BCM</option>
                            </select>
                        </td>
                        <td>
                            <select name="aktif" form="tarif-{{ $item->id }}">
                                <option value="1" @selected($item->aktif)>Aktif</option>
                                <option value="0" @selected(!$item->aktif)>Nonaktif</option>
                            </select>
                        </td>
                        <td>
                            <div class="row-actions">
                                <form id="tarif-{{ $item->id }}" method="post" action="{{ route('tarif.ubah', $item) }}">
                                    @csrf
                                    @method('put')
                                    <button class="button">Simpan</button>
                                </form>
                                <form method="post" action="{{ route('tarif.hapus', $item) }}" onsubmit="return confirm('Hapus tarif ini?')">
                                    @csrf
                                    @method('delete')
                                    <button class="link-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-panel">Belum ada tarif.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card panel compact">
    <h2>Tambah Periode Gaji</h2>
    <form method="post" action="{{ route('periode.simpan') }}" class="entry-grid">
        @csrf
        <label>Bulan<input name="bulan" type="number" min="1" max="12" required></label>
        <label>Tahun<input name="tahun" type="number" min="2020" max="2100" required></label>
        <label>Tanggal Mulai<input name="tanggal_mulai" type="date" required></label>
        <label>Tanggal Selesai<input name="tanggal_selesai" type="date" required></label>
        <button class="button primary">Tambah Periode</button>
    </form>
</section>

<section class="card table-card">
    <h2 class="section-title table-title">Periode Tersedia</h2>
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periode as $item)
                    <tr>
                        <td><input name="bulan" form="periode-{{ $item->id }}" type="number" min="1" max="12" value="{{ $item->bulan }}" required></td>
                        <td><input name="tahun" form="periode-{{ $item->id }}" type="number" min="2020" max="2100" value="{{ $item->tahun }}" required></td>
                        <td><input name="tanggal_mulai" form="periode-{{ $item->id }}" type="date" value="{{ $item->tanggal_mulai->format('Y-m-d') }}" required></td>
                        <td><input name="tanggal_selesai" form="periode-{{ $item->id }}" type="date" value="{{ $item->tanggal_selesai->format('Y-m-d') }}" required></td>
                        <td>
                            <select name="status" form="periode-{{ $item->id }}">
                                <option value="draft" @selected($item->status === 'draft')>Draft</option>
                                <option value="aktif" @selected($item->status === 'aktif')>Aktif</option>
                                <option value="selesai" @selected($item->status === 'selesai')>Selesai</option>
                            </select>
                        </td>
                        <td>
                            <div class="row-actions">
                                <form id="periode-{{ $item->id }}" method="post" action="{{ route('periode.ubah', $item) }}">
                                    @csrf
                                    @method('put')
                                    <button class="button">Simpan</button>
                                </form>
                                <form method="post" action="{{ route('periode.hapus', $item) }}" onsubmit="return confirm('Hapus periode ini?')">
                                    @csrf
                                    @method('delete')
                                    <button class="link-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-panel">Belum ada periode.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
