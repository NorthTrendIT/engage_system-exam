<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SapConnectionApiField extends Model
{
    use HasFactory;

    protected $fillable = [
        'sap_connection_id',
        'real_sap_connection_id',
        'field',
        'sap_field_id',
        'sap_table_name',
    ];

    public static $fields = [
        'segment' => 'Segment',
        'subsector' => 'SubSector',
        'province' => 'Province',
        'sector' => 'Sector',
        'product-line' => 'Product Line',
        'product-type' => 'Product Type',
        'product-application' => 'Product Application',
    ];

    public function sap_connection(){
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }
}
