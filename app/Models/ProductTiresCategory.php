<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTiresCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'u_tires',
        'sap_connection_id',
    ];
}
