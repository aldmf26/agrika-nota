@extends('layouts.app')

@section('title', 'Rekap Laporan Nota')

@section('content')
<div class="px-0 py-2 sm:px-4 sm:py-8">
    <div class="mb-6 flex flex-col gap-4 md:mb-8 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">📊 Rekap Laporan Nota</h1>
            <p class="mt-1 text-sm text-slate-500 sm:text-base">Pengeluaran per divisi tahun <span class="font-bold text-indigo-600">{{ $tahun }}</span>.</p>
        </div>
        
        <div class="filter-toolbar grid grid-cols-2 gap-3 p-3 md:flex md:flex-wrap md:items-center">
            <!-- Year Selector -->
            <form action="{{ route('admin.reports.index') }}" method="GET" class="flex min-w-0 items-center">
                <select name="tahun" onchange="this.form.submit()" class="filter-control md:w-auto">
                    @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                    @if(!in_array(date('Y'), $availableYears))
                    <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>Tahun {{ date('Y') }}</option>
                    @endif
                </select>
            </form>

            <div class="h-8 w-px bg-slate-200 mx-1 hidden md:block"></div>

            <!-- Export Buttons -->
            <div class="dropdown relative min-w-0">
                <button id="exportDropdown" type="button" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-200 md:w-auto md:px-5">
                    📥 Export Excel
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="exportMenu" class="absolute right-0 z-50 mt-2 hidden w-56 overflow-hidden rounded-lg border border-slate-100 bg-white shadow-xl">
                    <a href="{{ route('admin.reports.export', ['tahun' => $tahun, 'type' => 'summary']) }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                        📊 Export Rekap (Summary)
                    </a>
                    <a href="{{ route('admin.reports.export', ['tahun' => $tahun, 'type' => 'detail']) }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                        📝 Export Detail Approved
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="mb-8 grid grid-cols-2 gap-3 sm:gap-6 md:mb-10 md:grid-cols-3">
        <div class="group relative col-span-2 overflow-hidden border border-slate-100 bg-white p-5 shadow-sm md:col-span-1 md:p-6">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
            <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest mb-2">Total Tahun {{ $tahun }}</p>
            <button type="button" data-report-details data-year="{{ $tahun }}"
                class="break-words text-left text-2xl font-black text-indigo-700 underline decoration-indigo-200 underline-offset-4 tabular-nums hover:text-indigo-900 sm:text-3xl">
                Rp {{ number_format($grandTotal, 0, ',', '.') }}
            </button>
            <div class="mt-4 flex items-center text-xs font-medium text-indigo-600">
                <span class="bg-indigo-50 px-2 py-1 rounded-lg">Approved Status Only</span>
            </div>
        </div>

        <div class="group relative min-w-0 overflow-hidden border border-slate-100 bg-white p-4 shadow-sm sm:p-6">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
            <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest mb-2">Bulan Terbesar</p>
            @php
                $maxMonthVal = max($monthlyTotals ?: [0]);
                $maxMonth = array_search($maxMonthVal, $monthlyTotals) ?: 1;
                $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            @endphp
            <p class="truncate text-xl font-black text-slate-900 sm:text-3xl">{{ $maxMonthVal > 0 ? $monthNames[$maxMonth] : '-' }}</p>
            @if($maxMonthVal > 0)
                <button type="button" data-report-details data-year="{{ $tahun }}" data-month="{{ $maxMonth }}"
                    class="mt-2 break-words text-left text-xs font-bold text-emerald-700 underline decoration-emerald-200 underline-offset-4 hover:text-emerald-900 sm:text-sm">
                    Rp {{ number_format($maxMonthVal, 0, ',', '.') }}
                </button>
            @else
                <p class="mt-2 text-sm font-medium text-emerald-600">Rp 0</p>
            @endif
        </div>

        <div class="group relative min-w-0 overflow-hidden border border-slate-100 bg-white p-4 shadow-sm sm:p-6">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
            <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest mb-2">Divisi Teraktif</p>
            @php
                if (!empty($divisiTotals) && max($divisiTotals) > 0) {
                    $maxDivId = array_search(max($divisiTotals), $divisiTotals);
                    $maxDivName = $divisis->firstWhere('id', $maxDivId)->nama;
                } else {
                    $maxDivId = null;
                    $maxDivName = '-';
                }
            @endphp
            <p class="truncate text-xl font-black text-slate-900 sm:text-3xl" title="{{ $maxDivName }}">{{ $maxDivName }}</p>
            @if($maxDivId)
                <button type="button" data-report-details data-year="{{ $tahun }}" data-division="{{ $maxDivId }}"
                    class="mt-2 break-words text-left text-xs font-bold text-rose-700 underline decoration-rose-200 underline-offset-4 hover:text-rose-900 sm:text-sm">
                    Rp {{ number_format(max($divisiTotals), 0, ',', '.') }}
                </button>
            @else
                <p class="mt-2 text-sm font-medium text-rose-600">Rp 0</p>
            @endif
        </div>
    </div>

    <!-- Mobile Monthly Ranking -->
    @php
        $defaultMonth = (int) date('n');
        $mobileRankings = [];

        for ($month = 1; $month <= 12; $month++) {
            $mobileRankings[$month] = $divisis->map(fn ($divisi) => [
                'id' => $divisi->id,
                'nama' => $divisi->nama,
                'total' => $matrix[$divisi->id][$month] ?? 0,
            ])->filter(fn ($row) => $row['total'] > 0)->sortByDesc('total')->values();
        }
    @endphp
    <section class="border border-slate-100 bg-white shadow-sm md:hidden">
        <div class="border-b border-slate-100 p-4">
            <label for="mobileReportMonth" class="mb-2 block text-xs font-bold uppercase text-slate-500">Pilih Bulan</label>
            <select id="mobileReportMonth" class="w-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 focus:border-indigo-500 focus:ring-indigo-500">
                @for($month = 1; $month <= 12; $month++)
                    <option value="{{ $month }}" {{ $defaultMonth === $month ? 'selected' : '' }}>{{ $monthNames[$month] }}</option>
                @endfor
            </select>
        </div>

        <div class="flex items-end justify-between gap-3 bg-indigo-50 px-4 py-4">
            <div>
                <p class="text-xs font-bold uppercase text-indigo-500">Total Bulan</p>
                <p id="mobileReportMonthName" class="mt-1 text-sm font-semibold text-slate-700">{{ $monthNames[$defaultMonth] }} {{ $tahun }}</p>
            </div>
            <button type="button" id="mobileReportTotal" data-report-details data-year="{{ $tahun }}" data-month="{{ $defaultMonth }}"
                class="text-right text-xl font-black text-indigo-700 underline decoration-indigo-200 underline-offset-4 tabular-nums"
                data-totals='@json($monthlyTotals)'>Rp {{ number_format($monthlyTotals[$defaultMonth] ?? 0, 0, ',', '.') }}</button>
        </div>

        @for($month = 1; $month <= 12; $month++)
            <div id="mobileReportPanel{{ $month }}" class="mobile-report-panel {{ $defaultMonth === $month ? '' : 'hidden' }} divide-y divide-slate-100">
                @forelse($mobileRankings[$month] as $index => $row)
                    <div class="flex items-center gap-3 px-4 py-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">{{ $index + 1 }}</span>
                        <span class="min-w-0 flex-1 truncate text-sm font-semibold text-slate-800">{{ $row['nama'] }}</span>
                        <button type="button" data-report-details data-year="{{ $tahun }}" data-month="{{ $month }}"
                            data-division="{{ $row['id'] }}"
                            class="shrink-0 text-sm font-bold text-indigo-700 underline decoration-indigo-200 underline-offset-4 tabular-nums">
                            Rp {{ number_format($row['total'], 0, ',', '.') }}
                        </button>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center">
                        <p class="font-bold text-slate-800">Belum ada transaksi</p>
                        <p class="mt-1 text-sm text-slate-500">Tidak ada nota approved pada bulan ini.</p>
                    </div>
                @endforelse
            </div>
        @endfor
    </section>

    <!-- Desktop Report Table -->
    <div class="hidden overflow-hidden border border-slate-100 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-5 font-bold text-slate-700 sticky left-0 bg-slate-50 z-10 w-48">DIVISI</th>
                        @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $mName)
                        <th class="px-4 py-5 font-bold text-slate-700 text-center min-w-[100px]">{{ $mName }}</th>
                        @endforeach
                        <th class="px-6 py-5 font-bold text-slate-900 text-right bg-slate-100 sticky right-0 z-10 w-32 border-l border-slate-200">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($divisis as $divisi)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4 font-semibold text-slate-900 sticky left-0 bg-white group-hover:bg-slate-50 z-10 border-r border-slate-100">
                            {{ $divisi->nama }}
                        </td>
                        @for($i=1; $i<=12; $i++)
                        @php $val = $matrix[$divisi->id][$i] ?? 0; @endphp
                        <td class="px-4 py-4 text-center tabular-nums {{ $val > 0 ? 'text-slate-900 font-medium' : 'text-slate-300' }}">
                            @if($val > 0)
                                <button type="button" data-report-details data-year="{{ $tahun }}" data-month="{{ $i }}"
                                    data-division="{{ $divisi->id }}"
                                    class="font-bold text-indigo-700 underline decoration-indigo-200 underline-offset-4 hover:text-indigo-900">
                                    {{ number_format($val/1000, 0, ',', '.') }}k
                                </button>
                            @else
                                -
                            @endif
                        </td>
                        @endfor
                        <td class="px-6 py-4 text-right font-bold text-indigo-700 bg-indigo-50/30 sticky right-0 z-10 tabular-nums border-l border-slate-200">
                            @if($divisiTotals[$divisi->id] > 0)
                                <button type="button" data-report-details data-year="{{ $tahun }}" data-division="{{ $divisi->id }}"
                                    class="font-bold underline decoration-indigo-200 underline-offset-4 hover:text-indigo-900">
                                    {{ number_format($divisiTotals[$divisi->id], 0, ',', '.') }}
                                </button>
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[10px]">
                        <td class="px-6 py-4 sticky left-0 bg-slate-900 z-10">TOTAL BULANAN</td>
                        @for($i=1; $i<=12; $i++)
                        <td class="px-4 py-4 text-center tabular-nums">
                            @if($monthlyTotals[$i] > 0)
                                <button type="button" data-report-details data-year="{{ $tahun }}" data-month="{{ $i }}"
                                    class="font-bold underline decoration-slate-500 underline-offset-4 hover:text-indigo-200">
                                    {{ number_format($monthlyTotals[$i], 0, ',', '.') }}
                                </button>
                            @else
                                0
                            @endif
                        </td>
                        @endfor
                        <td class="px-6 py-4 text-right bg-indigo-600 sticky right-0 z-10 tabular-nums text-sm">
                            <button type="button" data-report-details data-year="{{ $tahun }}"
                                class="font-bold underline decoration-indigo-300 underline-offset-4 hover:text-indigo-100">
                                {{ number_format($grandTotal, 0, ',', '.') }}
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="mt-4 hidden items-center gap-2 text-xs font-medium text-slate-400 md:flex">
        <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
        <span>Angka dalam tabel (k) menunjukkan ribuan rupiah. Total ditampilkan dalam angka penuh.</span>
    </div>
