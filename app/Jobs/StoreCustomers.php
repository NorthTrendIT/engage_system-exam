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
use App\Models\User;
use Hash;
use Log;

class StoreCustomers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;

    protected $sap_connection_id;
    protected $real_sap_connection_id;

    public function __construct($data, $sap_connection_id)
    {
        $this->data = $data;
        $this->sap_connection_id = $sap_connection_id;
        $this->real_sap_connection_id = $sap_connection_id;
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
                if(empty(@$value['U_CardCode'])){
                    continue;
                }

                if(@$value['CardType'] != "cCustomer"){
                    continue;
                }

                if(@$value['GroupCode'] == "111"){ // GROUP EMPLOYEE NO NEED TO STORE
                    continue;
                }


                if($this->real_sap_connection_id == 1){ // GROUP Cagayan, Davao NEED TO STORE in Solid Trend 
                    if(in_array(@$value['GroupCode'], [103, 105])){
                        $this->sap_connection_id = 5;
                    }else{
                        $this->sap_connection_id = 1;
                    }
                }

                if(!is_null(@$value['U_Classification']) || @$value['U_Classification'] != ""){
                    $obj_class = Classes::updateOrCreate(
                                            [
                                                'name' => @$value['U_Classification'],
                                                'module' => 'C',
                                                'sap_connection_id' => $this->sap_connection_id,
                                            ],
                                            [
                                                'name' => @$value['U_Classification'],
                                                'module' => 'C',
                                                'sap_connection_id' => $this->sap_connection_id,
                                                'real_sap_connection_id' => $this->real_sap_connection_id,
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
                                'created_date' => @$value['CreateDate']." ".@$value['CreateTime'],
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
                                'vat_group' => @$value['VatGroup'],
                                'u_regowner' => @$value['U_REGOWNER'],
                                //'u_mp' => @$value['U_MP'],
                                'u_msec' => @$value['U_MSEC'],
                                'u_tsec' => @$value['U_TSEC'],
                                'u_class' => @$value['U_CLASS'],
                                'u_rgn' => @$value['U_RGN'],
                                'price_list_num' => @$value['PriceListNum'],
                                'territory' => @$value['Territory'],

                                'class_id' => !is_null(@$value['U_Classification']) ? @$obj_class->id : NULL,
                                'u_classification' => @$value['U_Classification'],

                                'u_mkt_segment' => @$value['U_MktSegment'],
                                'u_cust_segment' => @$value['U_CustSegment'],
                                'u_sector' => @$value['U_Sector'],
                                'u_subsector' => @$value['U_Subsector'],
                                'u_province' => @$value['U_Province'],
                                'u_card_code' => @$value['U_CardCode'],
                                'open_orders_balance' => @$value['OpenOrdersBalance'],
                                'frozen' => @$value['Frozen'] == "tYES" ? 1 : 0,
                                'frozen_from' => @$value['FrozenFrom'],
                                'frozen_to' => @$value['FrozenTo'],

                                'updated_date' => @$value['UpdateDate'],
                                'last_sync_at' => current_datetime(),
                                'sap_connection_id' => $this->sap_connection_id,
                                'real_sap_connection_id' => $this->real_sap_connection_id,
                                'payment_group_code' => @$value['PayTermsGrpCode'],
                            );
                
                // Log::info(print_r($insert,true));   
                $obj = Customer::updateOrCreate(
                                        [
                                            'card_code' => @$value['CardCode'],
                                            'sap_connection_id' => $this->sap_connection_id,
                                        ],
                                        $insert
                                    );
                // Store BPAddresses details
                if(@$obj->id){

                    $bp_orders = [];
                    if(isset($value['BPAddresses'])){

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


                    // Store Customer Records in users table
                    $check_user = User::where('u_card_code',$obj->u_card_code)->first();
                    $check_customer = Customer::where('u_card_code',$obj->u_card_code)->select('id','sap_connection_id', 'real_sap_connection_id')->get()->toArray();
                    if(is_null($check_user)){

                        $name = explode(" ", $obj->card_name, 2);
                        $password = get_random_password();

                        $insert_user =  array(
                                            'department_id' => 3,
                                            'role_id' => 4,
                                            'customer_id' => $obj->id,
                                            'sales_specialist_name' => @$obj->card_name,
                                            'first_name' => !empty($name[0]) ? $name[0] : null,
                                            'last_name' => !empty($name[1]) ? $name[1] : null,
                                            'is_active' => $obj->is_active,
                                            //'password' => Hash::make(@$obj->card_code),
                                            'password' => Hash::make($password),
                                            'password_text' => $password,
                                            // 'email' => strtolower(@$obj->card_code)."-".$this->sap_connection_id.'@mailinator.com',
                                            // 'email' => strtolower(@$obj->u_card_code)."-".$this->sap_connection_id.'@mailinator.com',
                                            'email' => strtolower(@$obj->u_card_code).'@mailinator.com',
                                            'first_login' => true,
                                            'sap_connection_id' => $this->sap_connection_id,
                                            'real_sap_connection_id' => $this->real_sap_connection_id,
                                            'default_profile_color' => get_hex_color(),

                                            'u_card_code' => $obj->u_card_code,
                                            'multi_customer_id' => $obj->id,
                                            'multi_sap_connection_id' => $obj->sap_connection_id,
                                            'multi_real_sap_connection_id' => $obj->real_sap_connection_id,
                                        );

                        User::create($insert_user);
                    }else{

                        $multi_customer_id = array_column($check_customer, 'id');
                        if(!in_array($obj->id, $multi_customer_id)){
                            array_push($multi_customer_id, $obj->id);
                        }

                        $multi_sap_connection_id = array_column($check_customer, 'sap_connection_id');
                        if(!in_array($obj->sap_connection_id, $multi_sap_connection_id)){
                            array_push($multi_sap_connection_id, $obj->sap_connection_id);
                        }

                        $multi_real_sap_connection_id = array_column($check_customer, 'real_sap_connection_id');
                        if(!in_array($obj->real_sap_connection_id, $multi_real_sap_connection_id)){
                            array_push($multi_real_sap_connection_id, $obj->real_sap_connection_id);
                        }

                        // $multi_sap_connections = explode(",", $check_customer->multi_sap_connections);
                        // array_push($multi_sap_connections, $obj->id);


                        if($obj->is_active){
                            $check_user->is_active = $obj->is_active;
                        }
                        $check_user->multi_customer_id = implode(",", $multi_customer_id);
                        $check_user->multi_sap_connection_id = implode(",", $multi_sap_connection_id);
                        $check_user->multi_real_sap_connection_id = implode(",", $multi_real_sap_connection_id);
                        $check_user->save();
                    }
                }
            }

        }
    }
}
