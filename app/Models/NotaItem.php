<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaItem extends Model
{
    protected $fillable = [
        'nota_id',
        'divisi_id',
        'nominal',
        'persentase',
    ];

    protected $casts = [
        'nominal' => 'integer',
        'persentase' => 'float',
    ];

    public function nota(): BelongsTo
    {
        return $this->belongsTo(Nota::class);
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    /**
     * Format nominal untuk display
     */
    public function nominalFormatted(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}
