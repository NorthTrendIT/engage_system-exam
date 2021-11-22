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
		'parent_id',
		'all_module_access',
	];

	public function role_module_access()
    {
        return $this->hasMany(RoleModuleAccess::class,'role_id');
    }

    public function parent()
    {
        return $this->belongsTo(Role::class,'parent_id');
    }
}
