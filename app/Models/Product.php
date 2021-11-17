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
    ];
}
