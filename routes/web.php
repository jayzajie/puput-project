<?php

use App\Http\Controllers\AutentikasiController;
use App\Http\Controllers\DasborController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\RekapKerjaController;
use App\Http\Controllers\RitaseController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AutentikasiController::class, 'formulir'])->name('login');
    Route::post('/masuk', [AutentikasiController::class, 'masuk'])->name('masuk');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/keluar', [AutentikasiController::class, 'keluar'])->name('keluar');
    Route::get('/dasbor', DasborController::class)->name('dasbor');
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::patch('/profil/foto', [ProfilController::class, 'ubahFoto'])->name('profil.foto');
    Route::patch('/profil/password', [ProfilController::class, 'ubahPassword'])->name('profil.password');
    Route::get('/rekap-kerja', [RekapKerjaController::class, 'index'])->name('rekap.index');
    Route::middleware('peran:admin,karyawan')->group(function (): void {
        Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/slip-gaji/{slipGaji}', [PayrollController::class, 'slip'])->name('slip.show');
    });

    Route::middleware('peran:admin,supervisor')->group(function (): void {
        Route::post('/rekap-kerja', [RekapKerjaController::class, 'simpan'])->name('rekap.simpan');
        Route::patch('/rekap-kerja/{rekapKerja}/verifikasi', [RekapKerjaController::class, 'verifikasi'])->name('rekap.verifikasi');
        Route::delete('/rekap-kerja/{rekapKerja}', [RekapKerjaController::class, 'hapus'])->name('rekap.hapus');
        Route::get('/ritase', [RitaseController::class, 'index'])->name('ritase.index');
        Route::post('/ritase/coal', [RitaseController::class, 'simpanCoal'])->name('ritase.coal');
        Route::post('/ritase/topsoil', [RitaseController::class, 'simpanTopsoil'])->name('ritase.topsoil');
        Route::delete('/ritase/coal/{ritaseCoal}', [RitaseController::class, 'hapusCoal'])->name('ritase.coal.hapus');
        Route::delete('/ritase/topsoil/{ritaseTopsoil}', [RitaseController::class, 'hapusTopsoil'])->name('ritase.topsoil.hapus');
    });

    Route::middleware('peran:admin')->group(function (): void {
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
        Route::post('/karyawan', [KaryawanController::class, 'simpan'])->name('karyawan.simpan');
        Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'ubah'])->name('karyawan.ubah');
        Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'hapus'])->name('karyawan.hapus');
        Route::post('/payroll/hitung', [PayrollController::class, 'hitung'])->name('payroll.hitung');
        Route::patch('/payroll/{payroll}/finalisasi', [PayrollController::class, 'finalisasi'])->name('payroll.finalisasi');
        Route::delete('/payroll/{payroll}', [PayrollController::class, 'hapusPayroll'])->name('payroll.hapus');
        Route::delete('/slip-gaji/{slipGaji}', [PayrollController::class, 'hapusSlip'])->name('slip.hapus');
        Route::post('/potongan', [PayrollController::class, 'tambahPotongan'])->name('potongan.simpan');
        Route::put('/potongan/{potongan}', [PayrollController::class, 'ubahPotongan'])->name('potongan.ubah');
        Route::delete('/potongan/{potongan}', [PayrollController::class, 'hapusPotongan'])->name('potongan.hapus');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('/laporan/ekspor', [LaporanController::class, 'ekspor'])->name('laporan.ekspor');
        Route::delete('/laporan/{laporan}', [LaporanController::class, 'hapus'])->name('laporan.hapus');
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('/pengaturan/tarif/{tarifRitase}', [PengaturanController::class, 'simpanTarif'])->name('tarif.ubah');
        Route::post('/pengaturan/tarif', [PengaturanController::class, 'tambahTarif'])->name('tarif.simpan');
        Route::delete('/pengaturan/tarif/{tarifRitase}', [PengaturanController::class, 'hapusTarif'])->name('tarif.hapus');
        Route::post('/pengaturan/periode', [PengaturanController::class, 'simpanPeriode'])->name('periode.simpan');
        Route::put('/pengaturan/periode/{periodeGaji}', [PengaturanController::class, 'ubahPeriode'])->name('periode.ubah');
        Route::delete('/pengaturan/periode/{periodeGaji}', [PengaturanController::class, 'hapusPeriode'])->name('periode.hapus');
    });
});
