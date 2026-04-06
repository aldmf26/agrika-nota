@extends('layouts.app')

@section('title', 'Rekap Laporan Nota')

@section('content')
<div class="px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">📊 Rekap Laporan Nota</h1>
            <p class="text-slate-500 mt-1">Ringkasan pengeluaran nota per divisi dan bulan untuk tahun <span class="font-bold text-indigo-600">{{ $tahun }}</span>.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <!-- Year Selector -->
            <form action="{{ route('admin.reports.index') }}" method="GET" class="flex items-center gap-2">
                <select name="tahun" onchange="this.form.submit()" class="bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 px-4 shadow-sm transition-all duration-200">
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
            <div class="dropdown relative inline-block">
                <button id="exportDropdown" type="button" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-emerald-200 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0">
                    📥 Export Excel
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 z-50 overflow-hidden animate-in fade-in slide-in-from-top-2">
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
            <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest mb-2">Total Tahun {{ $tahun }}</p>
            <p class="text-3xl font-black text-slate-900 tabular-nums">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
            <div class="mt-4 flex items-center text-xs font-medium text-indigo-600">
                <span class="bg-indigo-50 px-2 py-1 rounded-lg">Approved Status Only</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
            <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest mb-2">Bulan Terbesar</p>
            @php
                $maxMonthVal = max($monthlyTotals ?: [0]);
                $maxMonth = array_search($maxMonthVal, $monthlyTotals) ?: 1;
                $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            @endphp
            <p class="text-3xl font-black text-slate-900">{{ $maxMonthVal > 0 ? $monthNames[$maxMonth] : '-' }}</p>
            <p class="text-sm font-medium text-emerald-600 mt-2">Rp {{ number_format($maxMonthVal, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
            <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest mb-2">Divisi Teraktif</p>
            @php
                if (!empty($divisiTotals) && max($divisiTotals) > 0) {
                    $maxDivId = array_search(max($divisiTotals), $divisiTotals);
                    $maxDivName = $divisis->firstWhere('id', $maxDivId)->nama;
                } else {
                    $maxDivName = '-';
                }
            @endphp
            <p class="text-3xl font-black text-slate-900 truncate" title="{{ $maxDivName }}">{{ $maxDivName }}</p>
            <p class="text-sm font-medium text-rose-600 mt-2">Rp {{ number_format(!empty($divisiTotals) ? max($divisiTotals) : 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Main Report Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
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
                            {{ $val > 0 ? number_format($val/1000, 0, ',', '.') . 'k' : '-' }}
                        </td>
                        @endfor
                        <td class="px-6 py-4 text-right font-bold text-indigo-700 bg-indigo-50/30 sticky right-0 z-10 tabular-nums border-l border-slate-200">
                            {{ number_format($divisiTotals[$divisi->id], 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[10px]">
                        <td class="px-6 py-4 sticky left-0 bg-slate-900 z-10">TOTAL BULANAN</td>
                        @for($i=1; $i<=12; $i++)
                        <td class="px-4 py-4 text-center tabular-nums">
                            {{ number_format($monthlyTotals[$i], 0, ',', '.') }}
                        </td>
                        @endfor
                        <td class="px-6 py-4 text-right bg-indigo-600 sticky right-0 z-10 tabular-nums text-sm">
                            {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="mt-4 flex items-center gap-2 text-xs text-slate-400 font-medium">
        <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
        <span>Angka dalam tabel (k) menunjukkan ribuan rupiah. Total ditampilkan dalam angka penuh.</span>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('exportDropdown');
        const menu = document.getElementById('exportMenu');
        
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        });
        
        document.addEventListener('click', function() {
            menu.classList.add('hidden');
        });
    });
</script>

<style>
    .tabular-nums {
        font-variant-numeric: tabular-nums;
    }
</style>
@endsection
