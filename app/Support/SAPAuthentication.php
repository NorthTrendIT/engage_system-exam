<?php


namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Models\SapCompanySession;

class SAPAuthentication
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $session;

	protected $database;
	protected $username;
	protected $password;

    public function __construct($database, $username, $password)
    {
    	$this->database = $database;
    	$this->username = $username;
    	$this->password = $password;

        $this->httpClient = new Client();
    }

    protected function login()
    {
    	try {
	    	$response = $this->httpClient->request(
	            'POST',
	            'https://sap.northtrend.com:50000/b1s/v1/Login',
	            [
	            	'verify' => false,
	            	'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
	            	'body' => json_encode([
				        'CompanyDB' => $this->database,
				        'Password' => $this->password,
				        'UserName' => $this->username,
				    ])
	            ]
	        );

	        if (in_array($response->getStatusCode(), [200,201])) {
	        	$response = json_decode($response->getBody(), true);

	        	$this->saveAuthentication($response);
	        }	
	       
    	} catch (\Exception $e) {
    		dd($e);
    	}
    }

    protected function saveAuthentication($response)
    {
    	$this->session = SapCompanySession::firstOrCreate([
    			'company_name' => $this->database,
    			'username' => $this->username,
    			['expires_at', '>=', $currentTime = Carbon::now()]
    		], [
    			'company_name' => $this->database,
    			'username' => $this->username,
    			'session_id' => $response['SessionId'],
    			'expires_at' => $currentTime->addMinutes(25),
    		]);
    }

    protected function getSession()
    {
    	if ($this->session) {
			return $this->session;
		}

		return $this->session = SapCompanySession::where([
			'company_name' => $this->database,
			'username' => $this->username,
		])->where('expires_at', '>=', Carbon::now())->first();
    }

    
    public function getAuthenticationSession()
    {
    	if ($session = $this->getSession()) {
			return $session;
    	}

    	$this->login();

		return $this->getSession();
    }
}
