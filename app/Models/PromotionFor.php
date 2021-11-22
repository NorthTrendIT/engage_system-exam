<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\Customer;

class PromotionFor extends Model
{
    use HasFactory;

    protected $table = 'promotion_for';

    protected $fillable = [
        'promotion_id',
        'area_id',
        'class_id',
        'customer_id',
        'product_id',
      ];

    public function promotion(){
        return $this->belongsTo(Promotion::class);
    }

    public function customer(){
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
