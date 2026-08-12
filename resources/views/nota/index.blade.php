@extends('layouts.app')

@section('title', 'History Nota')

@section('content')
    <div class="mb-6">
        <div class="mb-6 flex flex-col items-stretch gap-4 border-b-2 border-gray-100 pb-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">📋 History Nota</h1>
                <p class="text-gray-600 mt-1">Lihat dan kelola semua nota Anda secara rapi</p>
            </div>
            @can('create', App\Models\Nota::class)
                <a href="{{ route('nota.create') }}" class="inline-flex w-full items-center justify-center gap-2 sm:w-auto"
                    style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 0.75rem; text-decoration: none; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2), 0 2px 4px -1px rgba(16, 185, 129, 0.1); transition: all 0.2s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(16, 185, 129, 0.3), 0 4px 6px -2px rgba(16, 185, 129, 0.15)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(16, 185, 129, 0.2), 0 2px 4px -1px rgba(16, 185, 129, 0.1)';" id="btn_input_nota">
                    <span>+</span> Input Nota Baru
                </a>
            @endcan
        </div>

        <!-- Filter Section -->
        @php
            $hasActiveFilter = collect(request()->only([
                'search', 'status', 'tipe', 'divisi_id', 'filter_type', 'tanggal', 'bulan', 'tahun', 'start_date', 'end_date',
            ]))->filter(fn ($value) => filled($value) && $value !== 'all')->isNotEmpty();
        @endphp
        <div class="filter-toolbar mb-6 p-0">
            <button type="button" id="historyFilterButton" aria-controls="historyFilterPanel"
                aria-expanded="{{ $hasActiveFilter ? 'true' : 'false' }}"
                class="flex w-full items-center justify-between px-4 py-4 text-left text-sm font-semibold uppercase text-slate-600 md:hidden">
                <span>🔍 Filter & Pencarian</span>
                <svg id="historyFilterChevron" class="h-5 w-5 transition-transform {{ $hasActiveFilter ? 'rotate-180' : '' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="historyFilterPanel" class="{{ $hasActiveFilter ? '' : 'hidden' }} border-t border-gray-100 p-4 md:block md:border-t-0 md:p-5">
                <div class="mb-4 hidden text-sm font-semibold uppercase text-slate-500 md:block">🔍 Filter & Pencarian</div>
                <form method="GET" action="{{ route('nota.index') }}" class="flex flex-col gap-4">
                
                <!-- Baris ke-1: Pencarian Text & Tipe/Status -->
                <div class="grid gap-3 md:grid-cols-[minmax(240px,1fr)_repeat(4,minmax(135px,auto))]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No Nota, Keterangan, Nominal..." 
                        autocomplete="off" class="filter-control">
                    
                    <select name="status" class="filter-control cursor-pointer" onchange="this.form.submit()">
                        <option value="all">Semua Status</option>
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="tipe" class="filter-control cursor-pointer" onchange="this.form.submit()">
                        <option value="all">Semua Tipe</option>
                        @foreach ($tipes as $t)
                            <option value="{{ $t }}" {{ request('tipe') === $t ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $t)) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="divisi_id" class="filter-control cursor-pointer" onchange="this.form.submit()">
                        <option value="">Semua Divisi</option>
                        @foreach ($divisis as $d)
                            <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->nama }}
                            </option>
                        @endforeach
                    </select>

                    <label class="filter-control flex items-center justify-between gap-2 whitespace-nowrap">
                        Tampilkan
                        <select name="per_page" onchange="this.form.submit()" class="border-0 bg-transparent p-0 text-sm font-bold outline-none focus:ring-0">
                            @foreach ([10, 15, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @selected($notas->perPage() === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                
                <!-- Baris ke-2: Filter Waktu Berjenjang -->
                <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 md:flex-row md:flex-wrap md:items-end">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:gap-3">
                        <span class="filter-label">Jenis Filter</span>
                        <select name="filter_type" id="filter_type" onchange="toggleFilterInputs()" 
                            class="filter-control md:w-44">
                            <option value="all" {{ request('filter_type') == 'all' ? 'selected' : '' }}>Semua Data</option>
                            <option value="date" {{ request('filter_type') == 'date' ? 'selected' : '' }}>Per Tanggal</option>
                            <option value="month" {{ request('filter_type') == 'month' ? 'selected' : '' }}>Per Bulan</option>
                            <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>Per Tahun</option>
                            <option value="custom" {{ request('filter_type') == 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
                        </select>
                    </div>

                    <!-- Input: Per Tanggal -->
                    <div id="div_date" class="hidden flex flex-col gap-2 md:flex-row md:items-center">
                        <span class="text-xs font-bold text-gray-500">Pilih:</span>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="filter-control">
                    </div>

                    <!-- Input: Per Bulan/Tahun -->
                    <div id="div_month" class="hidden flex flex-col gap-2 md:flex-row md:items-center">
                        <span class="text-xs font-bold text-gray-500">Bulan:</span>
                        <select name="bulan" class="filter-control md:w-36">
                            <option value="">--</option>
                            @for ($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" {{ request('bulan', date('n')) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                            @endfor
                        </select>
                        <span class="text-xs font-bold text-gray-500">Tahun:</span>
                        <input type="number" name="tahun" value="{{ request('tahun', date('Y')) }}" placeholder="2024" min="2000" max="2100" class="filter-control md:w-28">
                    </div>

                    <!-- Input: Per Tahun Only -->
                    <div id="div_year" class="hidden flex flex-col gap-2 md:flex-row md:items-center">
                        <span class="text-xs font-bold text-gray-500">Tahun:</span>
                        <input type="number" name="tahun" id="tahun_only" value="{{ request('tahun', date('Y')) }}" placeholder="2024" min="2000" max="2100" class="filter-control md:w-28">
                    </div>

                    <!-- Input: Custom Range -->
                    <div id="div_custom" class="hidden flex flex-col gap-2 md:flex-row md:items-center">
                        <span class="text-xs font-bold text-gray-500">Dari:</span>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="filter-control">
                        <span class="text-xs font-bold text-gray-500">Sampai:</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="filter-control">
                    </div>

                    <div class="flex-grow"></div>

                    <button type="submit" class="filter-button-primary w-full md:w-auto">
                        TERAPKAN FILTER
                    </button>

                    <button type="button" onclick="window.location.href='{{ route('nota.index') }}'"
                        class="filter-button-secondary w-full md:w-auto">
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
                        document.addEventListener('DOMContentLoaded', () => {
                            toggleFilterInputs();

                            const button = document.getElementById('historyFilterButton');
                            const panel = document.getElementById('historyFilterPanel');
                            const chevron = document.getElementById('historyFilterChevron');

                            button?.addEventListener('click', () => {
                                const isOpen = !panel.classList.contains('hidden');
                                panel.classList.toggle('hidden');
                                button.setAttribute('aria-expanded', String(!isOpen));
                                chevron.classList.toggle('rotate-180');
                            });
                        });
                    </script>
                </div>
                </form>
            </div>
        </div>

        <!-- Mobile List -->
        <div class="divide-y divide-gray-100 border border-gray-100 bg-white shadow-sm md:hidden">
            @forelse($notas as $nota)
                <a href="{{ route('nota.show', $nota) }}" class="block px-4 py-4 transition-colors active:bg-gray-50">
                    <div class="mb-2 flex items-start justify-between gap-3">
                        <span class="text-xs font-medium text-gray-500">{{ $nota->tanggal_nota->format('d M Y') }}</span>
                        <x-status-badge :status="$nota->status" class="shrink-0" />
                    </div>
                    <div class="flex items-baseline justify-between gap-3">
                        <span class="min-w-0 truncate text-sm font-bold text-gray-900">{{ $nota->nomor_nota ?? '(digital)' }}</span>
                        <span class="shrink-0 text-base font-bold text-gray-900">{{ $nota->nominal_formatted }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between gap-3 text-xs text-gray-500">
                        <span class="min-w-0 truncate">{{ $nota->divisi->nama ?? '-' }} · {{ ucfirst(str_replace('_', ' ', $nota->tipe)) }}</span>
                        <svg class="h-5 w-5 shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            @empty
                <div class="px-4 py-12 text-center">
                    <p class="text-lg font-bold text-gray-900">Tidak ada nota ditemukan</p>
                    <p class="mt-1 text-sm text-gray-500">Coba sesuaikan filter pencarian Anda</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden overflow-x-auto rounded-3xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50 md:block">
            <table class="w-full min-w-[900px] text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Nomor Nota</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Nominal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Divisi</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">Aksi</th>
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
            <div class="custom-pagination mt-6">
                <p class="mb-3 text-sm text-gray-500 md:mb-0">Menampilkan {{ $notas->firstItem() }}-{{ $notas->lastItem() }} dari {{ $notas->total() }} data</p>
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
