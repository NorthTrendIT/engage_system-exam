<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'is_important',
        'post_time',
        'request_payload',
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function documents(){
        return $this->hasMany(NotificationDocument::class, 'notification_id', 'id');
    }
}
