@extends('layouts.app')

@section('title', 'Input Nota Baru')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">📝 Input Nota Baru</h1>
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
                <div class="grid grid-cols-2 gap-4">
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

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Divisi <span class="text-red-500">*</span>
                        </label>
                        <select name="divisi_id" id="divisi_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            onchange="updateNomorNota()" required>
                            <option value="">-- Pilih Divisi --</option>
                            @foreach ($divisis as $d)
                                <option value="{{ $d->id }}" {{ old('divisi_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('divisi_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
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
                    <div class="grid grid-cols-2 gap-4">
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
                    <div class="grid grid-cols-2 gap-4">
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
                    <div class="grid grid-cols-2 gap-4">
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
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left">Divisi</th>
                                    <th class="px-4 py-3 text-right">Nominal (Rp)</th>
                                    <th class="px-4 py-3 text-center w-16">Aksi</th>
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
                        Foto Lampiran <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-green-500 transition-colors"
                        onclick="document.getElementById('attachments').click()">
                        <input type="file" id="attachments" name="attachments[]" class="hidden"
                            accept=".jpg,.jpeg,.png,.heic" multiple required>
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
                <div class="flex gap-4 pt-4">
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

    <script>
        const divisiData = @json($divisis);
        const originalNomorNota = "{{ $nomorNota }}"; // Tanpa kode prefix
        let splitItemsData = [];

        function updateNomorNota() {
            const divisiId = document.getElementById('divisi_id').value;
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
                nominal: ''
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

        // Update data dan re-render (untuk onchange dropdown)
        function updateSplitItem(id, field, value) {
            updateSplitItemData(id, field, value);
            renderSplitItems();
        }

        function renderSplitItems() {
            const tbody = document.getElementById('splitItemsBody');
            tbody.innerHTML = '';

            splitItemsData.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                            <td class="px-4 py-3">
                                <select class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                    onchange="updateSplitItem(${item.id}, 'divisi_id', this.value)" required>
                                    <option value="">-- Pilih Divisi --</option>
                                    ${divisiData.map(d => `<option value="${d.id}" ${item.divisi_id == d.id ? 'selected' : ''}>${d.nama}</option>`).join('')}
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm text-right focus:outline-none focus:ring-2 focus:ring-green-500"
                                    value="${item.nominal ? new Intl.NumberFormat('id-ID').format(item.nominal) : ''}"
                                    onkeyup="const val = this.value.replace(/\\D/g, ''); updateSplitItemData(${item.id}, 'nominal', val); formatCurrencyEl(this);"
                                    placeholder="0"
                                    required>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onclick="removeSplitItem(${item.id})"
                                    class="text-red-500 hover:text-red-700 font-semibold text-lg">
                                    ✕
                                </button>
                            </td>
                        `;
                tbody.appendChild(row);
            });

            updateSplitItemCount();
            calculateSplitTotal();
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
    </script>
@endsection