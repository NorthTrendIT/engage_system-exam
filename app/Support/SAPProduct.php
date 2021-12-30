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
                env('SAP_API_URL').$url,
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

            add_sap_log([
                            'status' => "error",
                            'error_data' => $e->getMessage(),
                        ], $this->log_id);

            return array(
                                'status' => false,
                                'data' => []
                            );
        }
    }

    // Store All Product Records In DB
    public function addProductDataInDatabase($url = false)
    {
        if($url){
            $response = $this->getProductData($url);
        }else{
            $response = $this->getProductData();
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                /*foreach ($data['value'] as $value) {
                    
                    $insert = array(

                                    'item_code' => @$value['ItemCode'],
                                    'item_name' => @$value['ItemName'],
                                    'foreign_name' => @$value['ForeignName'],
                                    'items_group_code' => @$value['ItemsGroupCode'],
                                    'customs_group_code' => @$value['CustomsGroupCode'],
                                    'sales_vat_group' => @$value['SalesVATGroup'],
                                    'purchase_vat_group' => @$value['PurchaseVATGroup'],
                                    'created_date' => @$value['CreateDate']." ".@$value['CreateTime'],
                                    'is_active' => @$value['Valid'] == "tYES" ? true : false,
                                    //'response' => json_encode($value),
                                );

                    $obj = Product::updateOrCreate(
                                            [
                                                'item_code' => @$value['ItemCode'],
                                            ],
                                            $insert
                                        );
                }*/

                $where = array(
                            'db_name' => $this->database,
                            'user_name' => $this->username,
                        );

                $sap_connection = SapConnection::where($where)->first();

                // Store Data of Product in database
                StoreProducts::dispatch($data['value'],@$sap_connection->id);

                if(isset($data['odata.nextLink'])){
                    //$this->addProductDataInDatabase($data['odata.nextLink']);
                    
                    SyncNextProducts::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);
                }else{
                    add_sap_log([
                            'status' => "completed",
                        ], $this->log_id);
                }
            }
        }
    }
}
