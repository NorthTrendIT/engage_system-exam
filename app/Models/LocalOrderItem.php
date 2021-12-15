<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'local_order_id',
        'product_id',
        'item_code',
        'quantity',
    ];

    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
