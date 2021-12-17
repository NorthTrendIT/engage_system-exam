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
            } catch (\GuzzleHttp\Exception\ConnectException $e) {

                $response = ['status' => false, 'message' => 'ConnectException'];
                return $response;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $response = ['status' => false, 'message' => 'ClientException'];
                return $response;
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {

                $response = ['status' => false, 'message' => 'BadResponseException'];
                return $response;
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                $response = ['status' => false, 'message' => 'ServerException'];
                return $response;
            } catch (\Exception $e) {
                // abort(500);
                $response = ['status' => false, 'message' => 'API Not Working'];
                return $response;
            }
        }
}
