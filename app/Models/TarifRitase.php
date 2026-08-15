<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class TarifRitase extends Model
{
    protected $table = 'tarif_ritase';
    protected $fillable = ['jenis', 'jarak_dari', 'jarak_sampai', 'tarif', 'satuan', 'aktif'];
    protected function casts(): array { return ['aktif' => 'boolean', 'tarif' => 'integer']; }
}
