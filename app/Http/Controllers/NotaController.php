<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Divisi;
use App\Http\Requests\StoreNotaRequest;
use App\Http\Requests\ApproveNotaRequest;
use App\Http\Requests\RejectNotaRequest;
use App\Services\NotaCalculationService;
use App\Services\NotaApprovalService;
use App\Services\AttachmentService;
use Illuminate\Support\Facades\Gate;

/**
 * NotaController - Kelola nota (input, lihat, approve)
 * 
 * Prinsip:
 * - Setiap method fokus pada 1 tugas
 * - Delegasi logic kompleks ke services
 * - Selalu authorize sebelum action
 */
class NotaController extends Controller
{
    public function __construct(
        protected NotaCalculationService $calculationService,
        protected NotaApprovalService $approvalService,
        protected AttachmentService $attachmentService,
    ) {}

    /**
     * INDEX - Lihat history nota
     * 
     * Query: filter by status, divisi, tipe, bulan
     * Untuk admin: hanya nota milik sendiri
     * Untuk approver: lihat semua nota
     */
    public function index()
    {
        // Build query dengan eager load
        $query = Nota::with(['user', 'divisi', 'approver', 'items.divisi'])
            ->orderByDesc('created_at');

        // Filter by user jika admin (hanya lihat milik sendiri)
        if (auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->id());
        }

        // Filter by status (dari query string)
        if (request('status') && request('status') !== 'all') {
            $query->where('status', request('status'));
        }

        // Filter by tipe
        if (request('tipe') && request('tipe') !== 'all') {
            $query->where('tipe', request('tipe'));
        }

        // Filter by divisi
        if (request('divisi_id')) {
            $query->where('divisi_id', request('divisi_id'))
                ->orWhereHas('items', fn($q) => $q->where('divisi_id', request('divisi_id')));
        }

        // Filter by tanggal, bulan, tahun, atau custom range
        $filterType = request('filter_type', 'all');
        
        if ($filterType === 'date' && request('tanggal')) {
            $query->whereDate('tanggal_nota', request('tanggal'));
        } elseif ($filterType === 'month' && request('bulan') && request('tahun')) {
            $query->where('bulan', request('bulan'))
                  ->where('tahun', request('tahun'));
        } elseif ($filterType === 'year' && request('tahun')) {
            $query->where('tahun', request('tahun'));
        } elseif ($filterType === 'custom' && request('start_date') && request('end_date')) {
            $query->whereBetween('tanggal_nota', [request('start_date'), request('end_date')]);
        }

