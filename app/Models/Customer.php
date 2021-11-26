<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    	'card_code',
    	'card_type',
    	'card_name',
    	'group_code',
    	'contact_person',
    	'email',
    	'city',
    	'created_date',
    	'is_active',
        'response',

        'address',
        'zip_code',
        'phone1',
        'notes',
        'credit_limit',
        'max_commitment',
        'federal_tax_id',
        'current_account_balance',
        'vat_group',
        'u_regowner',
        'u_mp',
        'u_msec',
        'u_tsec',
        'u_class',
        'u_rgn',
        'class_id',
        'price_list_num',
    ];

    public function bp_addresses()
    {
        return $this->hasMany(CustomerBpAddress::class,'customer_id');
    }

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class,'group_code','code');
    }
}
