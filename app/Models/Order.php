<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
        'base_entry',
        'doc_entry',
        'num_at_card',
        'doc_num',
        'doc_type',
        'doc_date',
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
        'u_sostat',
        'cancelled',
        'cancel_date',
        'created_at',
        'response',
        'created_at',
        'updated_at',
        'updated_date',
        'sap_connection_id',
        'real_sap_connection_id',
        'document_status',
        'last_sync_at',
        'u_omsno',
    ];

    public function invoice(){
        return $this->hasOne(Invoice::class, ['base_entry', 'sap_connection_id'], ['doc_entry', 'sap_connection_id']);
    }

    public function quotation(){
        return $this->hasOne(Quotation::class, ['doc_entry', 'sap_connection_id'], ['base_entry', 'sap_connection_id']);
    }

    public function customer(){
        return $this->hasOne(Customer::class, ['card_code', 'sap_connection_id'], ['card_code', 'sap_connection_id']);
    }

    public function sales_specialist(){
        return $this->belongsTo(User::class, ['sales_person_code','real_sap_connection_id'], ['sales_employee_code', 'sap_connection_id']);
    }
}
