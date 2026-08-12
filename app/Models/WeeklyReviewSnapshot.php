<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyReviewSnapshot extends Model
{
    protected $fillable = ['weekly_review_id', 'reviewed_by', 'nota_ids', 'nota_count', 'total_nominal', 'reviewed_at'];

    protected $casts = ['nota_ids' => 'array', 'reviewed_at' => 'datetime'];
}
