<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProductItemLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_item_line_id',
    ];

    public function product_item_line()
    {
        return $this->belongsTo(ProductItemLine::class,'product_item_line_id');
    }
}
