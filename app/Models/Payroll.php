<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Payroll extends Model
{
    protected $table = 'payroll';
    protected $guarded = ['id'];
    protected function casts(): array { return ['difinalisasi_pada' => 'datetime', 'total_jam' => 'decimal:2']; }
    public function karyawan(): BelongsTo { return $this->belongsTo(Karyawan::class); }
    public function periode(): BelongsTo { return $this->belongsTo(PeriodeGaji::class, 'periode_gaji_id'); }
    public function potongan(): HasMany { return $this->hasMany(Potongan::class); }
    public function slipGaji(): HasOne { return $this->hasOne(SlipGaji::class); }
}
