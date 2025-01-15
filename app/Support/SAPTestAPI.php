<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use GuzzleHttp\Exception\RequestException;
use App\Mail\DataSyncFailed; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\SapApiUrl;
use Illuminate\Support\Facades\Session;
use App\Jobs\TestApiHostJob;
use Illuminate\Support\Facades\DB;

class SAPTestAPI
{
		/** @var Client */
        protected $httpClient;

        /** @var string */
        protected $session;

        protected $database;
        protected $username;
        protected $password;
        protected $url;
        public $searchString;

        public function __construct($database, $username, $password, $url = false)
        {
            $this->database = $database;
            $this->username = $username;
            $this->password = $password;
            $this->url = $url;
            $this->searchString =  'TestApiHostJob';

            $this->httpClient = new Client();
        }

        public function checkLogin($sendMail)
        {
            try {
                $host_url = ($this->url) ? $this->url : get_sap_api_url();
                $response = $this->httpClient->request(
                    'POST',
                    $host_url.'/b1s/v1/Login',
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

                    $logFilePath = storage_path('logs/hostApiUrlFailure.log');
                    if (file_exists($logFilePath)) {
                        unlink($logFilePath);
                        $host = SapApiUrl::where('url', $host_url)->first();
                        SapApiUrl::where('id', '!=', $host->id)->update(['active' => false]);
                        $host->update(['active' => true]);

                        DB::table('jobs')->where('payload', 'LIKE', '%' . $this->searchString . '%')->delete();
                        DB::statement('TRUNCATE TABLE sap_company_sessions');
                    }

                    return ['status' => true, 'message' => "API Working"];
                } else {
                    return ['status' => false, 'message' => "API Not Working"];
                }
            } catch (\Exception $e) {
                // Handle the exception and send an email 
                $details = $e->getMessage();

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

                if($message === "API is Down."){
                    $jobCount = DB::table('jobs')->where('payload', 'LIKE', '%' . $this->searchString . '%')->count();
                    if ($jobCount === 0) {
                        if($sendMail){ //only send email for admin
                            Mail::to('itsupport@northtrend.com')->send(new DataSyncFailed($details));
                        }
                        $this->checkHostUrlSession();
                    }
                }

                $response = ['status' => false, 'message' => $message];
                return $response;
            }
        }

        public function checkHostUrlSession(){
            $now = Carbon::now();
            $logFile = storage_path('logs/hostApiUrlFailure.log'); 
            file_put_contents($logFile, $now->format('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);

            $active_host = get_sap_api_url();
            $hosts = SapApiUrl::where('url', '!=', $active_host)->get();
            foreach($hosts as $h){
               TestApiHostJob::dispatch($h->url, $this->database, $this->username, $this->password);
            }
        }

}
