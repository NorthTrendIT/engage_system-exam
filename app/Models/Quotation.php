<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
        'base_entry',
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
        'real_sap_connection_id',
        'customer_promotion_id',
        'updated_date',
        'num_at_card',
        'cancelled',
        'cancelled_by',
        'cancel_date',
        'last_sync_at',
        'u_omsno',
        'comments',
    ];

    public function items(){
        return $this->hasMany(QuotationItem::class, 'quotation_id', 'id');
    }

    public function customer(){
        return $this->hasOne(Customer::class, ['card_code', 'real_sap_connection_id'], ['card_code', 'real_sap_connection_id']);
    }

    public function sales_specialist(){
        return $this->belongsTo(User::class, ['sales_person_code','sap_connection_id'], ['sales_employee_code', 'sap_connection_id']);
    }

    public function order(){
        return $this->hasOne(Order::class, ['u_omsno', 'sap_connection_id'], ['u_omsno', 'sap_connection_id'])->latest();
    }

    public function order1(){
        return $this->hasOne(Order::class, ['base_entry', 'sap_connection_id'], ['doc_entry', 'sap_connection_id'])->latest();
    }

    public function sap_connection(){
        return $this->belongsTo(SapConnection::class,'sap_connection_id');
    }

    public function customer_promotion(){
        return $this->belongsTo(CustomerPromotion::class,'customer_promotion_id');
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class,'doc_entry');
    }

    public function local_order(){
        return $this->hasOne(LocalOrder::class, ['doc_entry','sap_connection_id'], ['doc_entry', 'sap_connection_id']);
    }

}
