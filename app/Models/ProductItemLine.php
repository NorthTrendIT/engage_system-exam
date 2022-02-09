<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItemLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'u_item_line',
        'sap_connection_id',
    ];
}
