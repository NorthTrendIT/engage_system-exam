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
        'product_features_id',
        'product_benefits_id',
        'product_sell_sheets_id',
        'image',
    ];

    public function product_images()
    {
        return $this->hasMany(ProductImage::class,'product_id');
    }

    public function product_features()
    {
        return $this->belongsTo(ProductFeatures::class);
    }

    public function product_benefits()
    {
        return $this->belongsTo(ProductBenefits::class);
    }

    public function product_sell_sheets()
    {
        return $this->belongsTo(ProductSellSheets::class);
    }
}
