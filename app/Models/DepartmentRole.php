<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentRole extends Model
{
    use HasFactory;

    protected $fillable = [
    	'department_id',
    	'role_id',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
