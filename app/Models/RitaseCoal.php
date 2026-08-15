<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RitaseCoal extends Model
{
    protected $table = 'ritase_coal';
    protected $fillable = ['karyawan_id', 'tanggal', 'jumlah_ritase', 'total_ritase'];
    protected function casts(): array { return ['tanggal' => 'date', 'jumlah_ritase' => 'integer', 'total_ritase' => 'integer']; }
    public function karyawan(): BelongsTo { return $this->belongsTo(Karyawan::class); }
}
