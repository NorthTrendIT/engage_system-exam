<?php

namespace App\Support;

use GuzzleHttp\Client;
use App\Support\SAPAuthentication;

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

    public function __construct($database, $username, $password, $sap_connection_id)
    {
        $this->headers = array();
        $this->authentication = new SAPAuthentication($database, $username, $password);
        $this->headers['Cookie'] = $this->authentication->getSessionCookie();
        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Accept'] = 'application/json';

        $this->httpClient = new Client();

        $this->sap_connection_id = $sap_connection_id;
        if($sap_connection_id == 5){
            $this->real_sap_connection_id = 1;
        } else {
            $this->real_sap_connection_id = $sap_connection_id;
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
}
