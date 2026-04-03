@extends('layouts.app')

@section('title', 'Edit Nota - ' . $nota->nomor_nota)

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('nota.show', $nota) }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                ← Kembali ke Nota
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">✏️ Edit Nota</h1>
            <p class="text-gray-600 mt-1">Nomor: {{ $nota->nomor_nota ?? '(Digital)' }} | Status: <span
                    class="font-medium">{{ ucfirst($nota->status) }}</span></p>
        </div>

        <x-card title="Form Edit Nota" subtitle="Update data nota sesuai kebutuhan">
            <form action="{{ route('nota.update', $nota) }}" method="POST" enctype="multipart/form-data" class="space-y-6"
                onsubmit="finalizeSplitItems(event)">
                @csrf
                @method('PUT')

                <!-- TIPE NOTA - Required (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">
                        Tipe Nota <span class="text-red-500">*</span> <span class="text-gray-500 text-xs">(ReadOnly)</span>
                    </label>
                    <select disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                        <option selected>{{ ucfirst(str_replace('_', ' ', $nota->tipe)) }}</option>
                    </select>
                    <input type="hidden" name="tipe" value="{{ $nota->tipe }}">
                </div>

                <!-- COMMON FIELDS -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_nota"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ $nota->tanggal_nota->format('Y-m-d') }}" required>
                        @error('tanggal_nota')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Nomor Nota <span class="text-gray-500 text-xs">(Optional)</span>
                        </label>
                        <input type="text" name="nomor_nota" placeholder="NOT-20260403-0001"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ old('nomor_nota', $nota->nomor_nota) }}">
                        @error('nomor_nota')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Divisi <span class="text-red-500">*</span>
                        </label>
                        <select name="divisi_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                            <option value="">-- Pilih Divisi --</option>
                            @foreach ($divisis as $divisi)
                                <option value="{{ $divisi->id }}"
                                    {{ $nota->divisi_id === $divisi->id ? 'selected' : '' }}>
                                    {{ $divisi->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('divisi_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Nominal <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="nominal" id="nominalUtama" placeholder="Rp 0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ old('nominal', $nota->nominal) }}" min="0" required>
                        @error('nominal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- KETERANGAN -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">
                        Keterangan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keterangan" rows="3" placeholder="Deskripsi singkat tentang nota ini"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        required>{{ old('keterangan', $nota->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ATTACHMENTS -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">
                        Upload Lampiran Baru <span class="text-gray-500 text-xs">(Optional - Max 5 files,
                            PNG/JPG/PDF)</span>
                    </label>
                    <input type="file" name="attachments[]" multiple accept=".png,.jpg,.jpeg,.pdf"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-600 mt-1">Jika ingin menambah lampiran baru, upload di sini. Jika tidak,
                        biarkan kosong.</p>
                    @error('attachments')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    @if ($nota->attachments->count() > 0)
                        <div class="mt-3 bg-gray-50 p-3 rounded border border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-2">Lampiran Saat Ini:</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach ($nota->attachments as $attachment)
                                    <div class="relative group">
                                        @if (in_array($attachment->mime_type, ['image/png', 'image/jpeg', 'image/jpg']))
                                            <img src="{{ Storage::disk('public')->url($attachment->file_path) }}"
                                                alt="{{ $attachment->file_name }}"
                                                class="w-full h-20 object-cover rounded border border-gray-300">
                                        @else
                                            <div
                                                class="w-full h-20 bg-gray-200 rounded border border-gray-300 flex items-center justify-center text-2xl">
                                                📄
                                            </div>
                                        @endif
                                        <p class="text-xs text-gray-600 mt-1 truncate">{{ $attachment->file_name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- BUTTONS -->
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 transition-colors">
                        💾 Simpan Perubahan
                    </button>
                    <a href="{{ route('nota.show', $nota) }}"
                        class="flex-1 bg-gray-300 text-gray-900 px-6 py-3 rounded-lg font-medium hover:bg-gray-400 transition-colors text-center">
                        ❌ Batal
                    </a>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        // Placeholder JavaScript untuk compatibility dengan form fields (simplified version)
        function finalizeSplitItems(event) {
            // For now, just allow form submission
            return true;
        }
    </script>
@endsection
