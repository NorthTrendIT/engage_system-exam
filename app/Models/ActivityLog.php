<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
    	'user_id',
    	'ip_address',
        'data',
    ];

    public function activity(){
        return $this->hasOne(ActivityMaster::class, 'id', 'activity_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
