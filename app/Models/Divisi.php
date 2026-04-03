<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Divisi extends Model
{
    protected $table = 'divisis';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Accessors
     */
    public function getKodeAttribute($value)
    {
        return strtoupper($value);
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }

    public function notaItems(): HasMany
    {
        return $this->hasMany(NotaItem::class);
    }

    public function depositLogs(): HasMany
    {
        return $this->hasMany(DepositLog::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
