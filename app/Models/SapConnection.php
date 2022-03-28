<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SapConnection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    	'company_name',
    	'user_name',
        'db_name',
        'password',
    ];
}
