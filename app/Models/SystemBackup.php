<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemBackup extends Model
{
    protected $fillable = [
        'token', 'path', 'checksum', 'data_fingerprint', 'file_size',
        'nota_count', 'status', 'created_by', 'used_at', 'expires_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'nota_count' => 'integer',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'token';
    }
}
