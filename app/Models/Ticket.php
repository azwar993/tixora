<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quota',
        'sold',
        'reserved',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quota' => 'integer',
        'sold' => 'integer',
        'reserved' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ticketInstances()
    {
        return $this->hasMany(TicketInstance::class);
    }

    public function sections()
    {
        return $this->hasMany(EventSection::class);
    }
}
