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
        'id',
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
        'real_sap_connection_id',
        'u_mkt_segment',
        'u_cust_segment',
        'u_sector',
        'u_subsector',
        'u_province',
        'u_card_code',
        'u_classification',
        'updated_date',
        'last_sync_at',
        'open_orders_balance',
        'frozen',
        'frozen_from',
        'frozen_to',
        'payment_group_code',
    ];

    public function bp_addresses()
    {
        return $this->hasMany(CustomerBpAddress::class,'customer_id');
    }

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, ['group_code', 'real_sap_connection_id'], ['code', 'real_sap_connection_id']);
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
        // return $this->belongsTo(User::class,'id','customer_id');
        return $this->belongsTo(User::class,'u_card_code','u_card_code');
    }

    public function sap_connection()
    {
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }


    public function credit_memo_reports(){
        return $this->hasMany(CreditNote::class, ['card_code', 'sap_connection_id'], ['card_code', 'sap_connection_id'])->where('doc_type', 'dDocument_Service')->where('document_status', 'bost_Open')->where('doc_total', '>', 0);
    }


    public function debit_memo_reports(){
        return $this->hasMany(CreditNote::class, ['card_code', 'sap_connection_id'], ['card_code', 'sap_connection_id'])->where('doc_type', 'dDocument_Service')->where('document_status', 'bost_Open')->where('doc_total', '<', 0);
    }

    
    public function u_sector_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_sector', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'sector');
            });
    }


    /*public function u_mkt_segment_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_mkt_segment', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'sector');
            });
    }*/


    public function u_cust_segment_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_cust_segment', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'segment');
            });
    }


    public function u_subsector_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_subsector', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'subsector');
            });
    }

    public function u_province_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_province', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'province');
            });
    }

    public function u_classification_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_classification', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'classification');
            });
    }

    public function payTerm()
    {
        return $this->hasOne(PaymentTermsTypes::class,'group_number','payment_group_code');
    }

    public function customerOrder(){
        return $this->belongsTo(Quotation::class, ['card_code','sap_connection_id'], ['card_code', 'sap_connection_id']);
    }

    public function vatgroup(){
        return $this->hasOne(VatGroup::class, 'code', 'vat_group');
    }
}
