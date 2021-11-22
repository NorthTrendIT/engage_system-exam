<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'customer_id',
        'is_interested',
      ];
}
