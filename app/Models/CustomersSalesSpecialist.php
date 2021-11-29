<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomersSalesSpecialist extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'ss_id',
    ];

    public function promotion(){
        return $this->belongsTo(Promotion::class);
    }

    public function customer(){
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function sales_person(){
        return $this->hasOne(User::class, 'id', 'ss_id');
    }
}
