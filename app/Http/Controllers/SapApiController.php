<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPAuthentication;
use Session;

class SapApiController extends Controller
{
    public function __construct(){
		$this->headers = $this->cookie = array();
    	$this->authentication = new SAPAuthentication('TEST-APBW', 'manager', 'test');
	}

    public function index(){


    	$this->setSessionId($session = $this->authentication->getAuthenticationSession());

    	try {
	    	$client = new \GuzzleHttp\Client();
	        $response = $client->request(
	            'GET',
	            get_sap_api_url().'/b1s/v1/Users',
	            [
	            	'headers' => $this->headers,
	            	'verify' => false,

	            ]
	        );

	        if(in_array($response->getStatusCode(), [200,201])){
	        	$response = json_decode($response->getBody(),true);
	        	// dd($response);
	        }

    	} catch (\Exception $e) {
    		// dd($e->getCode());
    	}

    }

    public function getCustomerList(){
    	$this->setSessionId($session = $this->authentication->getAuthenticationSession());

    	try {
	    	$client = new \GuzzleHttp\Client();
	        $response = $client->request(
	            'GET',
	            get_sap_api_url().'/b1s/v1/BusinessPartners',
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
    		//dd($e->getCode());
    		return array(
	        					'status' => false,
	        					'data' => []
	        				);
    	}
    }

    public function setSessionId($session)
    {
		$this->headers['Cookie'] = "B1SESSION=".$session->session_id.";";
		$this->headers['Cookie'] .= "CompanyDB=".$session->company_name.";";
		$this->headers['Cookie'] .= "ROUTEID=.node0;";
    }
}
