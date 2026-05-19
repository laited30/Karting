<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'kart_id',
        'start_time',
        'end_time',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kart()
    {
        return $this->belongsTo(Kart::class);
    }
}