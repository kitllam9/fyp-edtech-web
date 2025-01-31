<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'description',
        'type',
        'target',
        'multiple_percentage_amount',
        'reward',
    ];
}
