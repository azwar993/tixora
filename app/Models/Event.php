<?php

namespace App\Models;

use App\Models\Ticket;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $attributes = [
        'workflow_status' => 'submitted',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'location',
        'venue',
        'seating_type',
        'event_date',
        'status',
        'approval_status',
        'workflow_status',
        'rejection_reason',
        'description',
        'image',
    ];

    protected $casts = [
        'event_date' => 'date',
        'workflow_status' => 'string',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function sections()
    {
        return $this->hasMany(EventSection::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
