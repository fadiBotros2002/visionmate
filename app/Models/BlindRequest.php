<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlindRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';
    protected $fillable = [
        'blind_id',
        'volunteer_id',
        'request_time',
        'status',
        'blind_latitude',
        'blind_longitude',
        'blind_location',
        'text_request',
        'accepted_at',
        'is_rated'
    ];

    protected $table = 'requests';

    public function blinds()
    {
        return $this->belongsTo(User::class, 'blind_id', 'user_id');
    }


    public function volunteers()
    {
        return $this->belongsTo(User::class, 'volunteer_id', 'user_id');
    }

    public function ratings()
    {
        return $this->hasOne(Rating::class, 'request_id', 'request_id');
    }

    public function notifications()
{
    return $this->hasMany(Notification::class, 'request_id', 'request_id');
}

public function getComputedStatusAttribute()
{
    if ($this->status === 'pending' && $this->created_at->diffInMinutes(now()) >= 10) {
        return 'expired';
    }
    return $this->status;
}

}
