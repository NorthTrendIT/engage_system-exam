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
        'brand_ids',
        'line_ids',
        'category_ids',
    ];

    protected $casts = [
        'brand_ids' => 'array',
        'line_ids' => 'array',
        'category_ids' => 'array',
    ];

    public function assignment()
    {
        return $this->hasMany(CustomersSalesSpecialist::class, 'assignment_id', 'id');
    }

    public function brand()
    {
        return $this->hasMany(CustomerProductGroup::class, 'assignment_id', 'id');
    }

    public function item()
    {
        return $this->hasMany(CustomerProductItemLine::class, 'assignment_id', 'id');
    }

    public function category()
    {
        return $this->hasMany(CustomerProductTiresCategory::class, 'assignment_id', 'id');
    }

    public function assignmentGroup()
    {
        return $this->hasMany(CustomersSalesSpecialist::class, 'assignment_id', 'id')
            ->selectRaw('assignment_id, COUNT(*) as count')
            ->groupBy('assignment_id');
    }

    public function assignmentTerritory()
    {
        return $this->hasMany(TerritorySalesSpecialist::class, 'assignment_id', 'id');
    }
}
