<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;

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

        public function checkLogin()
        {
            try {
                $response = $this->httpClient->request(
                    'POST',
                    env('SAP_API_URL').'/b1s/v1/Login',
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

                if ($response->successful()) {
                    return ['status' => true, 'message' => "API Working"];
                } elseif($response->failed()) {
                    return ['status' => false, 'message' => "API Not Working, Failed"];
                } elseif($response->clientError()){
                    return ['status' => false, 'message' => "API Not Working, Client Error"];
                } elseif($response->serverError()){
                    return ['status' => false, 'message' => "API Not Working, Server Error"];
                }
            } catch (\Exception $e) {
                // abort(500);
                $response = ['status' => false, 'message' => 'API Not Working'];
                return $response;
            }
        }
}
