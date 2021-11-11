<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
		'name',
		'all_module_access',
	];

	public function role_module_access()
    {
        return $this->hasMany(RoleModuleAccess::class,'role_id');
    }
}
