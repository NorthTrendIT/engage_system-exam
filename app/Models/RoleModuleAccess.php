<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleModuleAccess extends Model
{
    use HasFactory;

    protected $table = 'role_module_access';

    protected $fillable = [
        'role_id',
        'module_id',
        'read_access',
        'write_access',
    ];
}
