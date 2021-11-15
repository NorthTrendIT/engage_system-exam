<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    	'card_code',
    	'card_type',
    	'card_name',
    	'group_code',
    	'contact_person',
    	'email',
    	'city',
    	'created_date',
    	'is_active',
        'response',
    ];
}
