<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use HasFactory;

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
        'comments',
        'created_at',
        'updated_at',
        'updated_date',
        'sap_connection_id',
        'real_sap_connection_id',
        'document_status',
        'last_sync_at',
        'u_class',
    ];


    public function items(){
        return $this->hasMany(CreditNoteItem::class, 'credit_note_id', 'id');
    }

    public function customer(){
        return $this->hasOne(Customer::class, ['card_code', 'sap_connection_id'], ['card_code', 'sap_connection_id']);
    }

    public function sales_specialist(){
        return $this->belongsTo(User::class, ['sales_person_code','sap_connection_id'], ['sales_employee_code', 'sap_connection_id']);
    }

    public function invoice(){
        return $this->hasOne(Invoice::class, ['doc_entry', 'sap_connection_id'], ['base_entry', 'sap_connection_id']);
    }

    public function sap_connection(){
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }

}
