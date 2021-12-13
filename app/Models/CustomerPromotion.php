<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPromotion extends Model
{
    use HasFactory;


    public function promotion()
    {
        return $this->belongsTo(Promotions::class,'promotion_id');
    }
}
