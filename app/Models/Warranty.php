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
        'phone',
        'email',
        'address',
        'dealer_name',
        'location_1',
        'telephone_1',
        'location_2',
        'telephone_2',
        'fax',
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
