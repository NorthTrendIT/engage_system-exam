<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerritorySalesSpecialist extends Model
{
    use HasFactory;

    protected $fillable = [
    	'territory_id',
    	'user_id',
    ];

    public function territory()
    {
        return $this->belongsTo(Territory::class,'territory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
