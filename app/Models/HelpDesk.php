<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpDesk extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'ticket_number',
        'user_id',
        'user_type',
        'name',
        'email',
        'department_id',
        'help_desk_urgency_id',
        'help_desk_status_id',
        'subject',
        'message',
    ];

    public function urgency()
    {
        return $this->belongsTo(HelpDeskUrgencies::class);
    }
    
    public function status()
    {
        return $this->belongsTo(HelpDeskStatuses::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->belongsToMany(HelpDeskFiles::class, 'id', 'help_desk_id');
    }

    public function comments()
    {
        return $this->belongsToMany(HelpDeskComments::class, 'id', 'help_desk_id');
    }
}