</div>

<div id="reportDetailModal" class="fixed inset-0 z-50 hidden items-end justify-center bg-black/50 p-0 sm:items-center sm:p-4"
    role="dialog" aria-modal="true" aria-labelledby="reportDetailModalTitle">
    <div class="max-h-[92vh] w-full overflow-hidden bg-white shadow-2xl sm:max-w-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 sm:px-6">
            <span id="reportDetailModalTitle" class="text-sm font-bold text-slate-700">Rincian Nota</span>
            <button type="button" id="closeReportDetailModal"
                class="flex h-10 w-10 items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-slate-900"
                title="Tutup">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="sr-only">Tutup rincian laporan</span>
            </button>
        </div>
        <div id="reportDetailContent">
            <div class="px-4 py-12 text-center text-sm text-slate-500">Memuat rincian...</div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('exportDropdown');
        const menu = document.getElementById('exportMenu');
        
        btn?.addEventListener('click', function(e) {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        });
        
        document.addEventListener('click', function() {
            menu?.classList.add('hidden');
        });

        const monthSelect = document.getElementById('mobileReportMonth');
        const totalElement = document.getElementById('mobileReportTotal');
        const monthNameElement = document.getElementById('mobileReportMonthName');
        const monthNames = @json($monthNames);
        const reportYear = @json($tahun);

        monthSelect?.addEventListener('change', function() {
            const month = this.value;
            const totals = JSON.parse(totalElement.dataset.totals);

            document.querySelectorAll('.mobile-report-panel').forEach(panel => panel.classList.add('hidden'));
            document.getElementById(`mobileReportPanel${month}`)?.classList.remove('hidden');

            monthNameElement.textContent = `${monthNames[month]} ${reportYear}`;
            totalElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totals[month] ?? 0);
            totalElement.dataset.month = month;
        });

        const detailModal = document.getElementById('reportDetailModal');
        const detailContent = document.getElementById('reportDetailContent');
        const closeDetailButton = document.getElementById('closeReportDetailModal');
        const detailUrl = @json(route('admin.reports.details'));

        function closeReportDetails() {
            detailModal.classList.add('hidden');
            detailModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        async function openReportDetails(trigger) {
            const params = new URLSearchParams({ tahun: trigger.dataset.year });
            if (trigger.dataset.month) params.set('bulan', trigger.dataset.month);
            if (trigger.dataset.division) params.set('divisi_id', trigger.dataset.division);

            detailContent.innerHTML = '<div class="px-4 py-12 text-center text-sm text-slate-500">Memuat rincian...</div>';
            detailModal.classList.remove('hidden');
            detailModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');

            try {
                const response = await fetch(`${detailUrl}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!response.ok) throw new Error('Gagal memuat rincian laporan');
                detailContent.innerHTML = await response.text();
            } catch (error) {
                detailContent.innerHTML = '<div class="px-4 py-12 text-center"><p class="font-bold text-red-700">Rincian gagal dimuat</p><p class="mt-1 text-sm text-slate-500">Silakan tutup lalu coba lagi.</p></div>';
            }
        }

        document.addEventListener('click', function(event) {
            const trigger = event.target.closest('[data-report-details]');
            if (trigger) openReportDetails(trigger);
        });

        closeDetailButton?.addEventListener('click', closeReportDetails);
        detailModal?.addEventListener('click', function(event) {
            if (event.target === detailModal) closeReportDetails();
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !detailModal.classList.contains('hidden')) closeReportDetails();
        });
    });
</script>

<style>
    .tabular-nums {
        font-variant-numeric: tabular-nums;
    }
</style>
@endsection
