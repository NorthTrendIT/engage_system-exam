<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
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
    ];

    public function product(){
        return $this->hasOne(Product::class, 'item_code', 'item_code');
    }
}
