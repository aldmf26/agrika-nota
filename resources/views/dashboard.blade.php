@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="min-h-screen bg-slate-50 py-2 sm:px-4 sm:py-8">
        <div class="container max-w-7xl mx-auto">
            <div class="mb-6 flex flex-col items-stretch justify-between gap-4 sm:mb-10 sm:flex-row sm:items-end">
                <div>
                    <h1 class="mb-2 text-2xl font-extrabold text-slate-900 sm:text-4xl">
                        Dashboard Interaktif
                    </h1>
                    <p style="color: #64748b; font-size: 1rem;">
                        Selamat datang kembali, <strong style="color: #0f172a;">{{ Auth::user()->name }}</strong> 👋
                    </p>
                </div>
                @can('create', App\Models\Nota::class)

                    <div class="flex">
                        <a href="{{ route('nota.create') }}"
                            class="w-full justify-center sm:w-auto"
                            style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 0.75rem; text-decoration: none; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2), 0 2px 4px -1px rgba(16, 185, 129, 0.1); transition: all 0.2s;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(16, 185, 129, 0.3), 0 4px 6px -2px rgba(16, 185, 129, 0.15)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(16, 185, 129, 0.2), 0 2px 4px -1px rgba(16, 185, 129, 0.1)'; ">
                            <span>+</span> Buat Nota Baru
                        </a>
                    </div>
                @endcan
            </div>

            <!-- Stats Grid -->
            <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:mb-12 xl:grid-cols-4">
                <!-- Total Nota -->
                <div style="background: white; padding: 1.75rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); border: 1px solid #f1f5f9; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;"
                    onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)';">
                    <div
                        style="position: absolute; top: 0; right: 0; padding: 1rem; opacity: 0.1; font-size: 4rem; user-select: none;">
                        📋</div>
                    <div style="display: flex; flex-direction: column; position: relative; z-index: 10;">
                        <div
                            style="width: 48px; height: 48px; border-radius: 0.75rem; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem;">
                            📋
                        </div>
                        <p
                            style="color: #64748b; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            Total Nota</p>
                        <p style="font-size: 2.25rem; font-weight: 800; color: #1e293b; line-height: 1;">
                            {{ \App\Models\Nota::count() }}
                        </p>
                    </div>
                </div>

                <!-- Draft Nota -->
                <div style="background: white; padding: 1.75rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); border: 1px solid #f1f5f9; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;"
                    onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)';">
                    <div
                        style="position: absolute; top: 0; right: 0; padding: 1rem; opacity: 0.1; font-size: 4rem; user-select: none;">
                        ✏️</div>
                    <div style="display: flex; flex-direction: column; position: relative; z-index: 10;">
                        <div
                            style="width: 48px; height: 48px; border-radius: 0.75rem; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem;">
                            ✏️
                        </div>
                        <p
                            style="color: #64748b; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            Draft</p>
                        <p style="font-size: 2.25rem; font-weight: 800; color: #1e293b; line-height: 1;">
                            {{ \App\Models\Nota::where('status', 'draft')->count() }}
                        </p>
                    </div>
                </div>

                <!-- Pending Approval -->
                <div style="background: white; padding: 1.75rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); border: 1px solid #f1f5f9; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;"
                    onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)';">
                    <div
                        style="position: absolute; top: 0; right: 0; padding: 1rem; opacity: 0.1; font-size: 4rem; user-select: none;">
                        ⏳</div>
                    <div style="display: flex; flex-direction: column; position: relative; z-index: 10;">
                        <div
                            style="width: 48px; height: 48px; border-radius: 0.75rem; background: #fffbeb; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem;">
                            ⏳
                        </div>
                        <p
                            style="color: #64748b; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            Pending Approval</p>
                        <p style="font-size: 2.25rem; font-weight: 800; color: #1e293b; line-height: 1;">
                            {{ \App\Models\Nota::where('status', 'pending')->count() }}
                        </p>
                    </div>
                </div>

                <!-- Approved Nota -->
                <div style="background: white; padding: 1.75rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); border: 1px solid #f1f5f9; position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;"
                    onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)';">
                    <div
                        style="position: absolute; top: 0; right: 0; padding: 1rem; opacity: 0.1; font-size: 4rem; user-select: none;">
                        ✅</div>
                    <div style="display: flex; flex-direction: column; position: relative; z-index: 10;">
                        <div
                            style="width: 48px; height: 48px; border-radius: 0.75rem; background: #f0fdf4; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem;">
                            ✅
                        </div>
                        <p
                            style="color: #64748b; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            Approved</p>
                        <p style="font-size: 2.25rem; font-weight: 800; color: #1e293b; line-height: 1;">
                            {{ \App\Models\Nota::where('status', 'approved')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content Area: Recent Notes -->
            <div class="border border-slate-200 bg-white p-4 shadow-sm sm:p-8">
                <div class="mb-6 flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900 sm:text-2xl">
                        <span style="font-size: 1.5rem;">📝</span> Nota Terbaru
                    </h2>
                    <a href="{{ route('nota.index') }}"
                        style="display: inline-flex; align-items: center; gap: 0.25rem; color: #10b981; text-decoration: none; font-weight: 600; font-size: 0.875rem; transition: color 0.2s;"
                        onmouseover="this.style.color='#059669'" onmouseout="this.style.color='#10b981'">
                        Lihat Semua <span style="font-size: 1.25rem;">→</span>
                    </a>
                </div>

                @php
                    $recentNotas = \App\Models\Nota::with('user', 'divisi')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp

                @if ($recentNotas->count() > 0)
                    <div class="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
                        <table class="min-w-[720px]" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th
                                        style="padding: 1rem 1.5rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0; border-top-left-radius: 0.5rem;">
                                        Nomor</th>
                                    <th
                                        style="padding: 1rem 1.5rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0;">
                                        Tanggal</th>
                                    <th
                                        style="padding: 1rem 1.5rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0;">
                                        Tipe</th>
                                    <th
                                        style="padding: 1rem 1.5rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0;">
                                        Nominal</th>
                                    <th
                                        style="padding: 1rem 1.5rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0;">
                                        Status</th>
                                    <th
                                        style="padding: 1rem 1.5rem; text-align: right; color: #64748b; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0; border-top-right-radius: 0.5rem;">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentNotas as $nota)
                                    <tr style="transition: background-color 0.2s;" onmouseover="this.style.background='#f8fafc'"
                                        onmouseout="this.style.background='transparent'">
                                        <td style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
                                            <a href="{{ route('nota.show', $nota) }}"
                                                style="color: #0f172a; text-decoration: none; font-weight: 600; display: inline-block; transition: color 0.2s;"
                                                onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#0f172a'">
                                                {{ $nota->nomor_nota }}
                                            </a>
                                        </td>
                                        <td
                                            style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 0.95rem;">
                                            {{ $nota->created_at->format('d M Y') }}
                                        </td>
                                        <td style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
                                            <span
                                                style="background: #f1f5f9; color: #475569; padding: 0.35rem 0.75rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                                {{ ucfirst($nota->tipe) }}
                                            </span>
                                        </td>
                                        <td
                                            style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: 700;">
                                            {{ $nota->nominal_formatted }}
                                        </td>
                                        <td style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
                                            @php
                                                $statusColor = match ($nota->status) {
                                                    'approved' => '#dcfce7',
                                                    'pending' => '#fef3c7',
                                                    'rejected' => '#fee2e2',
                                                    default => '#f1f5f9',
                                                };
                                                $statusTextColor = match ($nota->status) {
                                                    'approved' => '#15803d',
                                                    'pending' => '#b45309',
                                                    'rejected' => '#b91c1c',
                                                    default => '#475569',
                                                };
                                            @endphp
                                            <span
                                                style="background: {{ $statusColor }}; color: {{ $statusTextColor }}; padding: 0.35rem 0.75rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);">
                                                {{ ucfirst($nota->status) }}
                                            </span>
                                        </td>
                                        <td style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; text-align: right;">
                                            <a href="{{ route('nota.show', $nota) }}"
                                                style="display: inline-flex; justify-content: center; align-items: center; width: 2rem; height: 2rem; border-radius: 0.5rem; color: #64748b; text-decoration: none; background: #f8fafc; transition: all 0.2s;"
                                                onmouseover="this.style.background='#10b981'; this.style.color='white';"
                                                onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b';"
                                                title="Lihat Detail">
                                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div
                        style="text-align: center; padding: 4rem 2rem; border: 2px dashed #e2e8f0; border-radius: 0.75rem; background: #f8fafc; margin-top: 1rem;">
                        <p style="margin-bottom: 1rem; font-size: 3rem; filter: grayscale(1); opacity: 0.5;">📭</p>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Belum ada nota
                        </h3>
                        <p style="color: #64748b; margin-bottom: 1.5rem;">Coba buat nota pertama Anda untuk mulai mengelola.</p>
                        <a href="{{ route('nota.create') }}"
                            style="display: inline-flex; align-items: center; gap: 0.5rem; background: white; color: #10b981; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; border: 1px solid #10b981; transition: all 0.2s;"
                            onmouseover="this.style.background='#10b981'; this.style.color='white';"
                            onmouseout="this.style.background='white'; this.style.color='#10b981';">
                            + Buat Nota
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
