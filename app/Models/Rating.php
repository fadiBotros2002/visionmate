<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $primaryKey = 'rating_id';
    protected $fillable = ['blind_id', 'volunteer_id', 'request_id', 'rating'];

    public function blinds()
    {
        return $this->belongsTo(User::class, 'blind_id', 'user_id');
    }

    public function volunteers()
    {
        return $this->belongsTo(User::class, 'volunteer_id', 'user_id');
    }

    public function requests()
    {
        return $this->belongsTo(BlindRequest::class, 'request_id', 'request_id');
    }
}
