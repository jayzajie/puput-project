<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class Pengguna extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $fillable = ['peran_id', 'karyawan_id', 'name', 'email', 'password', 'status', 'terakhir_login'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'terakhir_login' => 'datetime'];
    }

    public function peran(): BelongsTo { return $this->belongsTo(Peran::class); }
    public function karyawan(): BelongsTo { return $this->belongsTo(Karyawan::class); }
    public function berperanSebagai(string ...$peran): bool { return in_array($this->peran?->nama, $peran, true); }
}
