<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    use \Awobaz\Compoships\Compoships;

    protected $table = 'classes';

    protected $fillable = [
    	'name',
    	'module',
        'sap_connection_id',
        'real_sap_connection_id',
    ];

    public function sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, 'name', 'key');
    }

    public function sap_connection(){
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }

    public function name_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['name', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'classification');
            });
    }
}
