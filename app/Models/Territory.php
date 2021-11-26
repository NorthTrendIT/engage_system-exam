<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Territory extends Model
{
    use HasFactory;

    protected $fillable = [
        'territory_id',
        'parent',
        'description',
        'location_index',
        'is_active',
        'response',
    ];
}
