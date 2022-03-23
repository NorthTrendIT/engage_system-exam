<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use HasFactory;

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
        'document_status',
    ];

}
