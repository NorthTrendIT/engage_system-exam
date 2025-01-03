<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use GuzzleHttp\Exception\RequestException;
use App\Mail\DataSyncFailed; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SAPTestAPI
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

        public function checkLogin($sendMail)
        {
            try {
                $response = $this->httpClient->request(
                    'POST',
                    get_sap_api_url().'/b1s/v1/Login',
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
                    return ['status' => true, 'message' => "API Working"];
                } else {
                    return ['status' => false, 'message' => "API Not Working"];
                }
            } catch (\Exception $e) {
                // Handle the exception and send an email 
                $details = $e->getMessage();

                if($sendMail){ //only send email for admin
                    Mail::to('itsupport@northtrend.com')->send(new DataSyncFailed($details));
                }

                $now = Carbon::now();
                if ($now->hour == 0){
                    $date = $now->subDay()->toDateString();;
                    $logFile = storage_path('logs/dataSync-failed.log'); 
                    file_put_contents($logFile, $date . PHP_EOL, FILE_APPEND);
                }
                
                $code = $e->getCode();
                if($code){
                    $message = "";
                    $statusCode = !empty($e->getResponse()->getStatusCode()) ? $e->getResponse()->getStatusCode() : NULL;
                    $responsePhrase = !empty($e->getResponse()->getReasonPhrase()) ? $e->getResponse()->getReasonPhrase() : NULL;
                    if($statusCode == 401){
                        $message = 'Username and password do not match.';
                    } else {
                        $message = $statusCode.' '.$responsePhrase;
                    }
                } else{
                    $message = "API is Down.";
                }
                $response = ['status' => false, 'message' => $message];
                return $response;
            }
        }
}
