@extends('layouts.app')

@section('title', 'Input Nota Baru')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-4 sm:mb-6">
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">📝 Input Nota Baru</h1>
            <p class="text-gray-600 mt-1">Lengkapi form di bawah untuk mencatat nota baru</p>
        </div>

        <x-card title="Form Nota Baru" subtitle="Pilih tipe nota terlebih dahulu">
            <form action="{{ route('nota.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
                onsubmit="finalizeSplitItems(event)">
                @csrf

                <!-- TIPE NOTA - Required -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">
                        Tipe Nota <span class="text-red-500">*</span>
                    </label>
                    <select name="tipe" id="tipNota"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        onchange="updateFormFields()" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="biasa" {{ old('tipe') === 'biasa' ? 'selected' : '' }}>Biasa (Umum)</option>
                        <option value="split" {{ old('tipe') === 'split' ? 'selected' : '' }}>Split Tagihan</option>
                        <option value="revenue_sharing" {{ old('tipe') === 'revenue_sharing' ? 'selected' : '' }}>Revenue
                            Sharing</option>
                        <option value="kelebihan_bayar" {{ old('tipe') === 'kelebihan_bayar' ? 'selected' : '' }}>Kelebihan
                            Bayar (Deposit)</option>

                    </select>
                    @error('tipe')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- COMMON FIELDS -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_nota"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ old('tanggal_nota', date('Y-m-d')) }}" required>
                        @error('tanggal_nota')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="divisionField">
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Divisi <span class="text-red-500">*</span>
                        </label>
                        <x-divisi-autocomplete
                            name="divisi_id"
                            id="divisi_id"
                            :divisis="$divisis"
                            :value="old('divisi_id')"
                            :required="true"
                            placeholder="Ketik untuk mencari divisi..."
                            onchange="updateNomorNota"
                        />
                        @error('divisi_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Nomor Nota (Otomatis)
                        </label>
                        <input type="text" name="nomor_nota" id="nomor_nota_input"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed focus:outline-none"
                            value="{{ old('nomor_nota', $nomorNota ?? '') }}" readonly>
                        @error('nomor_nota')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="nominalField">
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Nominal (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nominal_display"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ old('nominal') ? number_format(old('nominal'), 0, ',', '.') : '' }}"
                            onkeyup="formatCurrency(this, 'nominal')">
                        <input type="hidden" name="nominal" id="nominal" value="{{ old('nominal') }}">
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
                    <textarea name="keterangan" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Contoh: Upah harian driver, Biaya transportasi, dll"
                        required>{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SPECIAL FIELDS UNTUK REVENUE SHARING -->
                <div id="revenueSharingFields" class="hidden space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Base Amount (Rp)</label>
                            <input type="text" id="base_amount_display"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                value="{{ old('base_amount') ? number_format(old('base_amount'), 0, ',', '.') : '' }}"
                                onkeyup="formatCurrency(this, 'base_amount'); calculateRevenue()">
                            <input type="hidden" name="base_amount" id="base_amount" value="{{ old('base_amount') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Persentase (%)</label>
                            <input type="number" name="persentase"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                value="{{ old('persentase') }}" min="0.01" max="100" step="0.01"
                                onchange="calculateRevenue()">
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            📊 Nominal yang akan tercatat: <strong id="revenuePreview">Rp 0</strong>
                        </p>
                    </div>
                </div>

                <!-- SPECIAL FIELDS UNTUK KELEBIHAN BAYAR -->
                <div id="overpaymentFields" class="hidden space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Nominal Seharusnya (Rp)</label>
                            <input type="text" id="nominal_seharusnya_display"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                value="{{ old('nominal_seharusnya') ? number_format(old('nominal_seharusnya'), 0, ',', '.') : '' }}"
                                onkeyup="formatCurrency(this, 'nominal_seharusnya'); calculateOverpayment()">
                            <input type="hidden" name="nominal_seharusnya" id="nominal_seharusnya"
                                value="{{ old('nominal_seharusnya') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Nominal Dibayar (Rp)</label>
                            <input type="text" id="nominal_dibayar_display"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                value="{{ old('nominal_dibayar') ? number_format(old('nominal_dibayar'), 0, ',', '.') : '' }}"
                                onkeyup="formatCurrency(this, 'nominal_dibayar'); calculateOverpayment()">
                            <input type="hidden" name="nominal_dibayar" id="nominal_dibayar"
                                value="{{ old('nominal_dibayar') }}">
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm text-green-800">
                            💰 Deposit (selisih): <strong id="overpaymentPreview">Rp 0</strong>
                        </p>
                    </div>
                </div>

                <!-- SPECIAL FIELDS UNTUK SPLIT TAGIHAN -->
                <div id="splitFields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Mode Pembagian</label>
                        <select name="split_mode" id="split_mode" onchange="changeSplitMode()"
                            class="w-full sm:w-56 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="rupiah" {{ old('split_mode', 'rupiah') === 'rupiah' ? 'selected' : '' }}>Rupiah</option>
                            <option value="persen" {{ old('split_mode') === 'persen' ? 'selected' : '' }}>Persen</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                Nominal Total (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nominal_total_display"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                value="{{ old('nominal_total') ? number_format(old('nominal_total'), 0, ',', '.') : '' }}"
                                onkeyup="formatCurrency(this, 'nominal_total'); calculateSplitTotal()">
                            <input type="hidden" name="nominal_total" id="nominal_total" value="{{ old('nominal_total') }}">
                            <p class="text-xs text-gray-500 mt-1">Akan terbagi ke divisi-divisi di bawah</p>
                            @error('nominal_total')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                Total Item
                            </label>
                            <div class="px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-medium">
                                <span id="splitItemCount">0</span> divisi
                            </div>
                        </div>
                    </div>

                    <!-- Split Items Table -->
                    <div class="border border-gray-200 rounded-lg overflow-visible min-h-[260px] bg-white">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left w-1/2">Divisi</th>
                                    <th class="px-4 py-3 text-right w-5/12" id="splitValueHeader">Nominal (Rp)</th>
                                    <th class="px-4 py-3 text-center w-1/12">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="splitItemsBody" class="divide-y">
                                <!-- Items akan ditambah via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <button type="button" onclick="addSplitItem()"
                        class="w-full bg-blue-50 text-blue-600 border border-blue-200 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                        ➕ Tambah Item
                    </button>

                    <!-- Split Total Preview -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex justify-between mb-2">
                            <p class="text-sm text-blue-800">Total Nominal:</p>
                            <p class="font-semibold text-blue-900" id="splitTotalPreview">Rp 0</p>
                        </div>
                        <div class="text-xs text-blue-700">
                            <span id="splitValidation" class="text-yellow-600">⚠️ Belum ada item</span>
                        </div>
                    </div>

                    @error('split_items')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                    @error('split_items.*.divisi_id')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                    @error('split_items.*.nominal')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- FILE UPLOAD -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">
                        Foto Lampiran <span class="text-gray-500 text-xs">(Opsional)</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 sm:p-6 text-center cursor-pointer hover:border-green-500 transition-colors"
                        onclick="document.getElementById('attachments').click()">
                        <input type="file" id="attachments" name="attachments[]" class="hidden"
                            accept=".jpg,.jpeg,.png,.heic" multiple>
                        <p class="text-gray-600">
                            📸 Klik untuk upload atau drag & drop<br>
                            <span class="text-xs text-gray-500">JPG, PNG, HEIC (Max 5MB per file)</span>
                        </p>
                    </div>
                    <div id="fileList" class="mt-2 space-y-1 text-sm"></div>
                    @error('attachments')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('attachments.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ACTIONS -->
                <div class="flex flex-col gap-3 pt-4 sm:flex-row sm:gap-4">
                    <button type="submit"
                        class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 transition-colors">
                        💾 Simpan Draft
                    </button>
                    <a href="{{ route('nota.index') }}"
                        class="flex-1 bg-gray-200 text-gray-900 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors text-center">
                        Batal
                    </a>
                </div>
            </form>
        </x-card>
    </div>

    @php
        $initialSplitItems = collect(old('split_items', []))->values()->map(function ($item, $index) {
            return array_merge([
                'id' => $index + 1,
                'divisi_id' => '',
                'nominal' => '',
                'persentase' => '',
            ], $item);
        });
    @endphp
    <script>
        const divisiData = @json($divisis);
        const originalNomorNota = "{{ $nomorNota }}"; // Tanpa kode prefix
        let splitItemsData = {{ Illuminate\Support\Js::from($initialSplitItems) }};

        function updateNomorNota() {
            if (document.getElementById('tipNota').value === 'split') {
                document.getElementById('nomor_nota_input').value = 'SPL' + originalNomorNota;
                return;
            }
            const divisiId = document.getElementById('divisi_id_hidden').value;
            const nomorNotaInput = document.getElementById('nomor_nota_input');

            if (divisiId) {
                const divisi = divisiData.find(d => d.id == divisiId);
                if (divisi && divisi.kode) {
                    nomorNotaInput.value = divisi.kode.toUpperCase() + originalNomorNota;
                    return;
                }
            }
            nomorNotaInput.value = originalNomorNota;
        }

        // Finalize split items sebelum form submit
        function finalizeSplitItems(e) {
            const tipe = document.getElementById('tipNota').value;

            if (tipe === 'split') {
                // Validasi: minimal 2 items, semua divisi dan nominal harus filled
                const errors = [];
                const divisiIds = [];

                if (splitItemsData.length < 2) {
                    errors.push('Minimal 2 divisi untuk split tagihan');
                }

                splitItemsData.forEach((item, idx) => {
                    if (!item.divisi_id) {
                        errors.push(`Item ${idx + 1}: Pilih divisi terlebih dahulu`);
                    } else {
                        // Check duplikasi divisi
                        if (divisiIds.includes(item.divisi_id)) {
                            errors.push(`Item ${idx + 1}: Divisi ini sudah dipilih di item lain`);
                        } else {
                            divisiIds.push(item.divisi_id);
                        }
                    }

                    if (!item.nominal || item.nominal === '0') {
                        errors.push(`Item ${idx + 1}: Isi nominal (minimal Rp 1.000)`);
                    }
                });

                if (errors.length > 0) {
                    e.preventDefault();
                    alert('❌ Silakan perbaiki:\n\n' + errors.join('\n'));
                    return false;
                }

                // Rebuild hidden inputs dengan data terbaru
                const tbody = document.getElementById('splitItemsBody');
                let hiddenCount = 0;

                // Clear existing hidden inputs (jika ada dari sebelumnya)
                document.querySelectorAll('input[name^="split_items"]').forEach(input => input.remove());

                // Create new hidden inputs sebelum submit
                splitItemsData.forEach((item, index) => {
                    const divisiInput = document.createElement('input');
                    divisiInput.type = 'hidden';
                    divisiInput.name = `split_items[${index}][divisi_id]`;
                    divisiInput.value = item.divisi_id;
                    tbody.parentElement.parentElement.appendChild(divisiInput);

                    const nominalInput = document.createElement('input');
                    nominalInput.type = 'hidden';
                    nominalInput.name = `split_items[${index}][nominal]`;
                    nominalInput.value = item.nominal;
                    tbody.parentElement.parentElement.appendChild(nominalInput);
                });
            }

            return true;
        }

        function updateFormFields() {
            const tipe = document.getElementById('tipNota').value;
            document.getElementById('nominalField').style.display = tipe !== 'revenue_sharing' && tipe !==
                'kelebihan_bayar' && tipe !== 'split' ? 'block' : 'none';
            document.getElementById('revenueSharingFields').style.display = tipe === 'revenue_sharing' ? 'block' : 'none';
            document.getElementById('overpaymentFields').style.display = tipe === 'kelebihan_bayar' ? 'block' : 'none';
            document.getElementById('splitFields').style.display = tipe === 'split' ? 'block' : 'none';
            document.getElementById('divisionField').style.display = tipe === 'split' ? 'none' : 'block';
            document.getElementById('divisi_id_hidden').required = tipe !== 'split';
            updateNomorNota();

            // Clear split items jika switch dari split
            if (tipe !== 'split') {
                splitItemsData = [];
                document.getElementById('splitItemsBody').innerHTML = '';
            }
        }

        function addSplitItem() {
            const id = Date.now();
            splitItemsData.push({
                id,
                divisi_id: '',
                nominal: '',
                persentase: ''
            });
            renderSplitItems();
        }

        function removeSplitItem(id) {
            splitItemsData = splitItemsData.filter(item => item.id !== id);
            renderSplitItems();
        }

        // Update hanya data, tanpa re-render (untuk keyup)
        function updateSplitItemData(id, field, value) {
            const item = splitItemsData.find(i => i.id === id);
            if (item) {
                item[field] = value;
                updateSplitItemCount();
                calculateSplitTotal();
            }
        }

        function updateSplitItem(id, field, value) {
            updateSplitItemData(id, field, value);
            renderSplitItems();
        }

        function updateSplitItemCount() {
            document.getElementById('splitItemCount').textContent = splitItemsData.length;
        }

        function calculateSplitTotal() {
            const total = splitItemsData.reduce((sum, item) => sum + (parseInt(item.nominal) || 0), 0);
            document.getElementById('splitTotalPreview').textContent = 'Rp ' + total.toLocaleString('id-ID');

            const nominalTotal = parseInt(document.getElementById('nominal_total').value) || 0;
            const validation = document.getElementById('splitValidation');

            if (splitItemsData.length === 0) {
                validation.textContent = '⚠️ Belum ada item - Klik "Tambah Item" untuk mulai';
                validation.className = 'text-yellow-600';
            } else if (splitItemsData.length < 2) {
                validation.textContent = '⚠️ Minimal 2 divisi untuk split tagihan';
                validation.className = 'text-yellow-600';
            } else if (nominalTotal === 0) {
                validation.textContent = '⚠️ Isi nominal total terlebih dahulu';
                validation.className = 'text-yellow-600';
            } else {
                validation.textContent = '✓ Valid - siap submit';
                validation.className = 'text-green-600';
            }
        }

        function formatCurrencyEl(input) {
            const value = input.value.replace(/\D/g, '');
            if (value) {
                input.value = new Intl.NumberFormat('id-ID').format(value);
            }
        }

        function formatCurrency(input, targetId) {
            let value = input.value.replace(/\D/g, '');
            let hiddenInput = document.getElementById(targetId);
            if (hiddenInput) hiddenInput.value = value;

            if (value) {
                input.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                input.value = '';
            }
        }

        function calculateRevenue() {
            const base = parseInt(document.getElementById('base_amount').value) || 0;
            const persen = parseFloat(document.querySelector('input[name="persentase"]').value) || 0;
            const nominal = Math.floor(base * persen / 100);
            document.getElementById('revenuePreview').textContent = 'Rp ' + nominal.toLocaleString('id-ID');
        }

        function calculateOverpayment() {
            const seharusnya = parseInt(document.getElementById('nominal_seharusnya').value) || 0;
            const dibayar = parseInt(document.getElementById('nominal_dibayar').value) || 0;
            const selisih = dibayar - seharusnya;
            document.getElementById('overpaymentPreview').textContent = 'Rp ' + Math.max(0, selisih).toLocaleString(
                'id-ID');
        }

        document.getElementById('attachments').addEventListener('change', function (e) {
            const list = document.getElementById('fileList');
            list.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const p = document.createElement('p');
                p.className = 'text-green-600';
                p.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                list.appendChild(p);
            });
        });

        // Initialize on load
        updateFormFields();
        renderSplitItems();

        function changeSplitMode() {
            splitItemsData.forEach(item => {
                item.nominal = '';
                item.persentase = '';
            });
            renderSplitItems();
        }

        function renderSplitItems() {
            const mode = document.getElementById('split_mode').value;
            const tbody = document.getElementById('splitItemsBody');
            document.getElementById('splitValueHeader').textContent = mode === 'persen' ? 'Persentase (%)' : 'Nominal (Rp)';
            tbody.innerHTML = '';

            if (splitItemsData.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = `
                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 italic">
                        Belum ada item divisi. Klik <strong>➕ Tambah Item</strong> untuk menambahkan divisi.
                    </td>`;
                tbody.appendChild(emptyRow);
                updateSplitItemCount();
                calculateSplitTotal();
                return;
            }

            splitItemsData.forEach((item) => {
                const value = mode === 'persen' ? item.persentase : item.nominal;
                const selectedIds = splitItemsData.filter(other => other.id !== item.id).map(other => String(other.divisi_id));
                const filteredDivisi = divisiData.filter(d => !selectedIds.includes(String(d.id)));
                const acId = 'split_divisi_' + item.id;
                const row = document.createElement('tr');
                row.className = "hover:bg-gray-50/50 transition-colors";
                row.innerHTML = `
                    <td class="px-2 sm:px-4 py-3 align-top">
                        <div class="relative divisi-autocomplete-wrapper" data-input-id="${acId}">
                            <input type="hidden" name="" data-split-divisi="${item.id}" value="${item.divisi_id || ''}">
                            <input type="text" id="${acId}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 autocomplete-input text-gray-900"
                                placeholder="Pilih / cari divisi..." autocomplete="off" value="${item.divisi_id ? (divisiData.find(d => String(d.id) === String(item.divisi_id))?.nama || '') : ''}">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 autocomplete-clear ${item.divisi_id ? '' : 'hidden'}"
                                onclick="clearSplitAutocomplete(${item.id})" title="Hapus pilihan">&times;</button>
                            <div id="${acId}_dropdown" class="absolute z-[999] w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-56 overflow-y-auto hidden autocomplete-dropdown"></div>
                        </div>
                    </td>
                    <td class="px-2 sm:px-4 py-3 align-top">
                        <input type="text" inputmode="decimal" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-right focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-900"
                            value="${mode === 'persen' ? (value || '') : (value ? new Intl.NumberFormat('id-ID').format(value) : '')}"
                            oninput="updateSplitValue(${item.id}, this)" placeholder="${mode === 'persen' ? '0%' : '0'}">
                    </td>
                    <td class="px-2 sm:px-4 py-3 align-top text-center">
                        <button type="button" onclick="removeSplitItem(${item.id})" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg text-lg transition-colors" title="Hapus">&times;</button>
                    </td>`;
                tbody.appendChild(row);

                window.initDivisiAutocomplete(acId, filteredDivisi, {
                    onSelect: function(selected) {
                        item.divisi_id = selected.id;
                        window.updateSplitItemData(item.id, 'divisi_id', selected.id);
                        renderSplitItems();
                    }
                });
            });
            updateSplitItemCount();
            calculateSplitTotal();
        }

        function clearSplitAutocomplete(itemId) {
            const item = splitItemsData.find(i => i.id === itemId);
            if (item) {
                item.divisi_id = '';
                renderSplitItems();
            }
        }

        function updateSplitValue(id, input) {
            const mode = document.getElementById('split_mode').value;
            if (mode === 'persen') {
                const value = input.value.replace(/[^0-9.,]/g, '').replace(',', '.');
                input.value = value;
                updateSplitItemData(id, 'persentase', value);
            } else {
                const value = input.value.replace(/\D/g, '');
                updateSplitItemData(id, 'nominal', value);
                formatCurrencyEl(input);
            }
        }

        function calculateSplitTotal() {
            const mode = document.getElementById('split_mode').value;
            const total = splitItemsData.reduce((sum, item) => sum + (mode === 'persen' ? (parseFloat(item.persentase) || 0) : (parseInt(item.nominal) || 0)), 0);
            const target = parseInt(document.getElementById('nominal_total').value) || 0;
            const validation = document.getElementById('splitValidation');
            document.getElementById('splitTotalPreview').textContent = mode === 'persen' ? total.toLocaleString('id-ID') + '%' : 'Rp ' + total.toLocaleString('id-ID');

            if (splitItemsData.length < 2) {
                validation.textContent = 'Minimal 2 divisi untuk split tagihan';
                validation.className = 'text-yellow-600';
                return;
            }
            if (!target) {
                validation.textContent = 'Isi nominal total terlebih dahulu';
                validation.className = 'text-yellow-600';
                return;
            }
            const difference = (mode === 'persen' ? 100 : target) - total;
            if (Math.abs(difference) > 0.001) {
                const prefix = difference > 0 ? 'Sisa' : 'Kelebihan';
                validation.textContent = mode === 'persen'
                    ? `${prefix} ${Math.abs(difference).toLocaleString('id-ID')}%`
                    : `${prefix} Rp ${Math.abs(difference).toLocaleString('id-ID')}`;
                validation.className = 'text-red-600';
                return;
            }
            validation.textContent = 'Valid - siap disimpan';
            validation.className = 'text-green-600';
        }

        function finalizeSplitItems(event) {
            if (document.getElementById('tipNota').value !== 'split') return true;
            const mode = document.getElementById('split_mode').value;
            const target = parseInt(document.getElementById('nominal_total').value) || 0;
            const total = splitItemsData.reduce((sum, item) => sum + (mode === 'persen' ? (parseFloat(item.persentase) || 0) : (parseInt(item.nominal) || 0)), 0);
            const ids = splitItemsData.map(item => String(item.divisi_id)).filter(Boolean);
            const valid = splitItemsData.length >= 2 && splitItemsData.length <= 20 && ids.length === splitItemsData.length && new Set(ids).size === ids.length && target > 0 && Math.abs(total - (mode === 'persen' ? 100 : target)) <= 0.001;
            if (!valid) {
                event.preventDefault();
                alert('Periksa divisi dan pembagian. Total harus tepat ' + (mode === 'persen' ? '100%.' : 'sama dengan nominal total.'));
                return false;
            }

            document.querySelectorAll('input[data-split-hidden]').forEach(input => input.remove());
            const form = event.target;
            splitItemsData.forEach((item, index) => {
                ['divisi_id', 'nominal', 'persentase'].forEach(field => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.dataset.splitHidden = '1';
                    input.name = `split_items[${index}][${field}]`;
                    input.value = item[field] || '';
                    form.appendChild(input);
                });
            });
            return true;
        }
    </script>
@endsection
