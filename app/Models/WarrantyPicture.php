<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyPicture extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_id',
        'type',
        'title',
        'image',
    ];
}
