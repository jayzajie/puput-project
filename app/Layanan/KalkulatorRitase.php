<?php

declare(strict_types=1);

namespace App\Layanan;

use App\Models\TarifRitase;

final class KalkulatorRitase
{
    public function coal(int $jumlah): int
    {
        return TarifRitase::query()->where('jenis', 'coal')->where('aktif', true)->get()
            ->sum(function (TarifRitase $tarif) use ($jumlah): int {
                $awal = (int) $tarif->jarak_dari;
                $akhir = $tarif->jarak_sampai === null ? $jumlah : min($jumlah, (int) $tarif->jarak_sampai);
                return max(0, $akhir - $awal + 1) * $tarif->tarif;
            });
    }

    public function topsoil(float $jarak, float $bcm): int
    {
        $tarif = TarifRitase::query()->where('jenis', 'topsoil')->where('aktif', true)
            ->where('jarak_dari', '<=', $jarak)->where('jarak_sampai', '>=', $jarak)->value('tarif');

        return (int) round($bcm * (int) $tarif);
    }
}
