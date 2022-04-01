<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPromotionProductDelivery extends Model
{
    use HasFactory;
    
    use \Awobaz\Compoships\Compoships;

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
        'sap_connection_id',
        'real_sap_connection_id',
    ];

    public function customer_promotion_product(){
        return $this->belongsTo(CustomerPromotionProduct::class,'customer_promotion_product_id');
    }

    public function quotation(){
        return $this->belongsTo(Quotation::class, ['doc_entry','sap_connection_id'], ['doc_entry', 'sap_connection_id']);
    }

    public function sap_connection(){
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }
}
