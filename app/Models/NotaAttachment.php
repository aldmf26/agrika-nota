<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaAttachment extends Model
{
    protected $fillable = [
        'nota_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function nota(): BelongsTo
    {
        return $this->belongsTo(Nota::class);
    }

    /**
     * Format file size untuk display
     */
    public function fileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 ** 2) return round($bytes / 1024, 2) . ' KB';
        return round($bytes / (1024 ** 2), 2) . ' MB';
    }
}
