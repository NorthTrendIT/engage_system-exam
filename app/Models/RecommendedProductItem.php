<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendedProductItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'assignment_id',
        'product_id'
    ];

    public function recommended(){
        return $this->belongsTo(RecommendedProduct::class, 'assignment_id');
    }

    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
