<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromotionTypes extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
		'title',
		'description',
		'scope',
		'percentage',
		'min_percentage',
		'max_percentage',
		'fixed_price',
		'fixed_quantity',
		'number_of_delivery',
		'is_active',
		'is_fixed_quantity',
		'is_total_fixed_quantity',
		'total_fixed_quantity',
		'sap_connection_id',
	];

	public function products()
    {
        return $this->hasMany(PromotionTypeProduct::class,'promotion_type_id','id');
    }

    public function sap_connection(){
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }
}
