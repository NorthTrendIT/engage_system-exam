<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Territory;
use App\Jobs\StoreTerritories;
use App\Jobs\SyncNextTerritories;

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

    // Store All Territory Records In DB
    public function addTerritoryDataInDatabase($url = false)
    {
        if($url){
            $response = $this->getTerritoryData($url);
        }else{
            $response = $this->getTerritoryData();
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                // Store Data of Territory in database
                StoreTerritories::dispatch($data['value']);

                if(isset($data['odata.nextLink'])){

                    SyncNextTerritories::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);

                }else{
                    add_sap_log([
                            'status' => "completed",
                        ], $this->log_id);
                }
            }
        }
    }
}
