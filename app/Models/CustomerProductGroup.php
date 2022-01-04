<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProductGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_group_id',
    ];

    public function product_group()
    {
        return $this->belongsTo(ProductGroup::class,'product_group_id');
    }
}
