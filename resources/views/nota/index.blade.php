@extends('layouts.app')

@section('title', 'History Nota')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-end mb-6 flex-wrap gap-4" style="border-bottom: 2px solid #f1f5f9; padding-bottom: 1.5rem;">
            <div>
                <h1 class="text-3xl font-bold text-gray-900" style="letter-spacing: -0.02em;">📋 History Nota</h1>
                <p class="text-gray-600 mt-1">Lihat dan kelola semua nota Anda secara rapi</p>
            </div>
            @can('create', App\Models\Nota::class)
                <a href="{{ route('nota.create') }}"
                    style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 0.75rem; text-decoration: none; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2), 0 2px 4px -1px rgba(16, 185, 129, 0.1); transition: all 0.2s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(16, 185, 129, 0.3), 0 4px 6px -2px rgba(16, 185, 129, 0.15)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(16, 185, 129, 0.2), 0 2px 4px -1px rgba(16, 185, 129, 0.1)';" id="btn_input_nota">
                    <span>+</span> Input Nota Baru
                </a>
            @endcan
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl mb-6 shadow-sm border border-gray-100 p-5 transition-shadow" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);">
            <div style="font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">🔍 Filter & Pencarian</div>
            <form method="GET" action="{{ route('nota.index') }}" class="flex flex-col gap-4">
                
                <!-- Baris ke-1: Pencarian Text & Tipe/Status -->
                <div class="flex gap-4 flex-wrap items-center">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No Nota, Keterangan, Nominal..." 
                        class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white transition-colors flex-grow" style="min-width: 200px; outline: none;">
                    
                    <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white transition-colors"
                        onchange="this.form.submit()" style="min-width: 140px; cursor: pointer; outline: none;">
                        <option value="all">Semua Status</option>
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="tipe" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white transition-colors"
                        onchange="this.form.submit()" style="min-width: 140px; cursor: pointer; outline: none;">
                        <option value="all">Semua Tipe</option>
                        @foreach ($tipes as $t)
                            <option value="{{ $t }}" {{ request('tipe') === $t ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $t)) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="divisi_id" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white transition-colors"
                        onchange="this.form.submit()" style="min-width: 140px; cursor: pointer; outline: none;">
                        <option value="">Semua Divisi</option>
                        @foreach ($divisis as $d)
                            <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Baris ke-2: Filter Waktu Berjenjang -->
                <div class="flex gap-4 flex-wrap items-center" style="border-top: 1px dashed #e2e8f0; padding-top: 1rem;">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Jenis Filter:</span>
                        <select name="filter_type" id="filter_type" onchange="toggleFilterInputs()" 
                            class="px-4 py-2 border border-indigo-100 rounded-lg text-sm bg-indigo-50 font-bold text-indigo-700 outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="all" {{ request('filter_type') == 'all' ? 'selected' : '' }}>Semua Data</option>
                            <option value="date" {{ request('filter_type') == 'date' ? 'selected' : '' }}>Per Tanggal</option>
                            <option value="month" {{ request('filter_type') == 'month' ? 'selected' : '' }}>Per Bulan</option>
                            <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>Per Tahun</option>
                            <option value="custom" {{ request('filter_type') == 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
                        </select>
                    </div>

                    <!-- Input: Per Tanggal -->
                    <div id="div_date" class="hidden flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500">Pilih:</span>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white outline-none">
                    </div>

                    <!-- Input: Per Bulan/Tahun -->
                    <div id="div_month" class="hidden flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500">Bulan:</span>
                        <select name="bulan" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white outline-none">
                            <option value="">--</option>
                            @for ($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" {{ request('bulan', date('n')) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                            @endfor
                        </select>
                        <span class="text-xs font-bold text-gray-500">Tahun:</span>
                        <input type="number" name="tahun" value="{{ request('tahun', date('Y')) }}" placeholder="2024" min="2000" max="2100" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white outline-none" style="width: 100px;">
                    </div>

                    <!-- Input: Per Tahun Only -->
                    <div id="div_year" class="hidden flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500">Tahun:</span>
                        <input type="number" name="tahun" id="tahun_only" value="{{ request('tahun', date('Y')) }}" placeholder="2024" min="2000" max="2100" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white outline-none" style="width: 100px;">
                    </div>

                    <!-- Input: Custom Range -->
                    <div id="div_custom" class="hidden flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500">Dari:</span>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white">
                        <span class="text-xs font-bold text-gray-500">Sampai:</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white">
                    </div>

                    <div class="flex-grow"></div>

                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold transition-all hover:bg-indigo-700 shadow-md">
                        TERAPKAN FILTER
                    </button>

                    <button type="button" onclick="window.location.href='{{ route('nota.index') }}'"
                        class="px-5 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm font-bold hover:bg-gray-200 transition-colors">
                        RESET
                    </button>
                    
                    <script>
                        function toggleFilterInputs() {
                            const type = document.getElementById('filter_type').value;
                            const divs = ['div_date', 'div_month', 'div_year', 'div_custom'];
                            
                            // Hide and Disable All
                            divs.forEach(id => {
                                const el = document.getElementById(id);
                                if (el) {
                                    el.classList.add('hidden');
                                    el.querySelectorAll('input, select').forEach(i => i.disabled = true);
                                }
                            });
                            
                            // Show and Enable Active
                            const active = document.getElementById('div_' + type);
                            if (active) {
                                active.classList.remove('hidden');
                                active.querySelectorAll('input, select').forEach(i => i.disabled = false);
                            }
                        }
                        
                        // Run once on load
                        document.addEventListener('DOMContentLoaded', toggleFilterInputs);
                    </script>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);">
            <table class="w-full" style="border-collapse: separate; border-spacing: 0;">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0; border-top-left-radius: 0.75rem;">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Nomor Nota</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Nominal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Divisi</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0; border-top-right-radius: 0.75rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($notas as $nota)
                        <tr class="transition-colors duration-200" style="cursor: default;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td class="px-6 py-5 text-sm text-gray-600 font-medium">
                                {{ $nota->tanggal_nota->format('d M Y') }}
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-900 font-bold">
                                {{ $nota->nomor_nota ?? '(digital)' }}
                            </td>
                            <td class="px-6 py-5 text-sm">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wide" style="box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.2);">
                                    {{ ucfirst(str_replace('_', ' ', $nota->tipe)) }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-sm font-bold text-gray-900">
                                {{ $nota->nominal_formatted }}
                            </td>
                            <td class="px-6 py-5 text-sm">
                                <div style="zoom: 0.9;">
                                    <x-status-badge :status="$nota->status" />
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-600 font-medium">
                                {{ $nota->divisi->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-5 text-right">
                                <a href="{{ route('nota.show', $nota) }}"
                                    style="display: inline-flex; justify-content: center; align-items: center; width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; color: #10b981; text-decoration: none; background: #ecfdf5; transition: all 0.2s;"
                                    title="Lihat Detail"
                                    onmouseover="this.style.background='#10b981'; this.style.color='white';"
                                    onmouseout="this.style.background='#ecfdf5'; this.style.color='#10b981';">
                                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                    <span style="font-size: 3rem; filter: grayscale(1); opacity: 0.5;">📭</span>
                                    <div>
                                        <p class="text-gray-900 font-bold text-lg">Tidak ada nota ditemukan</p>
                                        <p class="text-gray-500 mt-1">Coba sesuaikan filter pencarian Anda</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($notas->total() > 0)
            <div class="mt-6 flex justify-center custom-pagination">
                <style>
                    /* Fix Tailwinds paginator output that relies on unavailable utilities */
                    .custom-pagination nav { display: flex; flex-direction: column; align-items: center; gap: 1rem; }
                    .custom-pagination nav > div:first-child { display: flex; justify-content: space-between; width: 100%; margin-bottom: 1rem; }
                    .custom-pagination .relative.inline-flex { align-items: center; padding: 0.5rem 1rem; border: 1px solid #e2e8f0; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; color: #475569; background: white; text-decoration: none; transition: all 0.2s;}
                    .custom-pagination .relative.inline-flex:hover { background: #f8fafc; color: #10b981; }
                    .custom-pagination svg { width: 1.25rem; height: 1.25rem; display: inline-block; }
                    .custom-pagination span[aria-disabled="true"] .relative.inline-flex { color: #94a3b8; cursor: not-allowed; background: #f1f5f9; }
                    .custom-pagination [aria-current="page"] span { background: #10b981; color: white; border-color: #10b981; }
                    @media (min-width: 640px) {
                        .custom-pagination nav > div:first-child { display: none; }
                        .custom-pagination nav > div:last-child { display: flex; align-items: center; justify-content: space-between; width: 100%; }
                        .custom-pagination nav span.relative.z-0.inline-flex { display: flex; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); border-radius: 0.375rem; overflow: hidden; }
                        .custom-pagination nav span.relative.z-0.inline-flex > * { margin-left: -1px; border-radius: 0; padding: 0.5rem 1rem; border: 1px solid #e2e8f0; background: white; color: #475569; font-size: 0.875rem; font-weight: 500; text-decoration: none; cursor:pointer;}
                        .custom-pagination nav span.relative.z-0.inline-flex > *:hover:not(span[aria-disabled]) { background: #f8fafc; color: #10b981; }
                        .custom-pagination nav span.relative.z-0.inline-flex > span[aria-current="page"] { background: #10b981; color: white; z-index: 10; border-color: #10b981; }
                    }
                </style>
                {{ $notas->links() }}
            </div>
        @endif
    </div>
@endsection
