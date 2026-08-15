<?php

declare(strict_types=1);

namespace App\Layanan;

use App\Models\Karyawan;
use App\Models\Payroll;
use App\Models\PeriodeGaji;
use App\Models\Potongan;
use App\Models\RekapKerja;
use App\Models\RitaseCoal;
use App\Models\RitaseTopsoil;
use App\Models\SlipGaji;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class KalkulatorPayroll
{
    public function hitung(Karyawan $karyawan, PeriodeGaji $periode): Payroll
    {
        return DB::transaction(function () use ($karyawan, $periode): Payroll {
            $rekap = RekapKerja::query()->where('karyawan_id', $karyawan->id)
                ->whereBetween('tanggal', [$periode->tanggal_mulai, $periode->tanggal_selesai])
                ->whereNotNull('diverifikasi_pada')->get();

            if ($rekap->isEmpty()) {
                throw ValidationException::withMessages(['rekap' => 'Rekap kerja belum diverifikasi.']);
            }

            $upahJam = $karyawan->gaji_pokok / 173;
            $lembur = (int) round($rekap->sum(fn (RekapKerja $item): float =>
                ((float) $item->x1_5 * 1.5 + (float) $item->x2 * 2 + (float) $item->x3 * 3 + (float) $item->x4 * 4) * $upahJam
            ));
            $coal = (int) RitaseCoal::query()->where('karyawan_id', $karyawan->id)->whereBetween('tanggal', [$periode->tanggal_mulai, $periode->tanggal_selesai])->sum('total_ritase');
            $topsoil = (int) RitaseTopsoil::query()->where('karyawan_id', $karyawan->id)->whereBetween('tanggal', [$periode->tanggal_mulai, $periode->tanggal_selesai])->sum('total_ritase');
            $tunjangan = (int) $rekap->sum(fn (RekapKerja $item): int => $item->bbl + $item->ujk);
            $potongan = (int) Potongan::query()->where('karyawan_id', $karyawan->id)->where('periode_gaji_id', $periode->id)->sum('nominal');
            $kotor = $karyawan->gaji_pokok + $lembur + $coal + $topsoil + $tunjangan;

            $payroll = Payroll::updateOrCreate(
                ['karyawan_id' => $karyawan->id, 'periode_gaji_id' => $periode->id],
                ['total_jam' => $rekap->sum('total_jam'), 'gaji_pokok' => $karyawan->gaji_pokok,
                    'total_lembur' => $lembur, 'total_ritase_coal' => $coal, 'total_ritase_topsoil' => $topsoil,
                    'total_tunjangan' => $tunjangan, 'total_potongan' => $potongan, 'gaji_kotor' => $kotor,
                    'gaji_bersih' => max(0, $kotor - $potongan), 'status' => 'draft'],
            );
            Potongan::where('karyawan_id', $karyawan->id)->where('periode_gaji_id', $periode->id)->update(['payroll_id' => $payroll->id]);

            return $payroll->load('karyawan', 'periode', 'potongan');
        });
    }

    public function finalisasi(Payroll $payroll): SlipGaji
    {
        return DB::transaction(function () use ($payroll): SlipGaji {
            $payroll->update(['status' => 'final', 'difinalisasi_pada' => now()]);
            return SlipGaji::updateOrCreate(['payroll_id' => $payroll->id], ['tanggal_cetak' => now(), 'status' => 'tersedia']);
        });
    }
}
