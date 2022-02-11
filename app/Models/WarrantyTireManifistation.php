<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyTireManifistation extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_id',
        'tire_manifistation_id',
        'is_yes',
    ];
}
