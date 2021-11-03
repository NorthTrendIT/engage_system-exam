<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPAuthentication;
use Session;

class SapApiController extends Controller
{
    public function __construct(){
		$this->headers = $this->cookie = array();
	}

    public function index(){

    	$authentication = new SAPAuthentication('TEST-APBW', 'manager', 'test');

    	$this->setSessionId($session = $authentication->getAuthenticationSession());

    	try {
	    	$client = new \GuzzleHttp\Client();
	        $response = $client->request(
	            'GET',
	            env('SAP_API_URL').'/Users',
	            [
	            	'headers' => $this->headers,
	            	'verify' => false,

	            ]
	        );

	        if(in_array($response->getStatusCode(), [200,201])){
	        	$response = json_decode($response->getBody(),true);
	        	dd($response);
	        }
    		
    	} catch (\Exception $e) {
    		dd($e->getCode());
    	}

    }

    public function setSessionId($session)
    {
		$this->headers['Cookie'] = "B1SESSION=".$session->session_id.";";
		$this->headers['Cookie'] .= "CompanyDB=".$session->company_name.";";
		$this->headers['Cookie'] .= "ROUTEID=.node0;";
    }
}
