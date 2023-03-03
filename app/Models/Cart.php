<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    protected $fillable = [
    	'customer_id',
    	'product_id',
        'qty',
        'address',
        'due_date',
    ];

    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function customer(){
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

}
