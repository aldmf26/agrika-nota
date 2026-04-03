<?php

namespace App\Services;

use App\Models\Nota;
use App\Models\NotaItem;
use Carbon\Carbon;

/**
 * Service untuk menghitung nominal berdasarkan tipe nota
 * 
 * Prinsip: Hanya bertanggung jawab untuk kalkulasi, tidak ada side effects
 */
class NotaCalculationService
{
    /**
     * Hitung nominal untuk tipe SPLIT TAGIHAN
     * 
     * Input: array of [divisi_id => nominal, ...]
     * Output: total nominal
     * 
     * Contoh: Tagihan listrik Rp 1.000.000 dibagi:
     *   - Aga: 600.000
     *   - Agri: 400.000
     *   Total: 1.000.000
     */
    public function calculateSplitTotal(array $splitItems): int
    {
        return (int) collect($splitItems)
            ->sum(fn($item) => $item['nominal'] ?? 0);
    }

    /**
     * Hitung nominal untuk tipe REVENUE SHARING
     * 
     * Rumus: base_amount × (persentase / 100)
     * 
     * Contoh:
     *   base_amount: 587.487.136
     *   persentase: 8%
     *   nominal: 587.487.136 × 8 / 100 = 46.998.971
     */
    public function calculateRevenueSharing(int $baseAmount, float $persentase): int
    {
        return (int) ($baseAmount * $persentase / 100);
    }

    /**
     * Hitung SELISIH untuk tipe KELEBIHAN BAYAR
     * 
     * Rumus: nominal_dibayar - nominal_seharusnya
     * 
     * Contoh:
     *   nominal_seharusnya: 25.375.000
     *   nominal_dibayar: 25.550.000
     *   selisih (deposit): 175.000
     */
    public function calculateOverpayment(int $nominalSeharusnya, int $nominalDibayar): int
    {
        return $nominalDibayar - $nominalSeharusnya;
    }

    /**
     * Parse tanggal dan ambil bulan + tahun
     */
    public function getMonthAndYear(string $dateString): array
    {
        $date = Carbon::parse($dateString);

        return [
            'bulan' => $date->month,
            'tahun' => $date->year,
        ];
    }
}
