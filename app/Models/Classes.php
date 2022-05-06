<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
    	'name',
    	'module',
    ];

    public function sap_value() {
        return $this->belongsTo(SapConnectionApiFieldValue::class, 'name', 'key');
    }
}
