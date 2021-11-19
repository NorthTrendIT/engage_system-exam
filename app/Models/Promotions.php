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
      'description',
      'discount_percentage',
      'promotion_for',
      'promotion_scope',
      'promo_image',
      'promotion_start_date',
      'promotion_end_date',
      'is_active',
    ];
}
