<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    use \Awobaz\Compoships\Compoships;
    
    protected $fillable = [
            'invoice_id',
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
        ];

    public function product(){
        return $this->belongsTo(Product::class, ['item_code','sap_connection_id'], ['item_code', 'sap_connection_id']);
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
