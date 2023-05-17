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
        'correction'        => @$value['Correction'],
        'vat_correction'    => @$value['VatCorrection'],
        'equalization_tax_account' => @$value['EqualizationTaxAccount'],
        'service_supply'    => @$value['ServiceSupply'],
        'inactive'          => @$value['Inactive'],
        'tax_type_black_list' => @$value['TaxTypeBlackList'],
        'report_349_code' => @$value['Report349Code'],
        'vat_in_revenue_account' => @$value['VATInRevenueAccount'],
        'down_payment_tax_offset_account' => @$value['DownPaymentTaxOffsetAccount'],
        'cash_discount_account'  => @$value['CashDiscountAccount'],
        'vat_deductible_account' => @$value['VATDeductibleAccount'],
        'tax_region'             => @$value['TaxRegion'],
        'vatgroups_lines'        => @$value['VatGroups_Lines'],
        'sap_connection_id'      => $this->sap_connection_id,
	];
}
