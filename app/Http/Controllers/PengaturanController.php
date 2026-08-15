<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PeriodeGaji;
use App\Models\TarifRitase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class PengaturanController extends Controller
{
    public function index(): View { return view('pengaturan.index', ['tarif' => TarifRitase::orderBy('jenis')->orderBy('jarak_dari')->get(), 'periode' => PeriodeGaji::orderByDesc('tahun')->orderByDesc('bulan')->get()]); }

    public function simpanTarif(Request $request, TarifRitase $tarifRitase): RedirectResponse
    {
        $data = $request->validate(['jenis' => ['required', 'in:coal,topsoil'], 'jarak_dari' => ['nullable', 'numeric', 'min:0'], 'jarak_sampai' => ['nullable', 'numeric', 'gte:jarak_dari'], 'tarif' => ['required', 'integer', 'min:0'], 'satuan' => ['required', 'in:ritase,bcm'], 'aktif' => ['required', 'boolean']]);
        $tarifRitase->update($data);
        return back()->with('sukses', 'Tarif diperbarui.');
    }

    public function tambahTarif(Request $request): RedirectResponse
    {
        TarifRitase::create($request->validate(['jenis' => ['required', 'in:coal,topsoil'], 'jarak_dari' => ['nullable', 'numeric', 'min:0'], 'jarak_sampai' => ['nullable', 'numeric', 'gte:jarak_dari'], 'tarif' => ['required', 'integer', 'min:0'], 'satuan' => ['required', 'in:ritase,bcm'], 'aktif' => ['required', 'boolean']]));
        return back()->with('sukses', 'Tarif ditambahkan.');
    }

    public function hapusTarif(TarifRitase $tarifRitase): RedirectResponse
    {
        $tarifRitase->delete();
        return back()->with('sukses', 'Tarif dihapus.');
    }

    public function simpanPeriode(Request $request): RedirectResponse
    {
        $data = $request->validate(['bulan' => ['required', 'integer', 'between:1,12'], 'tahun' => ['required', 'integer', 'between:2020,2100'], 'tanggal_mulai' => ['required', 'date'], 'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai']]);
        PeriodeGaji::updateOrCreate(['bulan' => $data['bulan'], 'tahun' => $data['tahun']], $data + ['status' => 'aktif']);
        return back()->with('sukses', 'Periode gaji disimpan.');
    }

    public function ubahPeriode(Request $request, PeriodeGaji $periodeGaji): RedirectResponse
    {
        $periodeGaji->update($request->validate([
            'bulan' => ['required', 'integer', 'between:1,12', Rule::unique('periode_gaji')->where(fn ($query) => $query->where('tahun', $request->integer('tahun')))->ignore($periodeGaji->id)],
            'tahun' => ['required', 'integer', 'between:2020,2100'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:draft,aktif,selesai'],
        ]));
        return back()->with('sukses', 'Periode gaji diperbarui.');
    }

    public function hapusPeriode(PeriodeGaji $periodeGaji): RedirectResponse
    {
        if ($periodeGaji->payroll()->exists()) {
            throw ValidationException::withMessages(['periode' => 'Periode yang sudah memiliki payroll tidak dapat dihapus.']);
        }
        $periodeGaji->delete();
        return back()->with('sukses', 'Periode gaji dihapus.');
    }
}
