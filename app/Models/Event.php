<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'location',
        'venue',
        'event_date',
        'status',
        'description',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];
}