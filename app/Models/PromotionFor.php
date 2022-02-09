<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\Customer;

class PromotionFor extends Model
{
    use HasFactory;

    protected $table = 'promotion_for';

    protected $fillable = [
        'promotion_id',
        'area_id',
        'class_id',
        'customer_id',
        'product_id',
        'territory_id',
        'sales_specialist_id',
        'brand_id',
        'market_sector',
    ];

    public function promotion(){
        return $this->belongsTo(Promotions::class);
    }

    public function customer(){
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function territory(){
        return $this->hasOne(Territory::class, 'id', 'territory_id');
    }

    public function class(){
        return $this->hasOne(Classes::class, 'id', 'class_id');
    }

    public function sales_specialist(){
        return $this->hasOne(User::class, 'id', 'sales_specialist_id');
    }

    public function brand(){
        return $this->belongsTo(ProductGroup::class,'brand_id');
    }
}
