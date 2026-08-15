<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Potongan extends Model
{
    protected $table = 'potongan';
    protected $fillable = ['karyawan_id', 'periode_gaji_id', 'payroll_id', 'jenis_potongan', 'deskripsi', 'nominal'];
    protected function casts(): array { return ['nominal' => 'integer']; }
    public function payroll(): BelongsTo { return $this->belongsTo(Payroll::class); }
    public function karyawan(): BelongsTo { return $this->belongsTo(Karyawan::class); }
}
