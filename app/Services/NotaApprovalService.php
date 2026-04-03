<?php

namespace App\Services;

use App\Models\Nota;
use App\Models\NotaItem;
use App\Models\DepositLog;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk mengelola approval workflow nota
 * 
 * Prinsip: Menangani logika kompleks seperti approve, reject, void
 */
class NotaApprovalService
{
    /**
     * APPROVE nota
     * 
     * Yang dilakukan:
     * 1. Update status menjadi approved + set approver
     * 2. Jika tipe kelebihan_bayar, buat entry di deposit_log
     */
    public function approve(Nota $nota, int $approverId, ?string $catatan = null): void
    {
        DB::transaction(function () use ($nota, $approverId, $catatan) {
            // Update nota
            $nota->approve($approverId, $catatan);

            // Jika kelebihan bayar, catat ke deposit log
            if ($nota->tipe === 'kelebihan_bayar' && $nota->selisih > 0) {
                DepositLog::create([
                    'nota_id' => $nota->id,
                    'divisi_id' => $nota->divisi_id,
                    'nominal' => $nota->selisih,
                    'status' => 'tersedia',
                ]);
            }
        });
    }

    /**
     * REJECT nota
     * 
     * Yang dilakukan:
     * 1. Update status menjadi rejected + set approver
     * 2. Simpan alasan penolakan
     */
    public function reject(Nota $nota, int $approverId, string $catatan): void
    {
        DB::transaction(function () use ($nota, $approverId, $catatan) {
            $nota->reject($approverId, $catatan);
        });
    }

    /**
     * VOID nota (batalkan)
     * 
     * Yang dilakukan:
     * 1. Update status menjadi void
     * 2. Jika sudah ada deposit dari nota ini, void juga
     */
    public function void(Nota $nota): void
    {
        DB::transaction(function () use ($nota) {
            $nota->void();

            // Void deposit logs yang terkait
            DepositLog::where('nota_id', $nota->id)
                ->update(['status' => 'void']);
        });
    }

    /**
     * Hitung total nominal dari sebuah nota
     * 
     * Untuk split tagihan: sum semua divisi
     * Untuk lainnya: ambil field nominal
     */
    public function getTotalNominal(Nota $nota): int
    {
        if ($nota->tipe === 'split') {
            return $nota->items->sum('nominal');
        }

        return $nota->nominal;
    }

    /**
     * Get info divisi yang terlibat di nota ini
     * 
     * Untuk split: ambil dari table nota_items
     * Untuk lainnya: ambil dari divisi_id
     */
    public function getDivisiTerlibat(Nota $nota)
    {
        return $nota->getDivisiTerlibat();
    }
}
