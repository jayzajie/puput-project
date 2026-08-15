<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Payroll;
use App\Models\Pengguna;
use App\Models\PeriodeGaji;
use App\Models\RekapKerja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AlurPenggajianTest extends TestCase
{
    use RefreshDatabase;

    public function test_hak_akses_tiga_peran_dibatasi(): void
    {
        $this->seed();
        $karyawan = Pengguna::where('email', 'karyawan@gjp.co.id')->firstOrFail();
        $supervisor = Pengguna::where('email', 'supervisor@gjp.co.id')->firstOrFail();

        $this->actingAs($karyawan)->get(route('karyawan.index'))->assertForbidden();
        $this->actingAs($karyawan)->get(route('rekap.index'))->assertOk()->assertSee('Rekap Kerja & Lembur', false);
        $this->actingAs($supervisor)->get(route('ritase.index'))->assertOk();
        $this->actingAs($supervisor)->get(route('laporan.index'))->assertForbidden();
    }

    public function test_supervisor_dapat_input_dan_verifikasi_rekap(): void
    {
        $this->seed();
        $supervisor = Pengguna::where('email', 'supervisor@gjp.co.id')->firstOrFail();
        $karyawan = Karyawan::where('nik', 'GJP003001')->firstOrFail();

        $this->actingAs($supervisor)->post(route('rekap.simpan'), [
            'karyawan_id' => $karyawan->id, 'tanggal' => '2026-06-10', 'status' => 'hadir',
            'total_jam' => 8, 'x1_5' => 1, 'x2' => 0, 'x3' => 0, 'x4' => 0,
            'bbl' => 10_000, 'ujk' => 20_000, 'keterangan' => null,
        ])->assertSessionHas('sukses');

        $rekap = RekapKerja::whereDate('tanggal', '2026-06-10')->firstOrFail();
        $this->assertNull($rekap->diverifikasi_pada);
        $this->actingAs($supervisor)->patch(route('rekap.verifikasi', $rekap))->assertSessionHas('sukses');
        $this->assertNotNull($rekap->fresh()->diverifikasi_pada);
    }

    public function test_admin_menghitung_finalisasi_dan_karyawan_melihat_slip(): void
    {
        $this->seed();
        $admin = Pengguna::where('email', 'admin@gjp.co.id')->firstOrFail();
        $penggunaKaryawan = Pengguna::where('email', 'karyawan@gjp.co.id')->firstOrFail();
        $karyawan = $penggunaKaryawan->karyawan;
        $periode = PeriodeGaji::where('bulan', 6)->where('tahun', 2026)->firstOrFail();

        $this->actingAs($admin)->post(route('potongan.simpan'), [
            'karyawan_id' => $karyawan->id, 'periode_gaji_id' => $periode->id,
            'jenis_potongan' => 'BPJS', 'nominal' => 176_707,
        ])->assertSessionHas('sukses');
        $this->post(route('payroll.hitung'), ['karyawan_id' => $karyawan->id, 'periode_id' => $periode->id])->assertRedirect();

        $payroll = Payroll::where('karyawan_id', $karyawan->id)->firstOrFail();
        $this->assertSame('draft', $payroll->status);
        $this->assertGreaterThan($karyawan->gaji_pokok, $payroll->gaji_kotor);
        $this->assertSame(176_707, $payroll->total_potongan);

        $this->patch(route('payroll.finalisasi', $payroll))->assertSessionHas('sukses');
        $slip = $payroll->fresh()->slipGaji;
        $this->assertNotNull($slip);
        $this->post(route('laporan.ekspor'), ['periode_id' => $periode->id, 'jenis' => 'payroll'])
            ->assertDownload('laporan-payroll-06-2026.csv');
        $this->actingAs($penggunaKaryawan)->get(route('slip.show', $slip))->assertOk()->assertSee('TOTAL GAJI BERSIH');
    }
}
