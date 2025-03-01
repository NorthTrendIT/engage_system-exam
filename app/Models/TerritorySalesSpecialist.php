<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerritorySalesSpecialist extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
    	'assignment_id',
    	'territory_id',
    	'user_id',
    	'sap_connection_id',
    ];

    public function territory()
    {
        return $this->belongsTo(Territory::class,'territory_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function salesAssignment()
    {
        return $this->belongsTo(salesAssignment::class, 'assignment_id', 'id');
    }

    public function sap_connection()
    {
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }

}
