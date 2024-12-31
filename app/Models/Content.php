<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'content';
    protected $fillable = [
        'title',
        'description',
        'type',
        'pdf_url',
        'exercise_details',
        'tags'
    ];
}