        // Carian free text: nomor nota, keterangan, nominal
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_nota', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhere('nominal', 'like', "%{$search}%");
            });
        }

        // Paginate dengan mempertahankan query string
        $notas = $query->paginate(15)->withQueryString();

        // Data untuk filter dropdown
        $divisis = Divisi::aktif()->get(['id', 'nama']);
        $statuses = ['draft', 'pending', 'approved', 'rejected', 'void'];
        $tipes = ['biasa', 'split', 'revenue_sharing', 'kelebihan_bayar', 'digital'];

        return view('nota.index', compact('notas', 'divisis', 'statuses', 'tipes'));
    }

    /**
     * CREATE - Form input nota baru
     */
    public function create()
    {
        Gate::authorize('create', Nota::class);

        $divisis = Divisi::aktif()->get(['id', 'nama', 'kode']);

        // Generate auto nomor nota (hanya hitung yang tidak terhapus agar bisa reuse jika yang terakhir dihapus)
        // Format default: XXXX (akan ditambah kode divisi di JS)
        $latestId = Nota::max('id') ?? 0;
        $nextNumber = str_pad($latestId + 1, 4, '0', STR_PAD_LEFT);
        $nomorNota = $nextNumber;

        return view('nota.create', compact('divisis', 'nomorNota'));
    }

    /**
     * STORE - Simpan nota ke database
     */
    public function store(StoreNotaRequest $request)
    {
        $validated = $request->validated();

        // Hitung nominal berdasarkan tipe
        $nominal = $this->calculateNominalByType($validated);

        // Create nota
        $nota = Nota::create([
            'user_id' => auth()->id(),
            'tipe' => $validated['tipe'],
            'status' => 'pending', // Auto-submit langsung ke pending (approver review)
            'tanggal_nota' => $validated['tanggal_nota'],
            'tahun' => $validated['tahun'],
            'bulan' => $validated['bulan'],
            'divisi_id' => $validated['divisi_id'],
            'nomor_nota' => $validated['nomor_nota'] ?? null,
            'keterangan' => $validated['keterangan'],
            'nominal' => $nominal,
            'base_amount' => $validated['base_amount'] ?? null,
            'persentase' => $validated['persentase'] ?? null,
            'nominal_seharusnya' => $validated['nominal_seharusnya'] ?? null,
            'nominal_dibayar' => $validated['nominal_dibayar'] ?? null,
            'selisih' => $this->calculationService->calculateOverpayment(
                $validated['nominal_seharusnya'] ?? 0,
                $validated['nominal_dibayar'] ?? 0
            ),
        ]);

        // Simpan split items jika tipe split
        if ($nota->tipe === 'split') {
            foreach ($validated['split_items'] as $item) {
                $nota->items()->create([
                    'divisi_id' => $item['divisi_id'],
                    'nominal' => $item['nominal'],
                ]);
            }
        }

        // Upload files yang attached
        if ($validated['attachments'] ?? false) {
            $this->attachmentService->uploadAttachments($nota, $validated['attachments']);
        }

        return redirect()
            ->route('nota.show', $nota)
            ->with('success', 'Nota berhasil dibuat dan dikirim ke approver untuk review. Anda masih bisa edit sampai approver memberikan keputusan. ✓');
    }

    /**
     * SHOW - Lihat detail nota
     */
    public function show(Nota $nota)
    {
        Gate::authorize('view', $nota);

        $nota->load(['user', 'divisi', 'approver', 'items.divisi', 'attachments']);
        $totalNominal = $this->approvalService->getTotalNominal($nota);
        $divisiTerlibat = $this->approvalService->getDivisiTerlibat($nota);

        return view('nota.show', compact('nota', 'totalNominal', 'divisiTerlibat'));
    }

    /**
     * SUBMIT - Submit nota dari draft menjadi pending
     * 
     * Method ini sederhana: cek validasi minimal, ubah status jadi pending
     */
    public function submit(Nota $nota)
    {
        Gate::authorize('update', $nota);

        // Validasi: nota harus draft dan ada lampiran
        if ($nota->status !== 'draft') {
            return back()->with('error', 'Hanya nota draft yang bisa disubmit');
        }

        if ($nota->attachments->isEmpty()) {
            return back()->with('error', 'Nota wajib memiliki lampiran sebelum submit');
        }

        // Update status
        $nota->update(['status' => 'pending']);

        return redirect()
            ->route('nota.show', $nota)
            ->with('success', 'Nota submitted untuk review. Menunggu approver...');
    }

    /**
     * APPROVE - Approve nota (hanya approver role)
     */
    public function approve(Nota $nota, ApproveNotaRequest $request)
    {
        Gate::authorize('approve', $nota);

        $this->approvalService->approve(
            $nota,
            auth()->id(),
            $request->input('catatan_approver')
        );

        return redirect()
            ->route('nota.show', $nota)
            ->with('success', 'Nota berhasil di-approve! ✓');
    }

    /**
     * REJECT - Reject nota dengan alasan (hanya approver role)
     */
    public function reject(Nota $nota, RejectNotaRequest $request)
    {
        Gate::authorize('reject', $nota);

        $this->approvalService->reject(
            $nota,
            auth()->id(),
            $request->input('catatan_approver')
        );

        // Send notification ke user
        $nota->user->notify(new \App\Notifications\NotaRejectedNotification($nota));

        return redirect()
            ->route('nota.show', $nota)
            ->with('warning', 'Nota di-reject. Admin perlu revisi.');
    }

    /**
     * VOID - Batalkan nota (hanya approver/super_admin)
     */
    public function void(Nota $nota)
    {
        Gate::authorize('void', $nota);

        $this->approvalService->void($nota);

        return redirect()
            ->route('nota.index')
            ->with('info', 'Nota dibatalkan.');
    }

    /**
     * DESTROY - Soft delete nota (hanya status draft)
     */
    public function destroy(Nota $nota)
    {
        Gate::authorize('delete', $nota);

        // Arsip data sebelum dihapus permanen
        \Illuminate\Support\Facades\DB::table('nota_archives')->insert([
            'original_id' => $nota->id,
            'nomor_nota' => $nota->nomor_nota,
            'user_id' => $nota->user_id,
            'divisi_id' => $nota->divisi_id,
            'tanggal_nota' => $nota->tanggal_nota,
            'nominal' => $nota->getNominalTotal(),
            'keterangan' => $nota->keterangan,
            'full_data' => json_encode($nota->load('items', 'attachments')->toArray()),
            'deleted_by' => auth()->user()->nama ?? auth()->user()->username,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Hapus permanen agar ID bisa digunakan kembali jika yang dihapus adalah yang terakhir
        $nota->forceDelete();

        return redirect()
            ->route('nota.index')
            ->with('success', 'Nota berhasil dihapus dan diarsipkan ke tabel arsip. Nomor nota terakhir sekarang bisa digunakan kembali untuk input baru.');
    }

    /**
     * PRINT - Print nota dan update status cetak
     */
    public function print(Nota $nota)
    {
        Gate::authorize('view', $nota);

        // Update status cetak hanya sekali jika belum pernah, atau boleh update terus terserah
        // Biasanya update siapa yang cetak terakhir
        $nota->update([
            'is_printed' => true,
            'printed_at' => now(),
            'printed_by' => auth()->id()
        ]);

        $nota->load(['user', 'divisi', 'approver', 'items.divisi']);
        
        return view('nota.print', compact('nota'));
    }

    /**
     * RESTORE - Restore nota yang sudah dihapus (jika dalam 3 bulan)
     */
    public function restore($id)
    {
        $nota = Nota::onlyTrashed()->findOrFail($id);

        Gate::authorize('delete', $nota);

        $nota->restore();

        return redirect()
            ->route('nota.index')
            ->with('success', 'Nota sudah di-restore.');
    }

    /**
     * EDIT - Form untuk edit nota (pending atau rejected)
     */
    public function edit(Nota $nota)
    {
        Gate::authorize('update', $nota);

        $divisis = Divisi::aktif()->get(['id', 'nama']);
        $nota->load(['items', 'attachments']);

        return view('nota.edit', compact('nota', 'divisis'));
    }

    /**
     * UPDATE - Update nota dari form edit
     */
    public function update(StoreNotaRequest $request, Nota $nota)
    {
        Gate::authorize('update', $nota);

        $validated = $request->validated();
        $nominal = $this->calculateNominalByType($validated);

        // Update nota
        $nota->update([
            'tipe' => $validated['tipe'],
            'tanggal_nota' => $validated['tanggal_nota'],
            'tahun' => $validated['tahun'],
            'bulan' => $validated['bulan'],
            'divisi_id' => $validated['divisi_id'],
            'nomor_nota' => $validated['nomor_nota'] ?? null,
            'keterangan' => $validated['keterangan'],
            'nominal' => $nominal,
            'base_amount' => $validated['base_amount'] ?? null,
            'persentase' => $validated['persentase'] ?? null,
            'nominal_seharusnya' => $validated['nominal_seharusnya'] ?? null,
            'nominal_dibayar' => $validated['nominal_dibayar'] ?? null,
            'selisih' => $this->calculationService->calculateOverpayment(
                $validated['nominal_seharusnya'] ?? 0,
                $validated['nominal_dibayar'] ?? 0
            ),
        ]);

        // Update split items jika tipe split
        if ($nota->tipe === 'split') {
            $nota->items()->delete();
            foreach ($validated['split_items'] as $item) {
                $nota->items()->create([
                    'divisi_id' => $item['divisi_id'],
                    'nominal' => $item['nominal'],
                ]);
            }
        }

        // Upload new files jika ada
        if ($validated['attachments'] ?? false) {
            $this->attachmentService->uploadAttachments($nota, $validated['attachments']);
        }

        // Jika rejected, auto-resubmit (status tetap pending)
        if ($nota->status === 'rejected') {
            $nota->update(['status' => 'pending']);
            return redirect()
                ->route('nota.show', $nota)
                ->with('success', 'Nota berhasil diupdate dan di-resubmit ke approver. ✓');
        }

        return redirect()
            ->route('nota.show', $nota)
            ->with('error', 'Nota hanya bisa diedit jika status Rejected.');
    }

    /**
     * HELPER: Hitung nominal berdasarkan tipe
     */
    private function calculateNominalByType(array $validated): int
    {
        $tipe = $validated['tipe'];

        return match ($tipe) {
            'split' => $this->calculationService->calculateSplitTotal($validated['split_items']),
            'revenue_sharing' => $this->calculationService->calculateRevenueSharing(
                $validated['base_amount'],
                $validated['persentase']
            ),
            'kelebihan_bayar' => $validated['nominal_dibayar'],
            default => $validated['nominal'] ?? 0, // biasa, digital
        };
    }
}
