<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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
    ];

    public function product_images()
    {
        return $this->hasMany(ProductImage::class,'product_id');
    }

}
