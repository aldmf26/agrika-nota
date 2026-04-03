@extends('layouts.app')

@section('title', 'Edit Divisi')

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">✏️ Edit Divisi</h1>
            <p class="text-gray-600 mt-1">Ubah data divisi dan kode prefix nota</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
            <form action="{{ route('admin.divisi.update', $divisi) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nama Divisi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wide">
                        Nama Divisi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium"
                        value="{{ old('nama', $divisi->nama) }}" placeholder="Contoh: Agrika Estate" required>
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Prefix -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wide">
                        Kode Prefix Nota <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold uppercase"
                        value="{{ old('kode', $divisi->kode) }}" placeholder="Contoh: AGE" maxlength="10" required>
                    @error('kode')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wide">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">{{ old('deskripsi', $divisi->deskripsi) }}</textarea>
                </div>

                <!-- Status Aktif -->
                <div class="flex items-center gap-3">
                    <input type="hidden" name="aktif" value="0">
                    <input type="checkbox" name="aktif" value="1" id="aktifSwitch"
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ old('aktif', $divisi->aktif) == '1' ? 'checked' : '' }}>
                    <label for="aktifSwitch" class="text-sm font-semibold text-gray-900 uppercase">Input Aktif</label>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-sm uppercase tracking-wide">
                        💾 Update
                    </button>
                    <a href="{{ route('admin.divisi.index') }}"
                        class="flex-1 bg-gray-200 text-gray-900 px-6 py-3 rounded-lg font-bold hover:bg-gray-300 transition-colors text-center uppercase tracking-wide">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
