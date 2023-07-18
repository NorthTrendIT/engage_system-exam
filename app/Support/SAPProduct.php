<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Product;
use App\Jobs\StoreProducts;
use App\Jobs\SyncNextProducts;
use App\Models\SapConnection;

class SAPProduct
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $headers;

	protected $database;
	protected $username;
	protected $password;
    protected $log_id;

    public function __construct($database, $username, $password, $log_id = false)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->log_id = $log_id;

        $this->headers = $this->cookie = array();
        $this->authentication = new SAPAuthentication($database, $username, $password);
        $this->headers['Cookie'] = $this->authentication->getSessionCookie();

        $this->httpClient = new Client();
    }

    // Get Product data
    public function getProductData($url = '/b1s/v1/Items')
    {
    	try {
            $response = $this->httpClient->request(
                'GET',
                get_sap_api_url().$url,
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

            if(!empty($this->log_id)){
                add_sap_log([
                                'status' => "error",
                                'error_data' => $e->getMessage(),
                            ], $this->log_id);                
            }

            return array(
                                'status' => false,
                                'data' => []
                            );
        }
    }

    // Store All Product Records In DB
    public function addProductDataInDatabase($url = false)
    {
        $where = array(
                    'db_name' => $this->database,
                    'user_name' => $this->username,
                );

        $sap_connection = SapConnection::where($where)->first();
                
        if($url){
            $response = $this->getProductData($url);
        }else{
            $latestData = Product::orderBy('updated_date','DESC')->where('sap_connection_id', $sap_connection->id)->first();
            if(!empty($latestData)){
                $latestData->updated_date = '2020-07-15'; //temporary
                $url = '/b1s/v1/Items?$filter=UpdateDate ge \''.$latestData->updated_date.'\' or CreateDate ge \''.$latestData->updated_date.'\'';
                $response = $this->getProductData($url);
            } else {
                $response = $this->getProductData();
            }
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                // Store Data of Product in database
                StoreProducts::dispatch($data['value'],@$sap_connection->id);

                if(isset($data['odata.nextLink'])){
                    //$this->addProductDataInDatabase($data['odata.nextLink']);

                    SyncNextProducts::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);
                }else{
                    if(!empty($this->log_id)){
                        add_sap_log([
                                'status' => "completed",
                            ], $this->log_id);
                    }
                }
            }
        }
    }
}
