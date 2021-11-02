<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPAuthentication;
use Session;

class SapApiController extends Controller
{
	/*public function __construct()
	{

		$this->middleware(function ($request, $next) {
			dd("yes");
			return $next($request);
		});

	}*/

    public function __construct()
	{
		$this->headers = $this->cookie = array();
		$this->cookie['B1SESSION'] = "";
		$this->cookie['CompanyDB'] = "TEST-APBW";
		$this->cookie['ROUTEID'] = ".node0";

		$this->last_login_time = 0;

	}

    public function index(){

    	// dd(session()->all());

    	$authentication = new SAPAuthentication('TEST-APBW', 'manager', 'test');

    	dd($authentication->getAuthenticationSession());

    	$this->setSessionId();

    	dd($this->headers,$this->cookie,$this->last_login_time,session()->all());

    	// dd($this->login());	
    	try {
	    	$client = new \GuzzleHttp\Client();
	        $response = $client->request(
	            'GET',
	            'https://sap.northtrend.com:50000/b1s/v1/Users',
	            [
	            	'headers' => [
	            					'Cookie' => 'B1SESSION=bc2bb10c-3b18-11ec-8000-20677ce77e84; CompanyDB=TEST-APBW; ROUTEID=.node0'
	            				],
	            	'verify' => false,

	            ]
	        );

	        dd(json_decode($response->getBody(),true));

	        // if(in_array($response->getStatusCode(), [200,201])){
	        //     $response = json_decode($response->getBody(),true);
    		
    	} catch (\Exception $e) {
    		dd($e->getCode());
    	}

    }

    public function login()
    {
    	try {
	    	$client = new \GuzzleHttp\Client();
	        $response = $client->request(
	            'POST',
	            'https://sap.northtrend.com:50000/b1s/v1/Login',
	            [
	            	'verify' => false,
	            	'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
	            	'body' => json_encode([
				        'CompanyDB' => 'TEST-APBW',
				        'Password' => 'test',
				        'UserName' => 'manager',
				    ])
	            ]
	        );
	        if(in_array($response->getStatusCode(), [200,201])){
	        	$response = json_decode($response->getBody(),true);
	        	$time = time();

	        	Session::put('B1SESSION',$response['SessionId']);
	        	Session::put('last_login_time',$time);

	        	// session(['B1SESSION' => $response['SessionId']]);
	        	// session(['last_login_time' => $response['SessionId']]);

				$this->cookie['B1SESSION'] = $response['SessionId'];
				$this->last_login_time = $time;	

				// dd(session()->all());   	
	        }
	       
    	} catch (\Exception $e) {
    		dd($e);
    	}
    }

    public function setSessionId()
    {
    	//dd(Session::get('B1SESSION'));

    	// dd(session()->all());
		if(Session::has('B1SESSION')){
           $this->cookie['B1SESSION'] = Session::get('B1SESSION');
        }

        if(Session::has('last_login_time')){
           $this->last_login_time = Session::get('last_login_time');
        }

        if($this->last_login_time == 0){
        	// Login
        	$this->login();
        }else{
        	$current_time = time();

        	$mins = ($current_time - $this->last_login_time) / 60;

        	if($mins >= 25 || $mins <= 0){
        		// Login
        		$this->login();
        	}

        }

		$this->headers['Cookie'] = "B1SESSION=".$this->cookie['B1SESSION'].";";
		$this->headers['Cookie'] .= "CompanyDB=".$this->cookie['CompanyDB'].";";
		$this->headers['Cookie'] .= "ROUTEID=".$this->cookie['ROUTEID'].";";
    }
}
