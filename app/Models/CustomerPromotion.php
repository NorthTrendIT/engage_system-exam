<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'promotion_id',
        'customer_bp_address_id',
        'total_quantity',
        'total_price',
        'total_discount',
        'total_amount',
        'status',
        'cancel_reason',
        'last_data',
        'created_at',
        'updated_at',
        'cancel_reason',
        'is_sap_pushed',
        'updated_by',
        'doc_entry',
        'sap_connection_id',
        'sales_specialist_id',
        'is_approved',
        'customer_user_id',
        'is_admin_read',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotions::class,'promotion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function products()
    {
        return $this->hasMany(CustomerPromotionProduct::class, 'customer_promotion_id', 'id');
    }

    public function customer_bp_address()
    {
        return $this->belongsTo(CustomerBpAddress::class,'customer_bp_address_id');
    }

    public function sales_specialist()
    {
        return $this->belongsTo(User::class,'sales_specialist_id');
    }

    public function customer_user()
    {
        return $this->belongsTo(User::class,'customer_user_id');
    }

    public function sap_connection(){
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }
}
