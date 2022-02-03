<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Customer;
use App\Models\SapConnection;
use App\Models\CustomerBpAddress;
use App\Jobs\StoreCustomers;
use App\Jobs\SyncNextCustomers;

class SAPCustomer
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $headers;

	protected $database;
	protected $username;
    protected $password;
	protected $log_id;

    public function __construct($database, $username, $password, $log_id = false)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->log_id = $log_id;
        
        $this->headers = $this->cookie = array();
        $this->authentication = new SAPAuthentication($database, $username, $password);
        $this->headers['Cookie'] = $this->authentication->getSessionCookie();

        $this->httpClient = new Client();
    }

    // Get customer data
    public function getCustomerData($url = '/b1s/v1/BusinessPartners')
    {
    	try {
            $response = $this->httpClient->request(
                'GET',
                get_sap_api_url().$url,
                [
                    'headers' => $this->headers,
                    'verify' => false,
                    // 'query' => [
                    //     '$filter' => "CardType eq 'cCustomer'",
                    // ],
                ]
            );

            if(in_array($response->getStatusCode(), [200,201])){
                $response = json_decode($response->getBody(),true);

                return array(
                                'status' => true,
                                'data' => $response
                            );
            }
            
        } catch (\Exception $e) {

            add_sap_log([
                            'status' => "error",
                            'error_data' => $e->getMessage(),
                        ], $this->log_id);

            return array(
                                'status' => false,
                                'data' => []
                            );
        }
    }

    // Store All Customer Records In DB
    public function addCustomerDataInDatabase($url = false)
    {
        if($url){
            $response = $this->getCustomerData($url);
        }else{
            $response = $this->getCustomerData();
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                /*foreach ($data['value'] as $value) {
                    
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
                                    'max_commitment' => @$value['MaxCommitment'],
                                    'federal_tax_id' => @$value['FederalTaxID'],
                                    'current_account_balance' => @$value['CurrentAccountBalance'],
                                    'vat_group' => @$value['VatGroup'],
                                    'u_regowner' => @$value['U_REGOWNER'],
                                    'u_mp' => @$value['U_MP'],
                                    'u_msec' => @$value['U_MSEC'],
                                    'u_tsec' => @$value['U_TSEC'],
                                    'u_class' => @$value['U_CLASS'],
                                    'u_rgn' => @$value['U_RGN'],
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
                }*/


                $where = array(
                            'db_name' => $this->database,
                            'user_name' => $this->username,
                        );

                $sap_connection = SapConnection::where($where)->first();

                // Store Data of Customer in database
                StoreCustomers::dispatch($data['value'],@$sap_connection->id);

                if(isset($data['odata.nextLink'])){

                    SyncNextCustomers::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);
                    //$this->addCustomerDataInDatabase($data['odata.nextLink']);
                }else{
                    add_sap_log([
                            'status' => "completed",
                        ], $this->log_id);
                }
            }
        }
    }
}
