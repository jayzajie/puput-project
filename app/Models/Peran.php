<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Peran extends Model
{
    protected $table = 'peran';
    protected $fillable = ['nama', 'deskripsi'];
    public function pengguna(): HasMany { return $this->hasMany(Pengguna::class); }
}
