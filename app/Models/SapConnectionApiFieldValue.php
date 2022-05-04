<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SapConnectionApiFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'sap_connection_api_field_id',
        'key',
        'value',
        'sap_connection_id',
        'real_sap_connection_id',
        'last_sync_at',
    ];

    public function sap_connection_api_field(){
        return $this->belongsTo(SapConnectionApiField::class,'sap_connection_api_field_id');
    }
}
