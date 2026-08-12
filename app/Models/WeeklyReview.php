<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyReview extends Model
{
    protected $fillable = ['year', 'month', 'week', 'reviewed_by', 'reviewed_at', 'nota_ids', 'nota_count', 'total_nominal'];

    protected $casts = ['nota_ids' => 'array', 'reviewed_at' => 'datetime'];

    public function snapshots()
    {
        return $this->hasMany(WeeklyReviewSnapshot::class);
    }
}
