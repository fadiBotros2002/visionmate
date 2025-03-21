<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';
    protected $fillable = ['volunteer_id', 'message', 'is_read'];


    public function users()
    {
        return $this->belongsTo(User::class, 'volunteer_id', 'user_id');
    }
}
