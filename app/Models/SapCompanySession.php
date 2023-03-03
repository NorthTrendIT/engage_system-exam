<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SapCompanySession extends Model
{
    use HasFactory;

    protected $fillable = [
    	'company_name',
		'username',
		'session_id',
		'expires_at',
    ];
}
