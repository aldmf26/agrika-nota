<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaIssue extends Model
{
    protected $fillable = ['nota_id', 'reported_by', 'note', 'reported_at', 'replacement_nota_id', 'resolved_by', 'resolved_at'];

    protected $casts = ['reported_at' => 'datetime', 'resolved_at' => 'datetime'];

    public function nota()
    {
        return $this->belongsTo(Nota::class);
    }

    public function replacement()
    {
        return $this->belongsTo(Nota::class, 'replacement_nota_id');
    }
}
