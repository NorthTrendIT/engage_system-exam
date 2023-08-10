<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendedProductAssignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'assignment_id',
        'business_unit',
        'customer_id'
    ];

    public function recommended(){
        return $this->belongsTo(RecommendedProduct::class, 'assignment_id');
    }

    public function customer(){
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }


}
