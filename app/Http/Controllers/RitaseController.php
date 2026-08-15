<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Layanan\KalkulatorRitase;
use App\Models\Karyawan;
use App\Models\PeriodeGaji;
use App\Models\RitaseCoal;
use App\Models\RitaseTopsoil;
use App\Models\TarifRitase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

final class RitaseController extends Controller
{
    public function __construct(private readonly KalkulatorRitase $kalkulator) {}

    public function index(Request $request): View
    {
        $karyawan = Karyawan::find($request->integer('karyawan_id')) ?? Karyawan::firstOrFail();
        $periode = PeriodeGaji::find($request->integer('periode_id')) ?? PeriodeGaji::latest('tahun')->latest('bulan')->firstOrFail();
        return view('ritase.index', [
            'karyawanAktif' => $karyawan, 'periodeAktif' => $periode,
            'daftarKaryawan' => Karyawan::where('aktif', true)->get(), 'daftarPeriode' => PeriodeGaji::all(),
            'coal' => RitaseCoal::where('karyawan_id', $karyawan->id)->whereBetween('tanggal', [$periode->tanggal_mulai, $periode->tanggal_selesai])->get(),
            'topsoil' => RitaseTopsoil::where('karyawan_id', $karyawan->id)->whereBetween('tanggal', [$periode->tanggal_mulai, $periode->tanggal_selesai])->get(),
            'tarifCoal' => TarifRitase::where('jenis', 'coal')->orderBy('jarak_dari')->get(),
            'tarifTopsoil' => TarifRitase::where('jenis', 'topsoil')->orderBy('jarak_dari')->get(),
        ]);
    }

    public function simpanCoal(Request $request): RedirectResponse
    {
        $data = $request->validate(['karyawan_id' => ['required', 'exists:karyawan,id'], 'tanggal' => ['required', 'date'], 'jumlah_ritase' => ['required', 'integer', 'min:0']]);
        $data['tanggal'] = Carbon::parse($data['tanggal'])->startOfDay();
        RitaseCoal::updateOrCreate(['karyawan_id' => $data['karyawan_id'], 'tanggal' => $data['tanggal']], $data + ['total_ritase' => $this->kalkulator->coal((int) $data['jumlah_ritase'])]);
        return back()->with('sukses', 'Ritase coal disimpan.');
    }

    public function simpanTopsoil(Request $request): RedirectResponse
    {
        $data = $request->validate(['karyawan_id' => ['required', 'exists:karyawan,id'], 'tanggal' => ['required', 'date'], 'jarak_km' => ['required', 'numeric', 'min:0', 'max:12'], 'bcm' => ['required', 'numeric', 'min:0'], 'jumlah_ritase' => ['required', 'integer', 'min:0'], 'bonus' => ['nullable', 'integer', 'min:0']]);
        $data['tanggal'] = Carbon::parse($data['tanggal'])->startOfDay();
        $bonus = (int) ($data['bonus'] ?? 0);
        RitaseTopsoil::updateOrCreate(['karyawan_id' => $data['karyawan_id'], 'tanggal' => $data['tanggal']], $data + ['bonus' => $bonus, 'total_ritase' => $this->kalkulator->topsoil((float) $data['jarak_km'], (float) $data['bcm']) + $bonus]);
        return back()->with('sukses', 'Ritase topsoil disimpan.');
    }

    public function hapusCoal(RitaseCoal $ritaseCoal): RedirectResponse
    {
        $ritaseCoal->delete();
        return back()->with('sukses', 'Ritase coal dihapus.');
    }

    public function hapusTopsoil(RitaseTopsoil $ritaseTopsoil): RedirectResponse
    {
        $ritaseTopsoil->delete();
        return back()->with('sukses', 'Ritase topsoil dihapus.');
    }
}
