<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class SystemController extends Controller
{
    /**
     * RESET - Hapus semua data (nota, items, attachments, archives, logs)
     * 
     * WARNING: This action is irreversible.
     */
    public function reset()
    {
        // Force check if super_admin role (can already be handled by middleware)
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Akses ditolak. Hanya Super Admin yang bisa reset data.');
        }

        try {
            // 1. Reset tables (disable FK checks during operation)
            // Note: TRUNCATE causes an implicit commit in MySQL, and cannot be undone via transactions.
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('nota_items')->truncate();
            DB::table('nota_attachments')->truncate();
            DB::table('nota_archives')->truncate();
            DB::table('deposit_logs')->truncate();
            DB::table('notas')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // 2. Delete media
            Storage::disk('public')->deleteDirectory('nota');

            return back()->with('success', '♻️ Sistem berhasil di-reset sepenuhnya. Semua data nota dan lampiran telah dihapus.');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('error', 'Gagal reset sistem: ' . $e->getMessage());
        }
    }
}
