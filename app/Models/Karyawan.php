<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $fillable = ['nik', 'nama', 'jabatan', 'email', 'gaji_pokok', 'aktif'];
    protected function casts(): array { return ['aktif' => 'boolean', 'gaji_pokok' => 'integer']; }
    public function pengguna(): HasOne { return $this->hasOne(Pengguna::class); }
    public function rekapKerja(): HasMany { return $this->hasMany(RekapKerja::class); }
    public function ritaseCoal(): HasMany { return $this->hasMany(RitaseCoal::class); }
    public function ritaseTopsoil(): HasMany { return $this->hasMany(RitaseTopsoil::class); }
    public function payroll(): HasMany { return $this->hasMany(Payroll::class); }
}
