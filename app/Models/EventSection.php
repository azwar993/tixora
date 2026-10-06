<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSection extends Model
{
    protected $fillable = [
        'event_id',
        'ticket_id',
        'code',
        'name',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class, 'section_id');
    }
}
