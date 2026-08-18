<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemBackup;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $enablePrintQr = SystemSetting::isQrEnabled();
        $resetBackup = SystemBackup::getValidResetBackup();
        $latestBackup = SystemBackup::where('expires_at', '>', now())
            ->latest()
            ->first();

        return view('admin.settings.index', compact('enablePrintQr', 'resetBackup', 'latestBackup'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'enable_print_qr' => ['nullable', 'in:0,1'],
        ]);

        $enableQr = $request->has('enable_print_qr') ? '1' : '0';
        SystemSetting::set('enable_print_qr', $enableQr, 'Tampilkan QR Code pada cetak detail nota');

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
