<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_id',
        'vehicle_maker',
        'vehicle_model',
        'vehicle_mileage',
        'year',
        'license_plate',
        'lt_tire_position',
        'lt_tire_mileage',
        'lt_tire_serial_no',
        'tb_tire_position',
        'tb_tire_mileage',
        'tb_tire_serial_no',
        'reason_for_tire_return',
        'location_of_damage',
    ];
}
