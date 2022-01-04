<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'module',
        'record_id',
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function role(){
        return $this->hasOne(Role::class, 'id', 'record_id');
    }

    public function customer(){
        return $this->hasOne(Customer::class, 'id', 'record_id');
    }

    public function customer_class(){
        return $this->hasOne(Classes::class, 'id', 'record_id');
    }

    public function sales_specialist(){
        return $this->hasOne(User::class, 'id', 'record_id');
    }

    public function territory(){
        return $this->hasOne(Territory::class, 'id', 'record_id');
    }
}
