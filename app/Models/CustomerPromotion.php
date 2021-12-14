<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'status',
        'cancel_reason',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotions::class,'promotion_id');
    }

    public function products()
    {
        return $this->hasMany(CustomerPromotionProduct::class, 'customer_promotion_id', 'id');
    }

    public function customer_bp_address()
    {
        return $this->belongsTo(CustomerBpAddress::class,'customer_bp_address_id');
    }
}
