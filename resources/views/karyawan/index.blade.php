@extends('layouts.aplikasi')

@section('judul', 'Data Karyawan - GJP Payroll')

@section('konten')
<div class="page-head">
    <div>
        <h1>Data Karyawan</h1>
        <p>Kelola data karyawan perusahaan</p>
    </div>
    <div class="head-actions">
        <button class="button primary" type="button" data-dialog="tambah-karyawan">＋ Tambah Karyawan</button>
    </div>
</div>

<section class="card table-card">
    <form class="toolbar" method="get">
        <div class="search">
            <input name="cari" type="search" placeholder="Cari NIK atau nama..." value="{{ $cari }}">
        </div>
        <button class="button" type="submit">Cari</button>
    </form>

    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Basic / UPPK</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawan as $item)
                    <tr>
                        <td>{{ $karyawan->firstItem() + $loop->index }}</td>
                        <td>{{ $item->nik }}</td>
                        <td>
                            <strong>{{ $item->nama }}</strong>
                            <small class="subtext">{{ $item->email }}</small>
                        </td>
                        <td>{{ $item->jabatan }}</td>
                        <td>Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>
                        <td>
                            <span class="status {{ $item->aktif ? '' : 'gray' }}">{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td>
                            <details class="row-menu">
                                <summary>Ubah</summary>
                                <form method="post" action="{{ route('karyawan.ubah', $item) }}" class="inline-edit">
                                    @csrf
                                    @method('put')
                                    <input name="nik" value="{{ $item->nik }}" required>
                                    <input name="nama" value="{{ $item->nama }}" required>
                                    <input name="jabatan" value="{{ $item->jabatan }}" required>
                                    <input name="email" type="email" value="{{ $item->email }}" required>
                                    <input name="gaji_pokok" type="number" value="{{ $item->gaji_pokok }}" required>
                                    <select name="aktif">
                                        <option value="1" @selected($item->aktif)>Aktif</option>
                                        <option value="0" @selected(!$item->aktif)>Nonaktif</option>
                                    </select>
                                    <button class="button primary">Simpan</button>
                                </form>
                            </details>

                            <form method="post" action="{{ route('karyawan.hapus', $item) }}" class="inline-form" onsubmit="return confirm('Hapus data {{ $item->nama }}?')">
                                @csrf
                                @method('delete')
                                <button class="link-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-panel">Karyawan tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<div class="pagination">{{ $karyawan->links() }}</div>
@endsection

@push('dialog')
<dialog id="tambah-karyawan">
    <form method="post" action="{{ route('karyawan.simpan') }}" class="dialog-card">
        @csrf
        <div class="dialog-head">
            <div>
                <h2>Tambah Karyawan</h2>
                <p>Akun dibuat otomatis dengan password awal “password”.</p>
            </div>
            <button class="icon-button" type="button" data-close-dialog>×</button>
        </div>

        <div class="form-grid">
            <label>
                NIK
                <input
                    type="text"
                    name="nik"
                    inputmode="numeric"
                    pattern="[0-9]{16}"
                    maxlength="16"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    required>
            </label>
            <label>Nama<input name="nama" required></label>
            <label>Jabatan<input name="jabatan" required></label>
            <label>Email<input name="email" type="email" required></label>
            <label>Basic / UPPK<input name="gaji_pokok" type="number" min="0" required></label>
            <label>
                Status
                <select name="aktif">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </label>
        </div>

        <div class="dialog-actions">
            <button class="button" type="button" data-close-dialog>Batal</button>
            <button class="button primary">Simpan</button>
        </div>
    </form>
</dialog>
@endpush
