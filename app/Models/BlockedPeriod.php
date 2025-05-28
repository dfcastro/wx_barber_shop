<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_datetime',
        'end_datetime',
        'reason',
    ];

    // Casts para garantir que os campos de data/hora sejam instâncias do Carbon
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];
}