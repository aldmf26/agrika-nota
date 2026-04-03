<?php

namespace App\Console\Commands;

use App\Models\Nota;
use Illuminate\Console\Command;
use Carbon\Carbon;

class PermanentlyDeleteOldNotas extends Command
{
    protected $signature = 'nota:cleanup-deleted';
    protected $description = 'Permanently delete nota yang sudah soft-deleted lebih dari 3 bulan';

    public function handle()
    {
        // Cari nota yang dihapus lebih dari 3 bulan lalu
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $deleted = Nota::onlyTrashed()
            ->where('deleted_at', '<=', $threeMonthsAgo)
            ->forceDelete();

        $this->info("✓ Menghapus {$deleted} nota yang sudah 3 bulan lebih di-delete.");

        return Command::SUCCESS;
    }
}
