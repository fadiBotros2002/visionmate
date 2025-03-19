<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $primaryKey = 'certificate_id';
    protected $fillable = ['volunteer_id', 'certificate_type', 'awarded_at'];


    public function users()
    {
        return $this->belongsTo(User::class, 'volunteer_id', 'user_id');
    }
}
