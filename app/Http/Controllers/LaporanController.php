<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Karyawan;
use App\Models\Payroll;
use App\Models\PeriodeGaji;
use App\Models\Potongan;
use App\Models\RekapKerja;
use App\Models\RitaseCoal;
use App\Models\RitaseTopsoil;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class LaporanController extends Controller
{
    public function index(): View { return view('laporan.index', ['laporan' => Laporan::latest()->paginate(10), 'periode' => PeriodeGaji::all()]); }

    public function ekspor(Request $request): StreamedResponse
    {
        $data = $request->validate(['periode_id' => ['required', 'exists:periode_gaji,id'], 'jenis' => ['required', 'in:payroll,lembur,ritase,potongan,karyawan']]);
        $periode = PeriodeGaji::findOrFail($data['periode_id']);
        Laporan::create(['nama_laporan' => 'Laporan '.ucfirst($data['jenis']), 'parameter' => $data, 'periode_dari' => $periode->tanggal_mulai, 'periode_sampai' => $periode->tanggal_selesai, 'dibuat_oleh' => $request->user()->id]);
        [$kepala, $baris] = $this->dataLaporan($data['jenis'], $periode);
        return response()->streamDownload(function () use ($kepala, $baris): void {
            $csv = fopen('php://output', 'w');
            fputcsv($csv, $kepala);
            foreach ($baris as $item) { fputcsv($csv, $item); }
            fclose($csv);
        }, "laporan-{$data['jenis']}-".str_replace('/', '-', $periode->label()).'.csv', ['Content-Type' => 'text/csv']);
    }

    public function hapus(Laporan $laporan): RedirectResponse
    {
        $laporan->delete();
        return back()->with('sukses', 'Riwayat laporan dihapus.');
    }

    private function dataLaporan(string $jenis, PeriodeGaji $periode): array
    {
        $rentang = [$periode->tanggal_mulai, $periode->tanggal_selesai];
        return match ($jenis) {
            'payroll' => [['NIK', 'Nama', 'Gaji Kotor', 'Potongan', 'Gaji Bersih', 'Status'], Payroll::with('karyawan')->where('periode_gaji_id', $periode->id)->get()->map(fn ($item) => [$item->karyawan->nik, $item->karyawan->nama, $item->gaji_kotor, $item->total_potongan, $item->gaji_bersih, $item->status])],
            'lembur' => [['Tanggal', 'NIK', 'Nama', 'x1.5', 'x2', 'x3', 'x4'], RekapKerja::with('karyawan')->whereBetween('tanggal', $rentang)->get()->map(fn ($item) => [$item->tanggal->format('Y-m-d'), $item->karyawan->nik, $item->karyawan->nama, $item->x1_5, $item->x2, $item->x3, $item->x4])],
            'ritase' => [['Jenis', 'Tanggal', 'NIK', 'Nama', 'Volume', 'Total'], RitaseCoal::with('karyawan')->whereBetween('tanggal', $rentang)->get()->map(fn ($item) => ['Coal', $item->tanggal->format('Y-m-d'), $item->karyawan->nik, $item->karyawan->nama, $item->jumlah_ritase, $item->total_ritase])->concat(RitaseTopsoil::with('karyawan')->whereBetween('tanggal', $rentang)->get()->map(fn ($item) => ['Topsoil', $item->tanggal->format('Y-m-d'), $item->karyawan->nik, $item->karyawan->nama, $item->bcm, $item->total_ritase]))],
            'potongan' => [['NIK', 'Nama', 'Jenis', 'Nominal'], Potongan::with('karyawan')->where('periode_gaji_id', $periode->id)->get()->map(fn ($item) => [$item->karyawan->nik, $item->karyawan->nama, $item->jenis_potongan, $item->nominal])],
            'karyawan' => [['NIK', 'Nama', 'Jabatan', 'Email', 'Gaji Pokok', 'Status'], Karyawan::all()->map(fn ($item) => [$item->nik, $item->nama, $item->jabatan, $item->email, $item->gaji_pokok, $item->aktif ? 'Aktif' : 'Nonaktif'])],
        };
    }
}
