<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\CustomerGroup;
use App\Models\SapConnection;

class SAPCustomerGroup
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

    // Get Customer Group data
    public function getCustomerGroupData($url = '/b1s/v1/BusinessPartnerGroups')
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

                return array(
                                'status' => true,
                                'data' => $response
                            );
            }
            
        } catch (\Exception $e) {

            if($this->log_id){
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

    // Store All Customer Group Records In DB
    public function addCustomerGroupDataInDatabase($url = false)
    {
        if($url){
            $response = $this->getCustomerGroupData($url);
        }else{
            $response = $this->getCustomerGroupData();
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
                    
                    if(@$value['Type'] != "bbpgt_CustomerGroup"){
                        continue;
                    }
                
                    $insert = array(
                                    'code' => @$value['Code'],
                                    'name' => @$value['Name'],
                                    'type' => @$value['Type'],
                                    'sap_connection_id' => @$sap_connection->id,
                                );

                    $obj = CustomerGroup::updateOrCreate(
                                            [
                                                'code' => @$value['Code'],
                                                'sap_connection_id' => @$sap_connection->id,
                                            ],
                                            $insert
                                        );
                }

                // Store Data of Customer Group in database
                if(isset($data['odata.nextLink'])){
                    $this->addCustomerGroupDataInDatabase($data['odata.nextLink']);
                }else{
                    add_sap_log([
                            'status' => "completed",
                        ], $this->log_id);
                }
            }
        }
    }
}
