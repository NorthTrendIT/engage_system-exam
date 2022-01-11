<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_entry',
        'doc_num',
        'doc_type',
        'document_status',
        'doc_date',
        'doc_time',
        'doc_due_date',
        'card_code',
        'card_name',
        'address',
        'doc_total',
        'doc_currency',
        'journal_memo',
        'payment_group_code',
        'sales_person_code',
        'u_brand',
        'u_branch',
        'u_commitment',
        'u_time',
        'u_posono',
        'u_posodate',
        'u_posotime',
        'u_remarks',
        'response',
        'created_at',
        'updated_at',
        'sap_connection_id',
        'customer_promotion_id',
    ];

    public function items(){
        return $this->hasMany(QuotationItem::class, 'quotation_id', 'id');
    }

    public function customer(){
        return $this->hasOne(Customer::class, 'card_code', 'card_code');
    }

    public function sales_specialist(){
        return $this->belongsTo(User::class, 'sales_person_code','sales_employee_code');
    }

    public function order(){
        return $this->hasOne(Order::class, 'doc_num', 'doc_entry');
    }

    public function invoice(){
        return $this->hasOne(Invoice::class, 'doc_num', 'doc_entry');
    }
}
