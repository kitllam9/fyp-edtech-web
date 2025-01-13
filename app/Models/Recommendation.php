<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    /** @use HasFactory<\Database\Factories\RecommendationFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'score',
        'user_id'
    ];
}
