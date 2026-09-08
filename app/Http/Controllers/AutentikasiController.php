<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class AutentikasiController extends Controller
{
    public function formulir(): View { return view('autentikasi.login'); }

    public function masuk(Request $request): RedirectResponse
    {
        $kredensial = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt($kredensial + ['status' => 'aktif'], $request->boolean('ingat'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        $request->user()->update(['terakhir_login' => now()]);
        DB::table('riwayat_login')->insert([
            'pengguna_id' => $request->user()->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'masuk_pada' => now(),
        ]);
        return redirect()->intended(route('dasbor'));
    }

    public function keluar(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
