<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use GuzzleHttp\Exception\RequestException;

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

                if (in_array($response->getStatusCode(), [200,201])) {
                    return ['status' => true, 'message' => "API Working"];
                } else {
                    return ['status' => false, 'message' => "API Not Working"];
                }
            } catch (\Exception $e) {
                // abort(500);
                $response = ['status' => false, 'message' => 'API Not Working'];
                return $response;
            }
        }
}
