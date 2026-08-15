<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\PeriodeGaji;
use App\Models\RekapKerja;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RekapKerjaController extends Controller
{
    public function index(Request $request): View
    {
        $pengguna = $request->user();
        $karyawan = $pengguna->berperanSebagai('karyawan') ? $pengguna->karyawan : Karyawan::find($request->integer('karyawan_id')) ?? Karyawan::first();
        $periode = PeriodeGaji::find($request->integer('periode_id')) ?? PeriodeGaji::latest('tahun')->latest('bulan')->firstOrFail();
        $rekap = RekapKerja::where('karyawan_id', $karyawan->id)->whereBetween('tanggal', [$periode->tanggal_mulai, $periode->tanggal_selesai])->orderBy('tanggal')->get();
        return view('rekap.index', ['rekap' => $rekap, 'karyawanAktif' => $karyawan, 'periodeAktif' => $periode, 'daftarKaryawan' => Karyawan::where('aktif', true)->get(), 'daftarPeriode' => PeriodeGaji::orderByDesc('tahun')->orderByDesc('bulan')->get()]);
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $request->validate(['karyawan_id' => ['required', 'exists:karyawan,id'], 'tanggal' => ['required', 'date'], 'status' => ['required', 'in:hadir,izin,sakit,mangkir,standby'], 'total_jam' => ['required', 'numeric', 'min:0', 'max:24'], 'x1_5' => ['required', 'numeric', 'min:0'], 'x2' => ['required', 'numeric', 'min:0'], 'x3' => ['required', 'numeric', 'min:0'], 'x4' => ['required', 'numeric', 'min:0'], 'bbl' => ['required', 'integer', 'min:0'], 'ujk' => ['required', 'integer', 'min:0'], 'keterangan' => ['nullable', 'string', 'max:255']]);
        $data['tanggal'] = Carbon::parse($data['tanggal'])->startOfDay();
        RekapKerja::updateOrCreate(['karyawan_id' => $data['karyawan_id'], 'tanggal' => $data['tanggal']], $data + ['diverifikasi_pada' => null, 'diverifikasi_oleh' => null]);
        return back()->with('sukses', 'Rekap kerja disimpan dan menunggu verifikasi.');
    }

    public function verifikasi(Request $request, RekapKerja $rekapKerja): RedirectResponse
    {
        $rekapKerja->update(['diverifikasi_pada' => now(), 'diverifikasi_oleh' => $request->user()->id]);
        return back()->with('sukses', 'Rekap kerja diverifikasi.');
    }

    public function hapus(RekapKerja $rekapKerja): RedirectResponse
    {
        $rekapKerja->delete();
        return back()->with('sukses', 'Rekap kerja dihapus.');
    }
}
