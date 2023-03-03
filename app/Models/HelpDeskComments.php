<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpDeskComments extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'help_desk_id',
        'user_id',
        'user_type',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
