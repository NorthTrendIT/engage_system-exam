<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
        'base_entry',
        'doc_entry',
        'doc_num',
        'num_at_card',
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
        'response',
        'created_at',
        'updated_at',
        'updated_date',
        'sap_connection_id',
        'real_sap_connection_id',
        'document_status',
        'cancelled',
        'end_delivery_date',
        'u_delivery',
        'last_sync_at',
        'completed_date',
        'completed_remarks',
        'u_omsno',
        'update_date',
    ];

    public function items(){
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    public function customer(){
        return $this->hasOne(Customer::class, ['card_code', 'sap_connection_id'], ['card_code', 'sap_connection_id']);
    }

    public function sales_specialist(){
        return $this->belongsTo(User::class, ['sales_person_code','sap_connection_id'], ['sales_employee_code', 'sap_connection_id']);
    }

    public function order(){
        return $this->hasOne(Order::class, ['doc_entry', 'sap_connection_id'], [ 'base_entry', 'sap_connection_id']);
    }

    public function sap_connection(){
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }
}
