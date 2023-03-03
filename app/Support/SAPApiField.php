<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\SapConnectionApiField;
use App\Models\SapConnectionApiFieldValue;
use App\Models\SapConnection;

class SAPApiField
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

    // Get API Field data
    public function getApiFieldData($url)
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

    // Store All API Field Records In DB
    public function addApiFieldInDatabase($input)
    {
        $url = '/b1s/v1/UserFieldsMD(TableName=\''.$input->sap_table_name.'\',FieldID='.$input->sap_field_id.')';

        $response = $this->getApiFieldData($url);
        
        if($response['status']){

            $data = $response['data'];

            if(@$data['ValidValuesMD']){

                foreach ($data['ValidValuesMD'] as $value) {
                    
                    $insert = array(
                                    'key' => @$value['Value'],
                                    'value' => @$value['Description'],
                                    'sap_connection_api_field_id' => @$input->id,
                                    'sap_connection_id' => @$input->sap_connection_id,
                                    'real_sap_connection_id' => $input->real_sap_connection_id,
                                    'last_sync_at' => current_datetime(),
                                );

                    $obj = SapConnectionApiFieldValue::updateOrCreate(
                                            [
                                                'key' => @$value['Value'],
                                                'sap_connection_api_field_id' => @$input->id,
                                            ],
                                            $insert
                                        );
                }

                if(!empty($this->log_id)){
                    add_sap_log([
                            'status' => "completed",
                        ], $this->log_id);
                }
            }
        }
    }
}
