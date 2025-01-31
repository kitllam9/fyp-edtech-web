<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

class Recommendation extends Model
{
    /** @use HasFactory<\Database\Factories\RecommendationFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'score',
        'user_id'
    ];

    protected $appends = [
        'tag_name',
    ];

    protected function tagName(): Attribute
    {
        return Attribute::make(
            get: fn() => Tag::find($this->product_id)->name,
        );
    }
}
