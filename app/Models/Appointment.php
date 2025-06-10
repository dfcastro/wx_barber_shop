<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'appointment_time',
        'status',
        'payment_status',   // << ADICIONAR
        'payment_method',   // << ADICIONAR
    ];

    // Definindo os casts para tipos corretos
    protected $casts = [
        'appointment_time' => 'datetime',
    ];

    // Relacionamentos (vamos definir em breve)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
