<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItemLine extends Model
{
    use HasFactory;

    use \Awobaz\Compoships\Compoships;
    
    protected $fillable = [
        'u_item_line',
        'sap_connection_id',
    ];

    public function u_item_line_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_item_line', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'product-line');
            });
    }
}
