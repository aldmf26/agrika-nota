<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Nota extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'divisi_id',
        'approver_id',
        'tipe',
        'status',
        'nomor_nota',
        'keterangan',
        'tanggal_nota',
        'tahun',
        'bulan',
        'nominal',
        'base_amount',
        'persentase',
        'nominal_seharusnya',
        'nominal_dibayar',
        'selisih',
        'catatan_approver',
        'approved_at',
        'rejected_at',
        'is_printed',
        'printed_at',
        'printed_by',
    ];

    protected $casts = [
        'tanggal_nota' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'deleted_at' => 'datetime',
        'nominal' => 'integer',
        'base_amount' => 'integer',
        'persentase' => 'float',
        'nominal_seharusnya' => 'integer',
        'nominal_dibayar' => 'integer',
        'selisih' => 'integer',
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
    ];

    /**
     * RELATIONSHIPS
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(NotaItem::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(NotaAttachment::class);
    }

    public function depositLog(): HasMany
    {
        return $this->hasMany(DepositLog::class);
    }

    /**
     * SCOPES
     */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId)->orWhereHas('items', function ($q) use ($divisiId) {
            $q->where('divisi_id', $divisiId);
        });
    }

    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopeByMonth($query, $tahun, $bulan)
    {
        return $query->where('tahun', $tahun)->where('bulan', $bulan);
    }

    /**
     * METHODS & HELPERS
     */

    /**
     * Hitung total nominal (termasuk split items jika ada)
     */
    public function getNominalTotal(): int
    {
        if ($this->tipe === 'split') {
            return $this->items->sum('nominal');
        }
        return $this->nominal;
    }

    /**
     * Approval actions
     */
    public function approve(int $approverId, ?string $catatan = null): void
    {
        $this->update([
            'status' => 'approved',
            'approver_id' => $approverId,
            'approved_at' => now(),
            'catatan_approver' => $catatan,
        ]);

        // Jika tipe kelebihan_bayar, buat deposit log
        if ($this->tipe === 'kelebihan_bayar' && $this->selisih > 0) {
            DepositLog::create([
                'nota_id' => $this->id,
                'divisi_id' => $this->divisi_id,
                'nominal' => $this->selisih,
                'status' => 'tersedia',
            ]);
        }
    }

    public function reject(int $approverId, string $catatan): void
    {
        $this->update([
            'status' => 'rejected',
            'approver_id' => $approverId,
            'rejected_at' => now(),
            'catatan_approver' => $catatan,
        ]);
    }

    public function void(): void
    {
        $this->update(['status' => 'void']);
    }

    /**
     * Daftar divisi yang terlibat (termasuk split items)
     */
    public function getDivisiTerlibat()
    {
        $divisiIds = collect([$this->divisi_id])->filter();

        if ($this->tipe === 'split') {
            $divisiIds = $divisiIds->merge($this->items->pluck('divisi_id'));
        }

        return Divisi::whereIn('id', $divisiIds->unique())->get();
    }

    /**
     * Format nominal untuk display
     */
    public function nominalFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Rp ' . number_format($this->getNominalTotal(), 0, ',', '.')
        );
    }

    /**
     * Status badge color
     */
    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'void' => 'slate',
        };
    }
}
