<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class salesAssignment extends Model
{
    use HasFactory;

    protected $table = 'sales_assignment';

    protected $fillable = [
    	'assignment_name',
    ];

    public function assignment(){
        return $this->hasMany(CustomersSalesSpecialist::class, 'assignment_id', 'id');
    }

    public function brand(){
        return $this->hasMany(CustomerProductGroup::class, 'assignment_id', 'id');
    }

    public function item(){
        return $this->hasMany(CustomerProductItemLine::class, 'assignment_id', 'id');
    }

    public function category(){
        return $this->hasMany(CustomerProductTiresCategory::class, 'assignment_id', 'id');
    }
}
