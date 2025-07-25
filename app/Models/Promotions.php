<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotions extends Model
{
  use HasFactory, SoftDeletes;
  
  protected $fillable = [
    'promotion_type_id',
    'title',
    'code',
    'description',
    'discount_percentage',
    'promotion_for',
    'promotion_scope',
    'promo_image',
    'promotion_start_date',
    'promotion_end_date',
    'is_active',
    'sap_connection_id',
    'promotion_scope_selection',
    'customer_selection',
  ];

  public function promotion_data()
  {
    return $this->hasMany(PromotionFor::class, 'promotion_id');
  }

  public function promotion_type()
  {
    return $this->belongsTo(PromotionTypes::class,'promotion_type_id');
  }

  public function promotion_interests()
  {
    return $this->hasMany(PromotionInterest::class, 'promotion_id');
  }

  public function sap_connection()
  {
    return $this->belongsTo(SapConnection::class,'sap_connection_id');
  }
}
