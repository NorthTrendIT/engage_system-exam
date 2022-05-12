<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
    	'item_code',
        'item_name',
    	'foreign_name',
    	'items_group_code',
    	'customs_group_code',
    	'sales_vat_group',
    	'purchase_vat_group',
    	'created_date',
    	'is_active',
    	'response',
        'technical_specifications',
        'product_features',
        'product_benefits',
        'product_sell_sheets',
    	'item_prices',

        'sap_connection_id',
        'u_mobil_sc',
        'u_item_type',
        'u_item_application',
        'u_pattern_type',
        'u_tire_size',
        'u_tire_diameter',
        'u_speed_symbol',
        'u_ply_rating',
        'u_tire_const',
        'u_fitment_conf',
        'u_business_group',
        'u_section_width',
        'u_series',
        'u_item_line',
        'u_tires',
        'item_class',
        'u_series2',
        'u_pattern2',
        'updated_date',

        'u_loadindex',
        'u_blength',
        'u_bwidth',
        'u_bheight',
        'u_bthicknes',
        'u_brsvdcapacity',
        'u_bcoldcrankamps',
        'u_bamperhour',
        'u_bhandle',
        'u_bpolarity',
        'u_bterminal',
        'u_bholddown',
        'u_bleadweight',
        'u_btotalweight',
        'u_product_tech',
        'sales_unit',
        'last_sync_at',
    ];

    public function product_images()
    {
        return $this->hasMany(ProductImage::class,'product_id');
    }

    public function product_item_line()
    {
        return $this->belongsTo(ProductItemLine::class, ['u_item_line','sap_connection_id'], ['u_item_line', 'sap_connection_id']);
    }

    public function product_tires_category()
    {
        return $this->belongsTo(ProductTiresCategory::class, ['u_tires','sap_connection_id'], ['u_tires', 'sap_connection_id']);
    }

    public function group()
    {
        return $this->belongsTo(ProductGroup::class, ['items_group_code','sap_connection_id'], ['number', 'sap_connection_id']);
    }

    public function sap_connection(){
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }


    public function u_item_line_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_item_line', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'product-line');
            });
    }


    public function u_item_type_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_item_type', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'product-type');
            });
    }

    public function u_item_application_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_item_application', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'product-application');
            });
    }

    public function u_pattern2_sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, ['u_pattern2', 'sap_connection_id'], ['key', 'sap_connection_id'])->whereHas('sap_connection_api_field', function($q) {
                $q->where('field', 'product-pattern');
            });
    }
}
