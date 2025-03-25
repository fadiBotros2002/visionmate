<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'username',
        'phone',
        'password',
        'role',
        'latitude',
        'longitude',
        'identity_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function requests()
    {
        return $this->hasMany(BlindRequest::class, 'blind_id', 'user_id');
    }

    public function assignedRequests()
    {
        return $this->hasMany(BlindRequest::class, 'volunteer_id', 'user_id');
    }

    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'blind_id', 'user_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'volunteer_id', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'volunteer_id', 'user_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'volunteer_id', 'user_id');
    }
}
