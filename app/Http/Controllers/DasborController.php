<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Payroll;
use App\Models\PeriodeGaji;
use App\Models\RekapKerja;
use App\Models\RitaseCoal;
use App\Models\RitaseTopsoil;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DasborController extends Controller
{
    public function __invoke(Request $request): View
    {
        $pengguna = $request->user();
        $karyawanId = $pengguna->berperanSebagai('karyawan') ? $pengguna->karyawan_id : null;
        $periode = PeriodeGaji::latest('tahun')->latest('bulan')->first();
        $payroll = Payroll::with(['karyawan', 'periode'])
            ->when($pengguna->berperanSebagai('karyawan'), fn ($query) => $query->where('karyawan_id', $karyawanId)->where('status', 'final'))
            ->when($pengguna->berperanSebagai('supervisor'), fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()->take(6)->get();
        $rekap = RekapKerja::query()->when($karyawanId, fn ($query) => $query->where('karyawan_id', $karyawanId));
        $coal = RitaseCoal::query()->when($karyawanId, fn ($query) => $query->where('karyawan_id', $karyawanId));
        $topsoil = RitaseTopsoil::query()->when($karyawanId, fn ($query) => $query->where('karyawan_id', $karyawanId));
        return view('dasbor', [
            'periode' => $periode,
            'payroll' => $payroll,
            'mode' => $pengguna->peran->nama,
            'jumlahKaryawan' => $karyawanId ? 1 : Karyawan::where('aktif', true)->count(),
            'totalGaji' => $payroll->sum('gaji_kotor'),
            'totalLembur' => (clone $rekap)->sum('x1_5') + (clone $rekap)->sum('x2') + (clone $rekap)->sum('x3') + (clone $rekap)->sum('x4'),
            'totalCoal' => $coal->sum('jumlah_ritase'),
            'totalTopsoil' => $topsoil->sum('bcm'),
        ]);
    }
}
