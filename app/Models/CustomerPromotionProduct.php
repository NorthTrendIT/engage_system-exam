<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPromotionProduct extends Model
{
    use HasFactory;

    protected $fillable = [
    	'id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function deliveries()
    {
        return $this->hasMany(CustomerPromotionProductDelivery::class, 'customer_promotion_product_id', 'id');
    }
}
