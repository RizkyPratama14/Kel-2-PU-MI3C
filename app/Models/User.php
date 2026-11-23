<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

<<<<<<< HEAD
=======
#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
>>>>>>> db308a1cbc261e6d38ede29640ef7550ac5bf4f0
class User extends Authenticatable
{
    use HasFactory, Notifiable;

<<<<<<< HEAD
    protected $fillable = [
        'name',
        'email',
        'no_whatsapp',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

=======
    /**
     * Cek apakah user memiliki peran admin.
     */
    public function isAdmin(): bool
    {
        return ($this->role ?? null) === 'admin';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
>>>>>>> db308a1cbc261e6d38ede29640ef7550ac5bf4f0
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isPenyewa(): bool  { return $this->role === 'penyewa'; }
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isOwner(): bool    { return $this->role === 'owner'; }
}
