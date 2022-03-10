<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    use \Awobaz\Compoships\Compoships;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'is_active',
        'email',
        'password',
        'profile',
        'city_id',
        'province_id',
        'department_id',
        'parent_id',
        'sales_specialist_name',
        // 'territory_id',
        'customer_id',
        'first_login',
        'sap_connection_id',
        'sales_employee_code',
        'created_by',
        'password_text',
        'is_sap_user',
        'default_profile_color',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class,'parent_id');
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function province()
    {
        return $this->belongsTo(Location::class,'province_id');
    }

    public function city()
    {
        return $this->belongsTo(Location::class,'city_id');
    }

    public function sales_person()
    {
        return $this->belongsToMany(CustomersSalesSpecialist::class, 'id', 'ss_id');
    }

    public function territories()
    {
        return $this->hasMany(TerritorySalesSpecialist::class,'user_id','id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function customer_delivery_schedules()
    {
        return $this->hasMany(CustomerDeliverySchedule::class,'user_id','id');
    }

    public function sap_connection()
    {
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }

    public function sales_specialist_customers()
    {
        return $this->hasMany(CustomersSalesSpecialist::class, 'ss_id', 'id');
    }
}
