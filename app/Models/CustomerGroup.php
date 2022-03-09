<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    use HasFactory;

    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
        'code',
    	'name',
    	'type',
        'sap_connection_id',
    ];

    public function sap_connection()
    {
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }
}
