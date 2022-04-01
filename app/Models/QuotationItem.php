<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
        'quotation_id',
        'line_num',
        'item_code',
        'item_description',
        'quantity',
        'ship_date',
        'price',
        'price_after_vat',
        'currency',
        'rate',
        'discount_percent',
        'werehouse_code',
        'sales_person_code',
        'gross_price',
        'gross_total',
        'gross_total_fc',
        'gross_total_sc',
        'ncm_code',
        'ship_to_code',
        'ship_to_description',
        'response',
        'sap_connection_id',
        'real_sap_connection_id',
    ];

    public function product(){
        return $this->belongsTo(Product::class, ['item_code','sap_connection_id'], ['item_code', 'sap_connection_id']);
    }

    public function product1(){
        return $this->belongsTo(Product::class, ['item_code','real_sap_connection_id'], ['item_code', 'sap_connection_id']);
    }

    public function quotation(){
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }
}
