<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Laporan extends Model
{
    protected $table = 'laporan';
    protected $fillable = ['nama_laporan', 'parameter', 'periode_dari', 'periode_sampai', 'file_path', 'dibuat_oleh'];
    protected function casts(): array { return ['parameter' => 'array', 'periode_dari' => 'date', 'periode_sampai' => 'date']; }
    public function pembuat(): BelongsTo { return $this->belongsTo(Pengguna::class, 'dibuat_oleh'); }
}
