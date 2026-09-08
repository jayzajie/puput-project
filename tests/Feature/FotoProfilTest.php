<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class FotoProfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengguna_dapat_memperbarui_foto_profil(): void
    {
        Storage::fake('public');
        $this->seed();
        $pengguna = Pengguna::where('email', 'admin@gjp.co.id')->firstOrFail();

        $this->actingAs($pengguna)->patch(route('profil.foto'), [
            'foto_profil' => UploadedFile::fake()->image('profil.jpg', 300, 300),
        ])->assertSessionHas('sukses');

        Storage::disk('public')->assertExists($pengguna->fresh()->foto_profil);
    }

    public function test_foto_sidebar_menuju_halaman_profil_bukan_logout(): void
    {
        $this->seed();
        $pengguna = Pengguna::where('email', 'admin@gjp.co.id')->firstOrFail();

        $this->actingAs($pengguna)->get(route('dasbor'))
            ->assertOk()
            ->assertSee('href="'.route('profil.index').'"', false)
            ->assertDontSee('Foto Profil')
            ->assertSee('aria-label="Keluar"', false);
    }

    public function test_pengguna_dapat_mengganti_password(): void
    {
        $this->seed();
        $pengguna = Pengguna::where('email', 'admin@gjp.co.id')->firstOrFail();

        $this->actingAs($pengguna)->patch(route('profil.password'), [
            'password_saat_ini' => 'password',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertSessionHas('sukses');

        $this->assertTrue(Hash::check('password-baru', $pengguna->fresh()->password));
    }

    public function test_login_berhasil_dicatat_dalam_riwayat(): void
    {
        $this->seed();
        $pengguna = Pengguna::where('email', 'admin@gjp.co.id')->firstOrFail();

        $this->post(route('masuk'), [
            'email' => $pengguna->email,
            'password' => 'password',
        ])->assertRedirect(route('dasbor'));

        $this->assertDatabaseHas('riwayat_login', ['pengguna_id' => $pengguna->id]);
        $this->get(route('profil.index'))->assertOk()->assertSee('Riwayat Login');
    }
}
