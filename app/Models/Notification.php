<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';
    protected $fillable = ['volunteer_id','request_id', 'message','type' ,'is_read'];


    public function users()
    {
        return $this->belongsTo(User::class, 'volunteer_id', 'user_id');
    }

    public function blindRequest()
    {
        return $this->belongsTo(BlindRequest::class, 'request_id', 'request_id');
    }
}
