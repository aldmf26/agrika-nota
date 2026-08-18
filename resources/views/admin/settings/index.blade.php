@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">⚙️ PENGATURAN SISTEM</h1>
            <p class="text-gray-500 mt-2 font-medium">Kelola preferensi umum aplikasi dan pemeliharaan data</p>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3">
                <span class="text-xl">✅</span>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="space-y-8">
            <!-- Card 1: Toggle QR Code -->
            <div class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-8 shadow-xl shadow-gray-100">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                            📷 Modul Verifikasi QR
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">QR Code pada Cetak Nota</h2>
                        <p class="text-gray-500 text-sm mt-1 max-w-xl">
                            Jika diaktifkan, lembar cetak detail nota akan menampilkan Kode QR Verifikasi resmi untuk pengaju dan pemeriksa. Jika dinonaktifkan, lembar cetak akan menggunakan area garis tanda tangan manual.
                        </p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-6 border-t border-gray-100 pt-6" id="qrSettingForm">
                    @csrf
                    <label for="enable_print_qr_input" class="flex items-center justify-between p-5 bg-gray-50 hover:bg-green-50/60 rounded-2xl border border-gray-200 cursor-pointer transition-all select-none">
                        <div>
                            <span class="font-bold text-gray-900 text-base block">Status Tampilan QR Code</span>
                            <span class="text-xs font-bold mt-1 inline-flex items-center gap-1.5 {{ $enablePrintQr ? 'text-emerald-700' : 'text-gray-500' }}">
                                <span class="w-2 h-2 rounded-full {{ $enablePrintQr ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $enablePrintQr ? 'QR Code AKTIF pada lembar cetak nota' : 'QR Code NONAKTIF (Garis Tanda Tangan Manual)' }}
                            </span>
                        </div>
                        <div class="relative inline-flex items-center shrink-0">
                            <input type="checkbox" id="enable_print_qr_input" name="enable_print_qr" value="1" class="sr-only peer" {{ $enablePrintQr ? 'checked' : '' }} onchange="document.getElementById('qrSettingForm').submit()">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                        </div>
                    </label>
                </form>
            </div>

            <!-- Card 2: Danger Zone / Reset Data -->
            <div class="bg-white rounded-3xl border-2 border-rose-100 p-6 sm:p-8 shadow-xl shadow-rose-50/50">
                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="bg-rose-50 w-14 h-14 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center text-2xl sm:text-3xl shrink-0">☢️</div>
                    <div>
                        <h3 class="text-rose-900 font-black text-xl tracking-tight">RESET DATA OPERASIONAL</h3>
                        <p class="text-rose-600/80 font-medium text-xs sm:text-sm mt-0.5">
                            Menghapus seluruh nota, item, dan lampiran foto. Backup JSON wajib dibuat sebelum reset dapat dilakukan.
                        </p>
                    </div>
                </div>

                <div class="mt-6 border-t border-rose-100 pt-6">
                    @if($resetBackup)
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                            <p class="font-black text-emerald-800 text-sm">Backup siap — {{ $resetBackup->nota_count }} nota</p>
                            <p class="text-xs text-emerald-700 mt-1">{{ number_format($resetBackup->file_size / 1024, 1, ',', '.') }} KB · berlaku sampai {{ $resetBackup->expires_at->format('d/m/Y H:i') }}</p>
                            <a href="{{ route('admin.system.backups.download', $resetBackup) }}" class="inline-block mt-2 text-xs font-bold text-emerald-800 underline">Unduh Backup JSON</a>
                        </div>
                        <form action="{{ route('admin.system.reset') }}" method="POST" class="mt-4" onsubmit="return confirm('Backup siap. Hapus data operasional dan foto sekarang?')">
                            @csrf
                            <input type="hidden" name="backup_id" value="{{ $resetBackup->id }}">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input name="confirmation" required autocomplete="off" placeholder="Ketik RESET" class="rounded-xl border border-rose-200 px-4 py-3 font-bold text-sm flex-1">
                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-3 rounded-xl font-black uppercase tracking-wider text-xs shadow-md shadow-rose-200 transition-colors">Reset Semua Data</button>
                            </div>
                            @error('confirmation')<p class="text-xs text-rose-600 mt-2">{{ $message }}</p>@enderror
                        </form>
                    @else
                        @if($latestBackup)
                            <div class="mb-4 rounded-2xl border border-sky-200 bg-sky-50 p-4">
                                <p class="font-bold text-sky-800 text-xs sm:text-sm">Backup terakhir tetap tersimpan sampai {{ $latestBackup->expires_at->format('d/m/Y H:i') }}</p>
                                <a href="{{ route('admin.system.backups.download', $latestBackup) }}" class="text-xs font-bold text-sky-800 underline mt-1 inline-block">Unduh Backup JSON</a>
                            </div>
                        @endif
                        <form action="{{ route('admin.system.backups.create') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-xl font-black uppercase tracking-wider text-xs shadow-md shadow-amber-200 transition-colors">Siapkan Backup JSON</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
