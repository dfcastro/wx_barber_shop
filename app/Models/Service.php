<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'duration_minutes',
    ];

    // Futuramente, podemos adicionar casts aqui também, se necessário
    // protected $casts = [
    // 'price' => 'decimal:2',
    // ];
}