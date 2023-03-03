<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTermsTypes extends Model
{
    use HasFactory;

    protected $table = 'payment_terms_type';

    protected $fillable = [
            'group_number',
            'number_of_additional_days',
        ];

	public function payGroupCode()
    {
        return $this->belongsTo(Customer::class,'payment_group_code','group_number');
    }
}
