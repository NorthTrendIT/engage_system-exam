<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    	'name',
    	'is_active',
        'user_id',
        
    ];

    public function roles()
    {
        return $this->hasMany(DepartmentRole::class,'department_id');
    }
}
