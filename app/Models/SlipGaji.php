<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SlipGaji extends Model
{
    protected $table = 'slip_gaji';
    protected $fillable = ['payroll_id', 'tanggal_cetak', 'file_path', 'status'];
    protected function casts(): array { return ['tanggal_cetak' => 'datetime']; }
    public function payroll(): BelongsTo { return $this->belongsTo(Payroll::class); }
}
