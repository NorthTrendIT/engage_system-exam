<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LocalOrder extends Model
{
    use HasFactory;

    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
        'customer_id',
        'sales_specialist_id',
        'placed_by',
        'confirmation_status',
        'due_date',
        'total',
        'card_code',
        'card_name',
        'address_id',
        'doc_entry',
        'doc_num',
        'sap_connection_id',
        'real_sap_connection_id',
        'approval',
        'approved_at',
        'approved_by',
        'disapproval_remarks',
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
        return $this->belongsTo(Quotation::class, ['doc_entry','sap_connection_id'], ['doc_entry', 'sap_connection_id']);
    }

    public static function getApproval(){
        $type = DB::select('SHOW COLUMNS FROM local_orders WHERE Field = ?', ['approval'])[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $values = array();
        foreach(explode(',', $matches[1]) as $value){
            $values[] = trim($value, "'");
        }
        return $values;
    }

    public function approver(){
        return $this->hasOne(User::class, 'id', 'approved_by');
    }

}
