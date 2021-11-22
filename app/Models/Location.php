<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    	'name',
    	'parent_id',
    	'is_active',
    ];


    public function parent()
    {
        return $this->belongsTo(Location::class,'parent_id');
    }
}
