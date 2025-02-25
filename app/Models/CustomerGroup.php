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
        'real_sap_connection_id',
        'last_sync_at',
    ];

    public function sap_connection()
    {
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, ['group_code', 'sap_connection_id'], ['code', 'sap_connection_id']);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_user', 'customer_group_id', 'user_id');
    }
}
