<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpDeskDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'help_desk_id',
        'department_id',
    ];


    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
