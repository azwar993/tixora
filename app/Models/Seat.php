<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'section_id',
        'seat_code',
        'section',
        'row',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function section()
    {
        return $this->belongsTo(EventSection::class, 'section_id');
    }

    public function ticketInstance()
    {
        return $this->hasOne(TicketInstance::class);
    }

    public function orderSeats()
    {
        return $this->hasMany(OrderSeat::class);
    }
}
