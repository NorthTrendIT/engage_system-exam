<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Territory;
use App\Jobs\StoreTerritories;
use App\Jobs\SyncNextTerritories;
use App\Models\SapConnection;

class SAPTerritory
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

    // Get Territory data
    public function getTerritoryData($url = '/b1s/v1/Territories')
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

    // Store All Territory Records In DB
    public function addTerritoryDataInDatabase($url = false)
    {
        $where = array(
            'db_name' => $this->database,
            'user_name' => $this->username,
        );

        $sap_connection = SapConnection::where($where)->first();

        if($url){
            $response = $this->getTerritoryData($url);
        }else{
            $response = $this->getTerritoryData();
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                // Store Data of Territory in database
                StoreTerritories::dispatch($data['value'], @$sap_connection->id);

                if(isset($data['odata.nextLink'])){

                    SyncNextTerritories::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);

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
