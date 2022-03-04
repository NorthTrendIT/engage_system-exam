<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPromotionProductDelivery extends Model
{
    use HasFactory;
    
    protected $fillable = [
    	'id',
    	'customer_promotion_product_id',
    	'delivery_date',
    	'delivery_quantity',
    	'last_data',
    	'created_at',
    	'updated_at',
        'doc_entry',
        'is_sap_pushed',
    ];

    public function customer_promotion_product(){
        return $this->belongsTo(CustomerPromotionProduct::class,'customer_promotion_product_id');
    }

    public function quotation(){
        return $this->belongsTo(Quotation::class, 'doc_entry','doc_entry');
    }
}
