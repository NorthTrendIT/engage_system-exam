<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\User;
use Hash;

class SAPSalesPersons
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

    // Get Sales Persons data
    public function getSalesPersonsData($url = '/b1s/v1/SalesPersons')
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
    public function addSalesPersonsDataInDatabase($url = false)
    {
        if($url){
            $response = $this->getSalesPersonsData($url);
        }else{
            $response = $this->getSalesPersonsData();
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                foreach ($data['value'] as $value) {
                    $email = ''.$value['U_Password'];
                    $name = explode(" ", $value['SalesEmployeeName'], 2);

                    $insert = array(
                                    'role_id' => 2,
                                    'sales_specialist_name' => $value['SalesEmployeeName'],
                                    'first_name' => !empty($name[0]) ? $name[0] : null,
                                    'last_name' => !empty($name[1]) ? $name[1] : null,
                                    'is_active' => $value['Active'] == "tYES" ? true : false,
                                    'password' => Hash::make($value['U_Password']),
                                    'email' => $email.'@mailinator.com',
                                    //'response' => json_encode($value),
                                );

                    $obj = User::updateOrCreate(
                                            [
                                                'sales_specialist_name' => @$value['SalesEmployeeName'],
                                            ],
                                            $insert
                                        );
                }

                if(!empty($data['odata.nextLink'])){
                    $this->addSalesPersonsDataInDatabase($data['odata.nextLink']);
                }
            }
        }
    }
}
