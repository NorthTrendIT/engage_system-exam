<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTarget extends Model
{
    use HasFactory;

    
    public function product_group()
    {
        return $this->belongsTo(ProductGroup::class,'brand_id');
    }

    public function product_category()
    {
        return $this->belongsTo(ProductTiresCategory::class,'category_id');
    }

}
