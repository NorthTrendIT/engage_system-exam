<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyClaimPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_id',
        'claim_point_id',
        'is_yes',
    ];
}
