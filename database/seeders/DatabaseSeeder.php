<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\PeriodeGaji;
use App\Models\RekapKerja;
use App\Models\RitaseCoal;
use App\Models\TarifRitase;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $peran = collect([
            'admin' => 'Mengelola seluruh proses penggajian',
            'supervisor' => 'Menginput dan memverifikasi data operasional',
            'karyawan' => 'Melihat rekap dan slip gaji pribadi',
        ])->mapWithKeys(fn (string $deskripsi, string $nama) => [$nama => Peran::updateOrCreate(['nama' => $nama], ['deskripsi' => $deskripsi])]);

        foreach ([
            ['GJP003001', 'Taufik', 'Operator Alat Berat', 'karyawan@gjp.co.id', 4_200_679],
            ['GJP003002', 'Budi Santoso', 'Driver DT', 'budi@gjp.co.id', 4_000_000],
            ['GJP003003', 'Andi Setiawan', 'Helper', 'andi@gjp.co.id', 3_500_000],
            ['GJP003004', 'Ricky Maulana', 'Mechanic', 'ricky@gjp.co.id', 4_750_000],
            ['GJP003005', 'Dedi Kurniawan', 'Supervisor', 'supervisor@gjp.co.id', 6_000_000],
            ['GJP003006', 'Siti Nurhaliza', 'Admin', 'admin@gjp.co.id', 4_200_000],
            ['GJP003007', 'Agus Salim', 'Operator Excavator', 'agus@gjp.co.id', 4_400_000],
        ] as [$nik, $nama, $jabatan, $email, $gaji]) {
            Karyawan::updateOrCreate(['nik' => $nik], compact('nama', 'jabatan', 'email') + ['gaji_pokok' => $gaji, 'aktif' => true]);
        }

        foreach ([
            ['Admin HR', 'admin@gjp.co.id', 'admin', 'GJP003006'],
            ['Supervisor', 'supervisor@gjp.co.id', 'supervisor', 'GJP003005'],
            ['Taufik', 'karyawan@gjp.co.id', 'karyawan', 'GJP003001'],
        ] as [$nama, $email, $namaPeran, $nik]) {
            Pengguna::updateOrCreate(['email' => $email], [
                'name' => $nama, 'password' => 'password', 'status' => 'aktif',
                'peran_id' => $peran[$namaPeran]->id,
                'karyawan_id' => Karyawan::where('nik', $nik)->value('id'),
            ]);
        }

        $periode = PeriodeGaji::updateOrCreate(['bulan' => 6, 'tahun' => 2026], [
            'tanggal_mulai' => '2026-06-01', 'tanggal_selesai' => '2026-06-30', 'status' => 'aktif',
        ]);
        $taufik = Karyawan::where('nik', 'GJP003001')->firstOrFail();

        foreach ([
            ['2026-06-01', 8, 0, 7, 1, 0, 30_800, 38_000], ['2026-06-02', 8, 1, 0, 0, 0, 30_800, 38_000],
            ['2026-06-03', 8, 1, 0, 0, 0, 30_800, 38_000], ['2026-06-04', 8, 1, 0, 0, 0, 30_800, 38_000],
            ['2026-06-05', 4, 1, 3, 0, 0, 30_800, 38_000],
        ] as [$tanggal, $jam, $x15, $x2, $x3, $x4, $bbl, $ujk]) {
            $rekap = RekapKerja::where('karyawan_id', $taufik->id)->whereDate('tanggal', $tanggal)->first()
                ?? new RekapKerja(['karyawan_id' => $taufik->id, 'tanggal' => $tanggal]);
            $rekap->fill([
                'status' => 'hadir', 'total_jam' => $jam, 'x1_5' => $x15, 'x2' => $x2,
                'x3' => $x3, 'x4' => $x4, 'bbl' => $bbl, 'ujk' => $ujk,
                'diverifikasi_pada' => now(),
                'diverifikasi_oleh' => Pengguna::where('email', 'supervisor@gjp.co.id')->value('id'),
            ])->save();
        }

        foreach ([
            ['coal', 0, 120, 0, 'ritase'], ['coal', 121, 171, 12_000, 'ritase'], ['coal', 172, null, 30_000, 'ritase'],
            ['topsoil', 0, 2, 8_000, 'bcm'], ['topsoil', 2, 4, 9_000, 'bcm'], ['topsoil', 4, 6, 10_000, 'bcm'],
            ['topsoil', 6, 8, 11_000, 'bcm'], ['topsoil', 8, 10, 12_000, 'bcm'], ['topsoil', 10, 12, 13_000, 'bcm'],
        ] as [$jenis, $dari, $sampai, $tarif, $satuan]) {
            TarifRitase::updateOrCreate(['jenis' => $jenis, 'jarak_dari' => $dari, 'jarak_sampai' => $sampai], ['tarif' => $tarif, 'satuan' => $satuan, 'aktif' => true]);
        }

        $coal = RitaseCoal::where('karyawan_id', $taufik->id)->whereDate('tanggal', $periode->tanggal_mulai)->first()
            ?? new RitaseCoal(['karyawan_id' => $taufik->id, 'tanggal' => $periode->tanggal_mulai]);
        $coal->fill(['jumlah_ritase' => 157, 'total_ritase' => 37 * 12_000])->save();
    }
}
