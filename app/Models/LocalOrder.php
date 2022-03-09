<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sales_specialist_id',
        'placed_by',
        'confirmation_status',
        'due_date',
        'card_code',
        'card_name',
        'address_id',
        'doc_entry',
        'doc_num',
        'sap_connection_id',
    ];

    public function customer(){
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function sales_specialist(){
        return $this->hasOne(User::class, 'id', 'sales_specialist_id');
    }

    public function address(){
        return $this->hasOne(CustomerBpAddress::class, 'id', 'address_id');
    }

    public function items(){
        return $this->hasMany(LocalOrderItem::class, 'local_order_id');
    }

    public function quotation(){
        return $this->hasOne(Quotation::class);
    }

}
