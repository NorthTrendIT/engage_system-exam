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
        'type_of_customer_request',
        'other_type_of_customer_request_name',
        'updated_by',
        'closed_reason',
        'closed_image',
    ];

    public static $type_of_customer_requests = [
        'Marketing Promo & Collaterals',
        'Sales Order',
        'Sales Invoice',
        'Pricing',
        'Rebates',
        'Delivery',
        'Payments',
        'Warranty Claim',
        'Meeting Request',
        'Training Request',
        'Stock Availability',
        'Other Matters',
    ];

    public function urgency()
    {
        return $this->belongsTo(HelpDeskUrgencies::class, 'help_desk_urgency_id');
    }
    
    public function status()
    {
        return $this->belongsTo(HelpDeskStatuses::class, 'help_desk_status_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files()
    {
        return $this->hasMany(HelpDeskFiles::class, 'help_desk_id', 'id');
    }

    public function comments()
    {
        return $this->belongsToMany(HelpDeskComments::class, 'id', 'help_desk_id');
    }

    public function departments()
    {
        return $this->hasMany(HelpDeskDepartment::class, 'help_desk_id', 'id');
    }
}
