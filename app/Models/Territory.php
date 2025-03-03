<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Territory extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
        'territory_id',
        'parent',
        'description',
        'location_index',
        'is_active',
        'response',
        'sap_connection_id',
        'last_sync_at',
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'territory', 'territory_id');
    }

    // public function users()
    // {
    //     return $this->belongsToMany(User::class);
    // }

    public function users()
    {
        return $this->belongsToMany(User::class, 'territory_sales_specialists', 'territory_id', 'user_id')
                    ->withPivot('assignment_id', 'sap_connection_id') // Access additional data
                    ->withTimestamps(); 
    }

}
