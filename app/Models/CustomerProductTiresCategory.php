<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProductTiresCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_tires_category_id',
    ];

    public function product_tires_category()
    {
        return $this->belongsTo(ProductTiresCategory::class,'product_tires_category_id');
    }
}
