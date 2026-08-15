<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RekapKerja extends Model
{
    protected $table = 'rekap_kerja';
    protected $fillable = ['karyawan_id', 'tanggal', 'status', 'total_jam', 'x1_5', 'x2', 'x3', 'x4', 'bbl', 'ujk', 'keterangan', 'diverifikasi_pada', 'diverifikasi_oleh'];
    protected function casts(): array { return ['tanggal' => 'date', 'diverifikasi_pada' => 'datetime', 'total_jam' => 'decimal:2', 'x1_5' => 'decimal:2', 'x2' => 'decimal:2', 'x3' => 'decimal:2', 'x4' => 'decimal:2']; }
    public function karyawan(): BelongsTo { return $this->belongsTo(Karyawan::class); }
    public function pemverifikasi(): BelongsTo { return $this->belongsTo(Pengguna::class, 'diverifikasi_oleh'); }
}
