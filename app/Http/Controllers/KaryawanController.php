<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Pengguna;
use App\Models\Peran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class KaryawanController extends Controller
{
    public function index(Request $request): View
    {
        $cari = $request->string('cari')->toString();
        $karyawan = Karyawan::query()->when($cari, fn ($query) => $query->where(fn ($sub) => $sub->where('nama', 'like', "%{$cari}%")->orWhere('nik', 'like', "%{$cari}%")))->latest()->paginate(10)->withQueryString();
        return view('karyawan.index', compact('karyawan', 'cari'));
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturan());
        DB::transaction(function () use ($data): void {
            $karyawan = Karyawan::create($data);
            Pengguna::create(['name' => $karyawan->nama, 'email' => $karyawan->email, 'password' => 'password', 'status' => 'aktif', 'karyawan_id' => $karyawan->id, 'peran_id' => Peran::where('nama', 'karyawan')->value('id')]);
        });
        return back()->with('sukses', 'Karyawan berhasil ditambahkan. Password awal: password');
    }

    public function ubah(Request $request, Karyawan $karyawan): RedirectResponse
    {
        $data = $request->validate($this->aturan($karyawan));
        DB::transaction(function () use ($data, $karyawan): void {
            $karyawan->update($data);
            $karyawan->pengguna?->update(['name' => $karyawan->nama, 'email' => $karyawan->email, 'status' => $karyawan->aktif ? 'aktif' : 'nonaktif']);
        });
        return back()->with('sukses', 'Data karyawan diperbarui.');
    }

    public function hapus(Karyawan $karyawan): RedirectResponse
    {
        DB::transaction(function () use ($karyawan): void { $karyawan->pengguna?->delete(); $karyawan->delete(); });
        return back()->with('sukses', 'Karyawan berhasil dihapus.');
    }

    private function aturan(?Karyawan $karyawan = null): array
    {
        return [
            'nik' => ['required', 'string', 'max:30', Rule::unique('karyawan')->ignore($karyawan)],
            'nama' => ['required', 'string', 'max:100'], 'jabatan' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('karyawan')->ignore($karyawan)],
            'gaji_pokok' => ['required', 'integer', 'min:0'], 'aktif' => ['required', 'boolean'],
        ];
    }
}
