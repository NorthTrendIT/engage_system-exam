<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'user_id',
        'is_interested',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotions::class,'promotion_id');
    }
}
