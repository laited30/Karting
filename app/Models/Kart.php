<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kart extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'model',
        'power',
        'is_active',
        'notes',
    ];
}
