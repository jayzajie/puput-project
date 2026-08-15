<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class PeriodeGaji extends Model
{
    protected $table = 'periode_gaji';
    protected $fillable = ['bulan', 'tahun', 'tanggal_mulai', 'tanggal_selesai', 'status'];
    protected function casts(): array { return ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date']; }
    public function payroll(): HasMany { return $this->hasMany(Payroll::class); }
    public function label(): string { return sprintf('%02d/%d', $this->bulan, $this->tahun); }
}
