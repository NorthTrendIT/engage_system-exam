<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyDiagnosticReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_id',
        'result',
        'tire_manifistations',
        'tire_size',
        'tire_size_selling_price',
        'remaining_tread_depth',
        'warranty_claim_adjustment',
        'payment_for_the_new_tire_replacement',
    ];

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }
}
