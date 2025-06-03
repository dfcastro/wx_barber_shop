<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Se você não estiver usando, pode manter comentado
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;

// Se você está usando MustVerifyEmail em algum lugar, descomente a interface e use a trait.
// class User extends Authenticatable implements MustVerifyEmail
class User extends Authenticatable implements CanResetPassword // Adicionar CanResetPassword
{
    use HasFactory, Notifiable; 
    use Notifiable, \Illuminate\Auth\Passwords\CanResetPassword; // Adicione , \Illuminate\Auth\MustVerifyEmail; se implementar MustVerifyEmail

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'is_admin',
        'provider_name',
        'provider_id',
        'provider_avatar',
        'is_active', // << ADICIONADO
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array // Sintaxe para Laravel 10+
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean', // << ADICIONADO
        ];
    }

    // Relação com Agendamentos (se ainda não existir, adicione)
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}