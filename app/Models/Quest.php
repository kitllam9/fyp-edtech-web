<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Quest extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
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
