<?php

namespace App\Console\Commands;

use App\Services\SystemResetService;
use Illuminate\Console\Command;

class PruneSystemBackups extends Command
{
    protected $signature = 'system-backups:prune';
    protected $description = 'Hapus backup reset yang kedaluwarsa';

    public function handle(SystemResetService $service): int
    {
        $this->info($service->pruneExpired().' backup kedaluwarsa dihapus.');
        return self::SUCCESS;
    }
}
