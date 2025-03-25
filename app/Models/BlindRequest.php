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
        'blind_location',  // إضافة blind_location هنا
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
}
