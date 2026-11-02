<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Layanan\KalkulatorPayroll;
use App\Models\Karyawan;
use App\Models\Payroll;
use App\Models\PeriodeGaji;
use App\Models\Potongan;
use App\Models\SlipGaji;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class PayrollController extends Controller
{
    public function __construct(private readonly KalkulatorPayroll $kalkulator) {}

    public function index(Request $request): View
    {
        $pengguna = $request->user();
        $karyawan = $pengguna->berperanSebagai('karyawan') ? $pengguna->karyawan : Karyawan::find($request->integer('karyawan_id')) ?? Karyawan::first();
        $periode = PeriodeGaji::find($request->integer('periode_id')) ?? PeriodeGaji::latest('tahun')->latest('bulan')->firstOrFail();
        $payroll = Payroll::with(['karyawan', 'periode', 'potongan', 'slipGaji'])->where('karyawan_id', $karyawan->id)->where('periode_gaji_id', $periode->id)->first();
        if ($pengguna->berperanSebagai('karyawan') && $payroll?->status !== 'final') { $payroll = null; }
        return view('payroll.index', ['payroll' => $payroll, 'karyawanAktif' => $karyawan, 'periodeAktif' => $periode, 'daftarKaryawan' => Karyawan::where('aktif', true)->get(), 'daftarPeriode' => PeriodeGaji::orderByDesc('tahun')->orderByDesc('bulan')->get()]);
    }

    public function hitung(Request $request): RedirectResponse
    {
        $data = $request->validate(['karyawan_id' => ['required', 'exists:karyawan,id'], 'periode_id' => ['required', 'exists:periode_gaji,id']]);
        $payroll = $this->kalkulator->hitung(Karyawan::findOrFail($data['karyawan_id']), PeriodeGaji::findOrFail($data['periode_id']));
        return redirect()->route('payroll.index', ['karyawan_id' => $payroll->karyawan_id, 'periode_id' => $payroll->periode_gaji_id])->with('sukses', 'Payroll berhasil dihitung.');
    }

    public function finalisasi(Payroll $payroll): RedirectResponse
    {
        $this->kalkulator->finalisasi($payroll);
        return back()->with('sukses', 'Payroll difinalisasi dan slip dipublikasikan.');
    }

    public function tambahPotongan(Request $request): RedirectResponse
    {
        $data = $request->validate(['karyawan_id' => ['required', 'exists:karyawan,id'], 'periode_gaji_id' => ['required', 'exists:periode_gaji,id'], 'jenis_potongan' => ['required', 'string', 'max:100'], 'deskripsi' => ['nullable', 'string', 'max:255'], 'nominal' => ['required', 'integer', 'min:0']]);
        Potongan::create($data);
        return back()->with('sukses', 'Potongan ditambahkan. Hitung ulang payroll untuk menerapkannya.');
    }

    public function ubahPotongan(Request $request, Potongan $potongan): RedirectResponse
    {
        abort_if($potongan->payroll?->status === 'final', 422, 'Potongan payroll final tidak dapat diubah.');
        $potongan->update($request->validate(['jenis_potongan' => ['required', 'string', 'max:100'], 'deskripsi' => ['nullable', 'string', 'max:255'], 'nominal' => ['required', 'integer', 'min:0']]));
        return back()->with('sukses', 'Potongan diperbarui. Hitung ulang payroll untuk menerapkannya.');
    }

    public function hapusPotongan(Potongan $potongan): RedirectResponse
    {
        abort_if($potongan->payroll?->status === 'final', 422, 'Potongan payroll final tidak dapat dihapus.');
        $potongan->delete();
        return back()->with('sukses', 'Potongan dihapus.');
    }

    public function slip(Request $request, SlipGaji $slipGaji): View
    {
        $slipGaji->load('payroll.karyawan', 'payroll.periode', 'payroll.potongan');
        abort_if($request->user()->berperanSebagai('karyawan') && $request->user()->karyawan_id !== $slipGaji->payroll->karyawan_id, 403);
        return view('payroll.slip', ['slip' => $slipGaji]);
    }

    public function hapusSlip(SlipGaji $slipGaji): RedirectResponse
    {
        DB::transaction(function () use ($slipGaji): void {
            $slipGaji->payroll->update(['status' => 'draft', 'difinalisasi_pada' => null]);
            $slipGaji->delete();
        });
        return redirect()->route('payroll.index')->with('sukses', 'Slip dihapus dan payroll dikembalikan ke draft.');
    }

    public function hapusPayroll(Payroll $payroll): RedirectResponse
    {
        $payroll->delete();
        return redirect()->route('payroll.index')->with('sukses', 'Payroll beserta slip terkait dihapus.');
    }
}
