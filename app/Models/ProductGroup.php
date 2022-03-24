<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGroup extends Model
{
    use HasFactory;

    use \Awobaz\Compoships\Compoships;
    
    protected $fillable = [
        'number',
        'group_name',
        'sap_connection_id',
        'last_sync_at',
    ];

    public function sap_connection()
    {
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }
}
