<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warranty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'warranty_claim_type',
        'customer_phone',
        'customer_email',
        'customer_address',
        'dealer_name',
        'customer_location',
        'customer_telephone',
        'dealer_location',
        'dealer_telephone',
        'dealer_fax',
        'created_by',
        'updated_by',
    ];

    public static $warranty_claim_types = [
        'Workmanship & Materials',
        'Treadware',
        'Ride Vibration',
        'Road Hazard',
    ];
}
