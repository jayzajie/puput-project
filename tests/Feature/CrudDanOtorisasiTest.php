<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\Laporan;
use App\Models\Payroll;
use App\Models\Pengguna;
use App\Models\PeriodeGaji;
use App\Models\Potongan;
use App\Models\RekapKerja;
use App\Models\RitaseCoal;
use App\Models\RitaseTopsoil;
use App\Models\SlipGaji;
use App\Models\TarifRitase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CrudDanOtorisasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_mengelola_karyawan_dan_relasi_akunnya(): void
    {
        $this->seed();
        $admin = Pengguna::where('email', 'admin@gjp.co.id')->firstOrFail();

        $this->actingAs($admin)->post(route('karyawan.simpan'), [
            'nik' => 'GJP009999', 'nama' => 'QA Karyawan', 'jabatan' => 'Tester',
            'email' => 'qa-karyawan@gjp.co.id', 'gaji_pokok' => 5_000_000, 'aktif' => 1,
        ])->assertSessionHas('sukses');

        $karyawan = Karyawan::where('nik', 'GJP009999')->firstOrFail();
        $this->assertSame('karyawan', $karyawan->pengguna?->peran->nama);

        $this->put(route('karyawan.ubah', $karyawan), [
            'nik' => 'GJP009999', 'nama' => 'QA Karyawan Ubah', 'jabatan' => 'QA Lead',
            'email' => 'qa-ubah@gjp.co.id', 'gaji_pokok' => 5_500_000, 'aktif' => 0,
        ])->assertSessionHas('sukses');
        $this->assertDatabaseHas('users', ['karyawan_id' => $karyawan->id, 'email' => 'qa-ubah@gjp.co.id', 'status' => 'nonaktif']);

        $this->delete(route('karyawan.hapus', $karyawan))->assertSessionHas('sukses');
        $this->assertDatabaseMissing('karyawan', ['id' => $karyawan->id]);
        $this->assertDatabaseMissing('users', ['karyawan_id' => $karyawan->id]);
    }

    public function test_supervisor_mengelola_rekap_dan_ritase_tetapi_tidak_melihat_payroll(): void
    {
        $this->seed();
        $supervisor = Pengguna::where('email', 'supervisor@gjp.co.id')->firstOrFail();
        $karyawan = Karyawan::where('nik', 'GJP003001')->firstOrFail();

        $this->actingAs($supervisor)->post(route('rekap.simpan'), [
            'karyawan_id' => $karyawan->id, 'tanggal' => '2026-06-20', 'status' => 'hadir',
            'total_jam' => 8, 'x1_5' => 1, 'x2' => 0, 'x3' => 0, 'x4' => 0,
            'bbl' => 0, 'ujk' => 0, 'keterangan' => 'QA',
        ])->assertSessionHas('sukses');
        $rekap = RekapKerja::whereDate('tanggal', '2026-06-20')->firstOrFail();
        $this->patch(route('rekap.verifikasi', $rekap))->assertSessionHas('sukses');
        $this->delete(route('rekap.hapus', $rekap))->assertSessionHas('sukses');
        $this->assertDatabaseMissing('rekap_kerja', ['id' => $rekap->id]);

        $this->post(route('ritase.coal'), ['karyawan_id' => $karyawan->id, 'tanggal' => '2026-06-20', 'jumlah_ritase' => 130])->assertSessionHas('sukses');
        $coal = RitaseCoal::whereDate('tanggal', '2026-06-20')->firstOrFail();
        $this->delete(route('ritase.coal.hapus', $coal))->assertSessionHas('sukses');
        $this->assertDatabaseMissing('ritase_coal', ['id' => $coal->id]);

        $this->post(route('ritase.topsoil'), ['karyawan_id' => $karyawan->id, 'tanggal' => '2026-06-20', 'jarak_km' => 5, 'bcm' => 12, 'jumlah_ritase' => 3, 'bonus' => 10_000])->assertSessionHas('sukses');
        $topsoil = RitaseTopsoil::whereDate('tanggal', '2026-06-20')->firstOrFail();
        $this->delete(route('ritase.topsoil.hapus', $topsoil))->assertSessionHas('sukses');
        $this->assertDatabaseMissing('ritase_topsoil', ['id' => $topsoil->id]);

        $this->get(route('payroll.index'))->assertForbidden();

        foreach (range(1, 6) as $index) {
            Karyawan::create([
                'nik' => 'GJP99000'.$index,
                'nama' => 'Karyawan Tambahan '.$index,
                'jabatan' => 'Operator',
                'email' => "tambahan{$index}@gjp.co.id",
                'gaji_pokok' => 4_000_000,
                'aktif' => true,
            ]);
        }

        $response = $this->get(route('dasbor'))->assertOk()->assertSee('Daftar Karyawan');
        $paginator = $response->viewData('daftarKaryawan');
        $this->assertSame(10, $paginator->perPage());
        $this->assertCount(10, $paginator->items());
        $this->assertSame(13, $paginator->total());
    }

    public function test_admin_mengelola_pengaturan_payroll_slip_dan_laporan(): void
    {
        $this->seed();
        $admin = Pengguna::where('email', 'admin@gjp.co.id')->firstOrFail();
        $karyawan = Karyawan::where('nik', 'GJP003001')->firstOrFail();
        $periode = PeriodeGaji::where('bulan', 6)->where('tahun', 2026)->firstOrFail();
        $this->actingAs($admin);

        $this->post(route('tarif.simpan'), ['jenis' => 'topsoil', 'jarak_dari' => 20, 'jarak_sampai' => 30, 'tarif' => 15_000, 'satuan' => 'bcm', 'aktif' => 1])->assertSessionHas('sukses');
        $tarif = TarifRitase::where('jarak_dari', 20)->firstOrFail();
        $this->put(route('tarif.ubah', $tarif), ['jenis' => 'coal', 'jarak_dari' => 200, 'jarak_sampai' => 250, 'tarif' => 25_000, 'satuan' => 'ritase', 'aktif' => 0])->assertSessionHas('sukses');
        $this->assertDatabaseHas('tarif_ritase', ['id' => $tarif->id, 'jenis' => 'coal', 'tarif' => 25_000, 'aktif' => 0]);
        $this->delete(route('tarif.hapus', $tarif))->assertSessionHas('sukses');
        $this->assertDatabaseMissing('tarif_ritase', ['id' => $tarif->id]);

        $this->post(route('periode.simpan'), ['bulan' => 7, 'tahun' => 2026, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-07-31'])->assertSessionHas('sukses');
        $periodeBaru = PeriodeGaji::where('bulan', 7)->firstOrFail();
        $this->put(route('periode.ubah', $periodeBaru), ['bulan' => 8, 'tahun' => 2026, 'tanggal_mulai' => '2026-08-01', 'tanggal_selesai' => '2026-08-31', 'status' => 'draft'])->assertSessionHas('sukses');
        $this->assertDatabaseHas('periode_gaji', ['id' => $periodeBaru->id, 'bulan' => 8, 'status' => 'draft']);
        $this->delete(route('periode.hapus', $periodeBaru))->assertSessionHas('sukses');
        $this->assertDatabaseMissing('periode_gaji', ['id' => $periodeBaru->id]);

        $this->post(route('potongan.simpan'), ['karyawan_id' => $karyawan->id, 'periode_gaji_id' => $periode->id, 'jenis_potongan' => 'BPJS', 'nominal' => 100_000])->assertSessionHas('sukses');
        $this->post(route('payroll.hitung'), ['karyawan_id' => $karyawan->id, 'periode_id' => $periode->id])->assertRedirect();
        $payroll = Payroll::whereBelongsTo($karyawan)->firstOrFail();
        $potongan = Potongan::where('karyawan_id', $karyawan->id)->firstOrFail();
        $this->put(route('potongan.ubah', $potongan), ['jenis_potongan' => 'BPJS Kesehatan', 'nominal' => 125_000, 'deskripsi' => 'QA'])->assertSessionHas('sukses');
        $this->patch(route('payroll.finalisasi', $payroll))->assertSessionHas('sukses');
        $slip = SlipGaji::where('payroll_id', $payroll->id)->firstOrFail();
        $this->put(route('potongan.ubah', $potongan), ['jenis_potongan' => 'Tidak boleh', 'nominal' => 1, 'deskripsi' => null])->assertUnprocessable();

        $this->post(route('laporan.ekspor'), ['periode_id' => $periode->id, 'jenis' => 'payroll'])->assertDownload();
        $laporan = Laporan::firstOrFail();
        $this->delete(route('laporan.hapus', $laporan))->assertSessionHas('sukses');
        $this->assertDatabaseMissing('laporan', ['id' => $laporan->id]);

        $this->delete(route('slip.hapus', $slip))->assertSessionHas('sukses');
        $this->assertSame('draft', $payroll->fresh()->status);
        $this->delete(route('payroll.hapus', $payroll))->assertSessionHas('sukses');
        $this->assertDatabaseMissing('payroll', ['id' => $payroll->id]);
    }

    public function test_karyawan_hanya_melihat_rekap_dan_slip_milik_sendiri(): void
    {
        $this->seed();
        $admin = Pengguna::where('email', 'admin@gjp.co.id')->firstOrFail();
        $penggunaKaryawan = Pengguna::where('email', 'karyawan@gjp.co.id')->firstOrFail();
        $supervisor = Pengguna::where('email', 'supervisor@gjp.co.id')->firstOrFail();
        $periode = PeriodeGaji::firstOrFail();

        $this->actingAs($admin)->post(route('payroll.hitung'), ['karyawan_id' => $penggunaKaryawan->karyawan_id, 'periode_id' => $periode->id]);
        $payrollSendiri = Payroll::where('karyawan_id', $penggunaKaryawan->karyawan_id)->firstOrFail();
        $this->patch(route('payroll.finalisasi', $payrollSendiri));

        $payrollLain = Payroll::create([
            'karyawan_id' => $supervisor->karyawan_id, 'periode_gaji_id' => $periode->id,
            'total_jam' => 8, 'gaji_pokok' => 6_000_000, 'total_lembur' => 0,
            'total_ritase_coal' => 0, 'total_ritase_topsoil' => 0, 'total_tunjangan' => 0,
            'total_potongan' => 0, 'gaji_kotor' => 6_000_000, 'gaji_bersih' => 6_000_000,
            'status' => 'final', 'difinalisasi_pada' => now(),
        ]);
        $slipLain = SlipGaji::create(['payroll_id' => $payrollLain->id, 'tanggal_cetak' => now(), 'status' => 'tersedia']);

        $periodeKedua = PeriodeGaji::create([
            'bulan' => 5, 'tahun' => 2026,
            'tanggal_mulai' => '2026-05-01', 'tanggal_selesai' => '2026-05-31',
            'status' => 'selesai',
        ]);

        $this->actingAs($penggunaKaryawan)->get(route('dasbor'))->assertOk()->assertSee('Taufik')->assertDontSee('Supervisor')->assertSee('05/2026');
        $this->get(route('dasbor', ['periode_id' => $periodeKedua->id]))->assertOk()->assertViewHas('periode', fn ($item) => $item->is($periodeKedua));
        $this->get(route('rekap.index', ['karyawan_id' => $supervisor->karyawan_id, 'periode_id' => $periodeKedua->id]))->assertOk()->assertDontSee('Dedi Kurniawan')->assertViewHas('periodeAktif', fn ($item) => $item->is($periodeKedua));
        $this->get(route('payroll.index', ['periode_id' => $periode->id]))->assertOk()->assertSee('Lihat / Cetak Slip Gaji');
        $this->get(route('slip.show', $payrollSendiri->slipGaji))->assertOk();
        $this->get(route('slip.show', $slipLain))->assertForbidden();
        $this->get(route('ritase.index'))->assertForbidden();
        $this->get(route('pengaturan.index'))->assertForbidden();
    }
}
