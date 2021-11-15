<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Customer;

class SAPCustomer
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $headers;

	protected $database;
	protected $username;
	protected $password;

    public function __construct($database, $username, $password)
    {
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
                env('SAP_API_URL').$url,
                [
                    'headers' => $this->headers,
                    'verify' => false,
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

                foreach ($data['value'] as $value) {
                    
                    $insert = array(
                                    'card_code' => @$value['CardCode'],
                                    'card_type' => @$value['CardType'],
                                    'card_name' => @$value['CardName'],
                                    'group_code' => @$value['GroupCode'],
                                    'contact_person' => @$value['ContactPerson'],
                                    'email' => @$value['EmailAddress'],
                                    'city' => @$value['City'],
                                    'created_date' => @$value['CreateDate'],
                                    'is_active' => @$value['Valid'] == "tYES" ? true : false,
                                    //'response' => json_encode($value),
                                );

                    $obj = Customer::updateOrCreate(
                                            [
                                                'card_code' => @$value['CardCode'],
                                            ],
                                            $insert
                                        );
                }

                if($data['odata.nextLink']){
                    $this->addCustomerDataInDatabase($data['odata.nextLink']);
                }
            }
        }
    }
}
