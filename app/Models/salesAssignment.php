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

    public function getBrandAssignmentAttribute()
    {
        return ProductGroup::whereIn('id', $this->brand_ids)->get();
    }

    public function item()
    {
        return $this->hasMany(CustomerProductItemLine::class, 'assignment_id', 'id');
    }

    public function getlineAssignmentAttribute()
    {
        if (empty($this->line_ids)) {
            return collect();  
        }
        return ProductItemLine::whereIn('id', $this->line_ids)->get();
    }

    public function category()
    {
        return $this->hasMany(CustomerProductTiresCategory::class, 'assignment_id', 'id');
    }

    public function getCategoryAssignmentAttribute()
    {
        if (empty($this->category_ids)) {
            return collect();  
        }
        return ProductTiresCategory::whereIn('id', $this->category_ids)->get();
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
