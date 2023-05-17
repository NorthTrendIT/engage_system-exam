<?php

namespace App\Support;

use App\Jobs\StoreVatGroups;
use GuzzleHttp\Client;
use App\Support\SAPAuthentication;
use App\Models\SapConnection;
use App\Jobs\SyncNextVatGroups;
use Log;

class SAPVatGroup
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $headers;

    protected $sap_connection_id;
    protected $real_sap_connection_id;

	protected $database;
	protected $username;
	protected $password;
    protected $log_id;
    protected $authentication;

    public function __construct($database, $username, $password,  $log_id)
    {
        $this->headers = array();
        $this->authentication = new SAPAuthentication($database, $username, $password);
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->log_id = $log_id;

        $this->headers['Cookie'] = $this->authentication->getSessionCookie();
        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Accept'] = 'application/json';

        $this->httpClient = new Client();

    }




    // Get Product data
    public function getVatGroupData($url = '/b1s/v1/VatGroups')
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






    public function requestSapApi($url = "", $method = "", $body = ""){
    	try {
            $response = $this->httpClient->request(
                $method,
                get_sap_api_url().$url,
                [
                    'headers' => $this->headers,
                    'verify' => false,
                    'body' => json_encode($body),
                ]
            );

            if(in_array($response->getStatusCode(), [200,201])){
                $response = json_decode($response->getBody(),true);
                return array(
                            'status' => true,
                            'data' => $response
                        );
            } else {
                return array(
                    'status' => false,
                    'data' => $response->getStatusCode(),
                );
            }

        } catch (\Exception $e) {
            // dd($e);
            $code = $e->getCode();
            if($code){
                $message = "API Error:";
                $statusCode = !empty($e->getResponse()->getStatusCode()) ? $e->getResponse()->getStatusCode() : NULL;
                $responsePhrase = !empty($e->getResponse()->getReasonPhrase()) ? $e->getResponse()->getReasonPhrase() : NULL;
                if($statusCode == 401){
                    $message = $message.' Username and password do not match.';
                } else {
                    $message = $message.' '.$statusCode.' '.$responsePhrase;
                }
            } else {
                $message = $message." API is Down.";
            }

            return array(
                        'status' => false,
                        'data' => $message
                    );
        }
    }

    public function getVat($vat_group){
        $customer_vat = 0;
        if(@$vat_group !== null){
            $vat = $this->requestSapApi('/b1s/v1/VatGroups(\''.@$vat_group.'\')', "GET");
            $rounded = $vat['data']['VatGroups_Lines'][0]['Rate'] * 1;

            $customer_vat = ($rounded === 0 || $rounded === 0.0) ? 0 : '1.'.$rounded; 
        }

        return $customer_vat;
    }


    public function addVatGroupDataInDatabase($url = false){
        
        ini_set('memory_limit', '512M'); //set limit

        $where = array(
            'db_name' => $this->database,
            'user_name' => $this->username,
        );

        $sap_connection = SapConnection::where($where)->first();
                
        if($url){
            $response = $this->getVatGroupData($url);
        }else{
            $response = $this->getVatGroupData('/b1s/v1/VatGroups');
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                // Store Data of Product in database
                StoreVatGroups::dispatch($data['value'],@$sap_connection->id);

                if(isset($data['odata.nextLink'])){
                    SyncNextVatGroups::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);
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
