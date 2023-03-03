<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'parent_id',
        'created_at',
        'updated_at',
    ];


    public function sub_titles(){
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
