<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RitaseTopsoil extends Model
{
    protected $table = 'ritase_topsoil';
    protected $fillable = ['karyawan_id', 'tanggal', 'jarak_km', 'bcm', 'jumlah_ritase', 'bonus', 'total_ritase'];
    protected function casts(): array { return ['tanggal' => 'date', 'jarak_km' => 'decimal:2', 'bcm' => 'decimal:2', 'jumlah_ritase' => 'integer', 'bonus' => 'integer', 'total_ritase' => 'integer']; }
    public function karyawan(): BelongsTo { return $this->belongsTo(Karyawan::class); }
}
