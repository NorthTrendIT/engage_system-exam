<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesPerson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    	'sales_employee_code',
        'sales_employee_name',
        'remark',
        'commission_for_sales_employee',
        'commission_group',
        'locked',
        'employee_id',
        'is_active',
        'u_manager',
        'u_position',
        'u_initials',
        'u_warehouse',
        'u_password',
        'u_area'
    ];
}
