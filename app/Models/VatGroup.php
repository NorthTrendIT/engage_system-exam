<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatGroup extends Model
{
    use HasFactory;
    protected $fillable = [
		'code'            ,
        'name'             ,
        'category'          ,
        'tax_account'       , 
        'eu'                ,
        'triangular_deal'   ,
        'acquisition_reverse' ,
        'non_deduct'        ,
        'acquisition_tax'   ,
        'goods_shipment'    ,
        'non_deduct_acc'    ,
        'deferred_tax_acc'  ,
        'correction'        ,
        'vat_correction'    ,
        'equalization_tax_account' ,
        'service_supply'   ,
        'inactive'         ,
        'tax_type_black_list',
        'report_349_code' ,
        'vat_in_revenue_account' ,
        'down_payment_tax_offset_account' ,
        'cash_discount_account'  ,
        'vat_deductible_account' ,
        'tax_region'             ,
        'vatgroups_lines'        ,
        'sap_connection_id'    
	];


    public function sap_connection(){
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }



}
