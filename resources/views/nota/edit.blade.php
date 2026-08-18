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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                @if ($nota->tipe === 'split')
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Nominal Total (Rp)</label>
                                <input type="number" name="nominal_total" id="nominal_total" min="2000"
                                    value="{{ old('nominal_total', $nota->nominal) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg" oninput="calculateSplitTotal()" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Mode Pembagian</label>
                                <select name="split_mode" id="split_mode" onchange="changeSplitMode()"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="rupiah">Rupiah</option>
                                    <option value="persen" {{ old('split_mode', $nota->items->contains(fn ($item) => $item->persentase !== null) ? 'persen' : 'rupiah') === 'persen' ? 'selected' : '' }}>Persen</option>
                                </select>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg overflow-visible min-h-[260px] bg-white">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="p-3 text-left w-1/2">Divisi</th>
                                        <th class="p-3 text-right w-5/12" id="splitValueHeader">Nominal (Rp)</th>
                                        <th class="p-3 text-center w-1/12">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="splitItemsBody" class="divide-y"></tbody>
                            </table>
                        </div>
                        <button type="button" onclick="addSplitItem()" class="w-full bg-blue-50 text-blue-700 border border-blue-200 px-4 py-2 rounded-lg">Tambah Divisi</button>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex justify-between gap-3">
                            <span id="splitValidation">Periksa pembagian</span>
                            <strong id="splitTotalPreview">Rp 0</strong>
                        </div>
                        @error('split_items')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>
                @endif

                @if ($nota->tipe !== 'split')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Divisi <span class="text-red-500">*</span>
                        </label>
                        <x-divisi-autocomplete
                            name="divisi_id"
                            id="divisi_id"
                            :divisis="$divisis"
                            :value="old('divisi_id', $nota->divisi_id)"
                            :required="true"
                            placeholder="Ketik untuk mencari divisi..."
                        />
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
                @endif

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

    @if ($nota->tipe === 'split')
    @php
        $existingSplitItems = $nota->items->map(function ($item) {
            return [
                'divisi_id' => $item->divisi_id,
                'nominal' => $item->nominal,
                'persentase' => $item->persentase,
            ];
        });
        $initialSplitItems = collect(old('split_items', $existingSplitItems))->values()->map(function ($item, $index) {
            return array_merge([
                'id' => $index + 1,
                'nominal' => '',
                'persentase' => '',
            ], $item);
        });
    @endphp
    <script>
        const divisiData = @json($divisis);
        let splitItemsData = {{ Illuminate\Support\Js::from($initialSplitItems) }};

        function addSplitItem() {
            if (splitItemsData.length >= 20) return;
            splitItemsData.push({id: Date.now(), divisi_id: '', nominal: '', persentase: ''});
            renderSplitItems();
        }
        function removeSplitItem(id) { splitItemsData = splitItemsData.filter(item => item.id !== id); renderSplitItems(); }
        function updateSplitItem(id, field, value) { const item = splitItemsData.find(item => item.id === id); if (item) item[field] = value; field === 'divisi_id' ? renderSplitItems() : calculateSplitTotal(); }
        function changeSplitMode() { splitItemsData.forEach(item => { item.nominal = ''; item.persentase = ''; }); renderSplitItems(); }
        function updateSplitValue(id, input) {
            const mode = document.getElementById('split_mode').value;
            if (mode === 'persen') {
                const value = input.value.replace(/[^0-9.,]/g, '').replace(',', '.'); input.value = value; updateSplitItem(id, 'persentase', value);
            } else {
                const value = input.value.replace(/\D/g, ''); input.value = value ? new Intl.NumberFormat('id-ID').format(value) : ''; updateSplitItem(id, 'nominal', value);
            }
        }
        function renderSplitItems() {
            const mode = document.getElementById('split_mode').value;
            document.getElementById('splitValueHeader').textContent = mode === 'persen' ? 'Persentase (%)' : 'Nominal (Rp)';
            const body = document.getElementById('splitItemsBody');
            body.innerHTML = '';

            if (splitItemsData.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = `
                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 italic">
                        Belum ada item divisi. Klik <strong>Tambah Divisi</strong> untuk menambahkan item split.
                    </td>`;
                body.appendChild(emptyRow);
                calculateSplitTotal();
                return;
            }

            splitItemsData.forEach(item => {
                const selected = splitItemsData.filter(other => other.id !== item.id).map(other => String(other.divisi_id));
                const filteredDivisi = divisiData.filter(d => !selected.includes(String(d.id)));
                const acId = 'split_divisi_' + item.id;
                const value = mode === 'persen' ? item.persentase : item.nominal;
                const row = document.createElement('tr');
                row.className = "hover:bg-gray-50/50 transition-colors";
                row.innerHTML = `
                    <td class="p-2 sm:p-3 align-top">
                        <div class="relative divisi-autocomplete-wrapper" data-input-id="${acId}">
                            <input type="hidden" name="" data-split-divisi="${item.id}" value="${item.divisi_id || ''}">
                            <input type="text" id="${acId}"
                                class="w-full p-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 autocomplete-input text-gray-900"
                                placeholder="Cari divisi..." autocomplete="off" value="${item.divisi_id ? (divisiData.find(d => String(d.id) === String(item.divisi_id))?.nama || '') : ''}">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 autocomplete-clear ${item.divisi_id ? '' : 'hidden'}"
                                onclick="clearSplitAutocomplete(${item.id})" title="Hapus pilihan">&times;</button>
                            <div id="${acId}_dropdown" class="absolute z-[999] w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-56 overflow-y-auto hidden autocomplete-dropdown"></div>
                        </div>
                    </td>
                    <td class="p-2 sm:p-3 align-top">
                        <input type="text" inputmode="decimal" class="w-full p-2 border border-gray-300 rounded-lg text-sm text-right focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-900"
                            value="${mode === 'persen' ? (value || '') : (value ? new Intl.NumberFormat('id-ID').format(value) : '')}"
                            oninput="updateSplitValue(${item.id}, this)" placeholder="${mode === 'persen' ? '0%' : '0'}">
                    </td>
                    <td class="p-2 sm:p-3 align-top text-center">
                        <button type="button" onclick="removeSplitItem(${item.id})" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg text-lg transition-colors" title="Hapus">&times;</button>
                    </td>`;
                body.appendChild(row);

                window.initDivisiAutocomplete(acId, filteredDivisi, {
                    onSelect: function(selected) {
                        item.divisi_id = selected.id;
                        renderSplitItems();
                    }
                });
            });
            calculateSplitTotal();
        }
        function clearSplitAutocomplete(itemId) {
            const item = splitItemsData.find(i => i.id === itemId);
            if (item) { item.divisi_id = ''; renderSplitItems(); }
        }
        function calculateSplitTotal() {
            const mode = document.getElementById('split_mode').value;
            const total = splitItemsData.reduce((sum, item) => sum + (mode === 'persen' ? (parseFloat(item.persentase) || 0) : (parseInt(item.nominal) || 0)), 0);
            const target = parseInt(document.getElementById('nominal_total').value) || 0;
            document.getElementById('splitTotalPreview').textContent = mode === 'persen' ? total.toLocaleString('id-ID') + '%' : 'Rp ' + total.toLocaleString('id-ID');
            const difference = (mode === 'persen' ? 100 : target) - total;
            const status = document.getElementById('splitValidation');
            status.textContent = splitItemsData.length < 2 ? 'Minimal 2 divisi' : Math.abs(difference) <= .001 ? 'Valid - siap disimpan' : `${difference > 0 ? 'Sisa' : 'Kelebihan'} ${mode === 'persen' ? Math.abs(difference).toLocaleString('id-ID') + '%' : 'Rp ' + Math.abs(difference).toLocaleString('id-ID')}`;
            status.className = Math.abs(difference) <= .001 && splitItemsData.length >= 2 ? 'text-green-700' : 'text-red-600';
        }
        function finalizeSplitItems(event) {
            const mode = document.getElementById('split_mode').value, target = parseInt(document.getElementById('nominal_total').value) || 0;
            const total = splitItemsData.reduce((sum, item) => sum + (mode === 'persen' ? (parseFloat(item.persentase) || 0) : (parseInt(item.nominal) || 0)), 0);
            const ids = splitItemsData.map(item => String(item.divisi_id)).filter(Boolean);
            if (splitItemsData.length < 2 || ids.length !== splitItemsData.length || new Set(ids).size !== ids.length || Math.abs(total - (mode === 'persen' ? 100 : target)) > .001) { event.preventDefault(); alert('Periksa divisi dan total pembagian.'); return false; }
            splitItemsData.forEach((item, index) => ['divisi_id','nominal','persentase'].forEach(field => { const input = document.createElement('input'); input.type='hidden'; input.name=`split_items[${index}][${field}]`; input.value=item[field] || ''; event.target.appendChild(input); }));
            return true;
        }
        renderSplitItems();
    </script>
    @else
    <script>function finalizeSplitItems() { return true; }</script>
    @endif
@endsection
