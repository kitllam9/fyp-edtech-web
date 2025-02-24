<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Content extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'content';
    protected $fillable = [
        'title',
        'description',
        'type',
        'pdf_url',
        'exercise_details',
        'tags',
        'points'
    ];

    protected $appends = [
        'recommendation_score',
    ];

    public function getRecommendationScoreAttribute()
    {
        // Assuming 'recommendation_score' is a field in your model
        return $this->attributes['recommendation_score'] ?? 0;
    }

    protected function recommendationScore(): Attribute
    {
        return Attribute::make(
            // get: fn($value) => $value,
            set: fn($value) => ['recommendation_score' => $value]
        );
    }



    protected function tagIds(): Attribute
    {
        return Attribute::make(
            get: fn() => Tag::whereIn('name',  json_decode($this->tags))->pluck('id')->toArray(),
        );
    }
}
