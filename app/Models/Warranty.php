<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warranty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ref_no',
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

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function vehicle(){
        return $this->belongsTo(WarrantyVehicle::class,'id','warranty_id');
    }

    public function claim_points(){
        return $this->hasMany(WarrantyClaimPoint::class,'warranty_id');
    }

    public function tire_manifistations(){
        return $this->hasMany(WarrantyTireManifistation::class,'warranty_id');
    }

    public function pictures(){
        return $this->hasMany(WarrantyPicture::class,'warranty_id');
    }
}
