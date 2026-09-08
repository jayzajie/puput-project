<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

final class ProfilController extends Controller
{
    public function index(Request $request): View
    {
        return view('profil', [
            'riwayatLogin' => DB::table('riwayat_login')
                ->where('pengguna_id', $request->user()->id)
                ->latest('masuk_pada')
                ->limit(20)
                ->get(),
        ]);
    }

    public function ubahFoto(Request $request): RedirectResponse
    {
        $request->validate([
            'foto_profil' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $pengguna = $request->user();
        $fotoLama = $pengguna->foto_profil;
        $pengguna->update(['foto_profil' => $request->file('foto_profil')->store('foto-profil', 'public')]);

        if ($fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        return back()->with('sukses', 'Foto profil berhasil diperbarui.');
    }

    public function ubahPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password_saat_ini' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password_saat_ini.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('sukses', 'Password berhasil diperbarui.');
    }
}
