<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\User;
use App\Models\SapConnection;
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
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        
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

        $where = array(
                    'db_name' => $this->database,
                    'user_name' => $this->username,
                );

        $sap_connection = SapConnection::where($where)->first();

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                foreach ($data['value'] as $value) {
                    $name = explode(" ", $value['SalesEmployeeName'], 2);
                    $email = strtolower($name[0]).$value['SalesEmployeeCode']."-".@$sap_connection->id.'@mailinator.com';

                    $insert = array(
                                    'role_id' => 2,
                                    'sales_employee_code' => $value['SalesEmployeeCode'],
                                    'sales_specialist_name' => $value['SalesEmployeeName'],
                                    'first_name' => !empty($name[0]) ? $name[0] : null,
                                    'last_name' => !empty($name[1]) ? $name[1] : null,
                                    'is_active' => $value['Active'] == "tYES" ? true : false,
                                    'password' => Hash::make('12345678'),
                                    'email' => $email,
                                    //'response' => json_encode($value),
                                    'sap_connection_id' => @$sap_connection->id,
                                );

                    $obj = User::updateOrCreate(
                                            [
                                                'role_id' => 2,
                                                'email' => $email,
                                                'sap_connection_id' => @$sap_connection->id,
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
