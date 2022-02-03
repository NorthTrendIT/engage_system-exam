<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyClaimPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'parent_id',
        'created_at',
        'updated_at',
    ];
}
