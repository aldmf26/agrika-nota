<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemBackup;
use App\Services\SystemResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemController extends Controller
{
    public function __construct(private SystemResetService $resetService) {}

    public function backup()
    {
        try {
            $backup = $this->resetService->createBackup(auth()->user());
            return back()->with('success', "Backup JSON siap: {$backup->nota_count} nota diamankan.");
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Backup gagal; tidak ada data dihapus: '.$e->getMessage());
        }
    }

    public function download(SystemBackup $backup)
    {
        try {
            $this->resetService->assertDownloadable($backup);
            return Storage::disk('local')->download(
                $backup->path,
                'agrika-nota-backup-'.$backup->created_at->format('Ymd-His').'.json',
                ['Content-Type' => 'application/json']
            );
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'backup_id' => ['required', 'integer', 'exists:system_backups,id'],
            'confirmation' => ['required', 'in:RESET'],
        ], ['confirmation.in' => 'Ketik RESET dengan huruf kapital.']);

        try {
            $photosDeleted = $this->resetService->reset(SystemBackup::findOrFail($validated['backup_id']));
            $message = 'Reset selesai. Backup JSON tetap tersedia selama 30 hari.';
            return back()->with($photosDeleted ? 'success' : 'warning', $photosDeleted ? $message : $message.' Folder foto gagal dibersihkan.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Reset dibatalkan: '.$e->getMessage());
        }
    }
}
