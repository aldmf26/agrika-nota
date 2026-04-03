<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepositLog extends Model
{
    protected $fillable = [
        'nota_id',
        'divisi_id',
        'nominal',
        'status',
        'dipakai_di_nota_id',
        'dipakai_at',
    ];

    protected $casts = [
        'nominal' => 'integer',
        'dipakai_at' => 'datetime',
    ];

    public function nota(): BelongsTo
    {
        return $this->belongsTo(Nota::class);
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function dipakaiDiNota(): BelongsTo
    {
        return $this->belongsTo(Nota::class, 'dipakai_di_nota_id');
    }

    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeByDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }

    /**
     * Format nominal untuk display
     */
    public function nominalFormatted(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}
