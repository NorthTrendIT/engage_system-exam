<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionTypeProduct extends Model
{
    use HasFactory;

    protected $fillable = [
		'promotion_type_id',
		'product_id',
        'brand_id',
        'fixed_quantity',
		'discount_percentage',
	];

	public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function promotion_type()
    {
        return $this->belongsTo(PromotionTypes::class);
    }

    public function brand()
    {
        return $this->belongsTo(ProductGroup::class,'brand_id');
    }
}
