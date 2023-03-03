<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'module',
        'title',
        'message',
        'is_important',
        'post_time',
        'request_payload',
        'is_important',
        'sap_connection_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function documents(){
        return $this->hasMany(NotificationDocument::class, 'notification_id', 'id');
    }

    public function connections(){
        return $this->hasMany(NotificationConnection::class, 'notification_id', 'id');
    }

    public function sap_connection(){
        return $this->hasOne(SapConnection::class, 'id', 'sap_connection_id');
    }
}
