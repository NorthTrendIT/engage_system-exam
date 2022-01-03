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

    public function scopeModule($query){
        return $query
                ->when($this->module === 'role',function($q){
                    return $q->with('role');
                })
                ->when($this->module === 'customer',function($q){
                    return $q->with('customer');
                })
                ->when($this->module === 'customer_class',function($q){
                    return $q->with('customer_class');
                })
                ->when($this->module === 'sales_specialist',function($q){
                    return $q->with('sales_specialist');
                })
                ->when($this->module === 'territory',function($q){
                    return $q->with('territory');
                });
    }
}
