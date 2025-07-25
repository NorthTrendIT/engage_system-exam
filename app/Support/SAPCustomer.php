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
use App\Models\PaymentTermsTypes;
use Log;
use DB;

class SAPCustomer
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $headers;
    protected $cookie;
    protected $authentication;

	protected $database;
	protected $username;
    protected $password;
	protected $log_id;
    protected $search;

    public function __construct($database, $username, $password, $log_id = false, $search)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->log_id = $log_id;
        $this->search = $search;

        $this->headers = $this->cookie = array();
        $this->authentication = new SAPAuthentication($database, $username, $password);
        $this->headers['Cookie'] = $this->authentication->getSessionCookie();
        // $this->headers['Prefer'] = "odata.maxpagesize=0 (get all data)";

        $this->httpClient = new Client();
    }

    // Get customer data
    public function getCustomerData($url = '/b1s/v1/BusinessPartners', $logFilePath = false)
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

                if($logFilePath){
                    unlink($logFilePath);
                }

                return array(
                                'status' => true,
                                'data' => $response
                            );
            }

        } catch (\Exception $e) {

            if(!empty($this->log_id)){
                add_sap_log([
                                'status' => "error",
                                'error_data' => $e->getMessage(),
                            ], $this->log_id);
            }

            return array(
                                'status' => false,
                                'data' => []
                            );
        }
    }

    // Store All Customer Records In DB
    public function addCustomerDataInDatabase($url = false, $logFilePath = false)
    {
        ini_set('memory_limit', '512M'); //set limit

        $where = array(
                    'db_name' => $this->database,
                    'user_name' => $this->username,
                );

        $sap_connection = SapConnection::where($where)->first();

        if($url){
            $response = $this->getCustomerData($url, $logFilePath);
        }else{
            if($this->search){
                $search = str_replace("'","''",$this->search);  //for single quote (') search
                $url = '/b1s/v1/BusinessPartners?$filter=contains(CardName, \''.$search.'\')&$top=20';
            }else{
                // $latestData = Customer::orderBy('updated_date','DESC')->where('sap_connection_id', $sap_connection->id)->first();
                // if(!empty($latestData)){
                    $currentDate = Carbon::now(); 
                    $todaysDate = $currentDate->toDateString();
                    $previousDate = $currentDate->subDay()->toDateString(); // -1 day  //$date->subDays(3);

                    // $url = '/b1s/v1/BusinessPartners?$filter=GroupCode eq 103 and VatGroup ne null and Valid eq \''.'tYES'.'\'';
                    // $url = '/b1s/v1/BusinessPartners?$filter=UpdateDate ge \''.$previousDate.'\' or CreateDate ge \''.$previousDate.'\'';
                    $url = '/b1s/v1/BusinessPartners?$count=true&$filter=(UpdateDate ge \''.$previousDate.'\' and UpdateDate le \''.$todaysDate.'\') or (CreateDate ge \''.$previousDate.'\' and CreateDate le \''.$todaysDate.'\')';
                
                    // Log::info(print_r($response,true));
                // } else {
                //     $url = '/b1s/v1/BusinessPartners';
                // }
            }
            $response = $this->getCustomerData($url);
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){
                // Store Data of Customer in database
                StoreCustomers::dispatch($data['value'],@$sap_connection->id);

                if(isset($data['odata.nextLink'])){

                    SyncNextCustomers::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id, $this->search);
                    $this->addCustomerDataInDatabase($data['odata.nextLink']);
                }else{
                    if(!empty($this->log_id)){
                        add_sap_log([
                                'status' => "completed",
                            ], $this->log_id);
                    }
                }
            }
        }
    }

    // Get customer data
    public function getPaymentTermTypeData($url = '/b1s/v1/PaymentTermsTypes')
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                get_sap_api_url().$url,
                [
                    'headers' => $this->headers,
                    'verify' => false,
                ]
            );

            if(in_array($response->getStatusCode(), [200,201])){
                $response = json_decode($response->getBody(),true);
                if(!empty($response['value'])){
                    $data = $response['value']; 
                    foreach($data as $val){
                        $payment = new PaymentTermsTypes();
                        $payment->group_number = @$val['GroupNumber'];
                        $payment->number_of_additional_days = @$val['NumberOfAdditionalDays'];
                        $payment->save();
                    }
                }

                return array(
                                'status' => true,
                                'data' => $response
                            );
            }

        } catch (\Exception $e) {

            if(!empty($this->log_id)){
                add_sap_log([
                                'status' => "error",
                                'error_data' => $e->getMessage(),
                            ], $this->log_id);
            }

            return array(
                                'status' => false,
                                'data' => []
                            );
        }
    }

    public function addSpecificCustomerData($card_code = false)
    {
        // Log::info(print_r($card_code,true));
        if($card_code){
            $url = "/b1s/v1/BusinessPartners('".$card_code."')";
            $response = $this->getCustomerData($url);
            //Log::info(print_r($response,true));
            // if($response['status']){
            //     $customer = $response['data'];
            //     if(!empty($customer)){
            //         $where = array(
            //                     'db_name' => $this->database,
            //                     'user_name' => $this->username,
            //                 );

            //         $sap_connection = SapConnection::where($where)->first();
            //         $sap_connection_id = $sap_connection->id;
            //         $data = [
            //             'frozen' => @$customer['Frozen'] == "tYES" ? 1 : 0,
            //             'frozen_from' => @$customer['FrozenFrom'],
            //             'frozen_to' => @$customer['FrozenTo'],
            //         ];

            //         $update = Customer::where([
            //                                     'card_code' => @$customer['CardCode'],
            //                                     'sap_connection_id' => $sap_connection_id,
            //                                 ])->update($data);
            //     }
            // }

            if($response['status']){
                $data[] = $response['data'];
                if($data){

                    StoreCustomers::dispatch($data, 5); //1.APBW, 2.NTMC, 3.PHILCREST, 4.PHILSYN [X], 5.SOLID TREND
                    if(isset($data['odata.nextLink'])){
                        //Log::info(print_r($this->database,true));
                        SyncNextCustomers::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id, $this->search);
                        $this->addCustomerDataInDatabase($data['odata.nextLink']);
                    }else{
                        if(!empty($this->log_id)){
                            add_sap_log([
                                    'status' => "completed",
                                ], $this->log_id);
                        }
                    }
                }
            }
        }
        
    }

    public function fetchCustomers(){
        $this->headers['Prefer'] = "odata.maxpagesize=0 (get all data)";
        $filter = 'PHP';
        $url = '/b1s/v1/BusinessPartners/?$filter=Currency ne \''.$filter.'\'&$select=CardCode,Currency';
        $response = $this->getCustomerData($url);

        return $response['data'];
    }

    public function fetchPriceLists(){
        $this->headers['Prefer'] = "odata.maxpagesize=0 (get all data)";
        $url = '/b1s/v1/PriceLists?$select=PriceListNo,PriceListName&$orderby=PriceListNo asc';
        $response = $this->getCustomerData($url);

        return $response['data'];
    }

    
}
