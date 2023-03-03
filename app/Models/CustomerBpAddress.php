<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBpAddress extends Model
{
    use HasFactory;

    protected $fillable = [
    	'customer_id',
        'order',
        'bp_code',
        'address',
        'street',
        'zip_code',
        'city',
        'country',
        'state',
        'federal_tax_id',
        'tax_code',
        'address_type',
        'created_date',
    ];
}
