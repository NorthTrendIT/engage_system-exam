<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Customer;
use App\Models\Classes;
use App\Models\CustomerBpAddress;

class StoreCustomers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!empty($this->data)){

            foreach ($this->data as $value) {

                if(!is_null(@$value['U_CLASS'])){
                    $obj_class = Classes::updateOrCreate(
                                            [
                                                'name' => @$value['U_CLASS'],
                                                'module' => 'C',
                                            ],
                                            [
                                                'name' => @$value['U_CLASS'],
                                                'module' => 'C',
                                            ]
                                        );
                }

                $insert = array(
                                'card_code' => @$value['CardCode'],
                                'card_type' => @$value['CardType'],
                                'card_name' => @$value['CardName'],
                                'group_code' => @$value['GroupCode'],
                                'contact_person' => @$value['ContactPerson'],
                                'email' => @$value['EmailAddress'],
                                'city' => @$value['City'],
                                //'created_date' => @$value['CreateDate']." ".@$value['CreateTime'],
                                'is_active' => @$value['Valid'] == "tYES" ? true : false,
                                //'response' => json_encode($value),

                                'address' => @$value['Address'],
                                'zip_code' => @$value['ZipCode'],
                                'phone1' => @$value['Phone1'],
                                'notes' => @$value['Notes'],
                                'credit_limit' => @$value['CreditLimit'],
                                //'max_commitment' => @$value['MaxCommitment'],
                                'federal_tax_id' => @$value['FederalTaxID'],
                                'current_account_balance' => @$value['CurrentAccountBalance'],
                                //'vat_group' => @$value['VatGroup'],
                                'u_regowner' => @$value['U_REGOWNER'],
                                //'u_mp' => @$value['U_MP'],
                                'u_msec' => @$value['U_MSEC'],
                                'u_tsec' => @$value['U_TSEC'],
                                'u_class' => @$value['U_CLASS'],
                                'u_rgn' => @$value['U_RGN'],
                                'price_list_num' => @$value['PriceListNum'],
                                
                                'class_id' => !is_null(@$value['U_CLASS']) ? @$obj_class->id : NULL,
                            );

                $obj = Customer::updateOrCreate(
                                        [
                                            'card_code' => @$value['CardCode'],
                                        ],
                                        $insert
                                    );

                // Store BPAddresses details
                $bp_orders = []; 
                if(isset($value['BPAddresses']) && @$obj->id){

                    foreach ($value['BPAddresses'] as $bp) {
                        $insert = array(
                                'bp_code' => @$bp['BPCode'],
                                'order' => @$bp['RowNum'],
                                'customer_id' => $obj->id,
                                'address' => @$bp['AddressName'],
                                'street' => @$bp['Street'],
                                'zip_code' => @$bp['ZipCode'],
                                'city' => @$bp['City'],
                                'country' => @$bp['Country'],
                                'state' => @$bp['State'],
                                'federal_tax_id' => @$bp['FederalTaxID'],
                                'tax_code' => @$bp['TaxCode'],
                                'address_type' => @$bp['AddressType'],
                                'created_date' => @$bp['CreateDate']." ".@$bp['CreateTime'],
                            );

                        array_push($bp_orders, @$bp['RowNum']);

                        $bp_obj = CustomerBpAddress::updateOrCreate(
                                            [
                                                'order' => @$bp['RowNum'],
                                                'customer_id' => $obj->id,
                                            ],
                                            $insert
                                        );
                    }

                    if(empty($value['BPAddresses'])){
                        $removeBpAddress = CustomerBpAddress::where('customer_id',$obj->id);
                        $removeBpAddress->delete();
                    }elseif(!empty($bp_orders)){
                        $removeBpAddress = CustomerBpAddress::where('customer_id',$obj->id);
                        $removeBpAddress->whereNotIn('order',$bp_orders);
                        $removeBpAddress->delete();
                    }

                }
            }

        }
    }
}
