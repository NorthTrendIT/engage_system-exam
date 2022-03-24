<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    use \Awobaz\Compoships\Compoships;

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
        'territory',

        'sap_connection_id',
        'u_mkt_segment',
        'u_cust_segment',
        'u_sector',
        'u_subsector',
        'u_province',
        'u_card_code',
        'u_classification',
        'updated_date',
        'last_sync_at',
    ];

    public function bp_addresses()
    {
        return $this->hasMany(CustomerBpAddress::class,'customer_id');
    }

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, ['group_code', 'sap_connection_id'], ['code', 'sap_connection_id']);
    }

    public function sales_specialist(){
        return $this->hasMany(CustomersSalesSpecialist::class, 'customer_id');
    }

    public function territories()
    {
        return $this->belongsTo(Territory::class,'territory','territory_id');
    }

    public function territory()
    {
        return $this->hasOne(Territory::class,'id','territory');
    }

    public function classes(){
        return $this->hasOne(Classes::class, 'id', 'class_id');
    }

    public function product_groups(){
        return $this->hasMany(CustomerProductGroup::class, 'customer_id');
    }

    public function product_item_lines(){
        return $this->hasMany(CustomerProductItemLine::class, 'customer_id');
    }

    public function product_tires_categories(){
        return $this->hasMany(CustomerProductTiresCategory::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'id','customer_id');
    }

    public function sap_connection()
    {
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }
}
