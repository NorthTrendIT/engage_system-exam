<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SapConnection;
use App\Jobs\StoreOrders;
use App\Jobs\SyncNextOrders;

class SAPOrders
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

    // Get Sales Persons data
    public function getOrderData($url = '/b1s/v1/Orders')
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

    // Store All Customer Records In DB
    public function addOrdersDataInDatabase($url = false)
    {
        if($url){
            $response = $this->getOrderData($url);
        }else{
            $response = $this->getOrderData();
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                /*foreach ($data['value'] as $order) {

                    $insert = array(
                                'doc_entry' => $order['DocEntry'],
                                'doc_num' => $order['DocNum'],
                                'doc_type' => $order['DocType'],
                                'doc_date' => $order['DocDate'],
                                'doc_due_date' => $order['DocDueDate'],
                                'card_code' => $order['CardCode'],
                                'card_name' => $order['CardName'],
                                'address' => $order['Address'],
                                'doc_total' => $order['DocTotal'],
                                'doc_currency' => $order['DocCurrency'],
                                'journal_memo' => $order['JournalMemo'],
                                'payment_group_code' => $order['PaymentGroupCode'],
                                'sales_person_code' => (int)$order['SalesPersonCode'],
                                'u_brand' => $order['U_BRAND'],
                                'u_branch' => $order['U_BRANCH'],
                                'u_commitment' => @$order['U_COMMITMENT'],
                                'u_time' => $order['U_TIME'],
                                'u_posono' => $order['U_POSONO'],
                                'u_posodate' => $order['U_POSODATE'],
                                'u_posotime' => $order['U_POSOTIME'],
                                'u_sostat' => $order['U_SOSTAT'],
                                'cancelled' => ($order['Cancelled'] == 'tNO' ? 'No' : 'Yes'),
                                'cancel_date' => $order['CancelDate'],
                                'created_at' => $order['CreationDate'],
                                'updated_at' => $order['UpdateDate'],
                                //'response' => json_encode($order),
                            );

                    $obj = Order::updateOrCreate([
                                                'doc_entry' => $order['DocEntry'],
                                            ],
                                            $insert
                                        );

                    if(!empty($order['DocumentLines'])){

                        $order_items = @$order['DocumentLines'];

                        foreach($order_items as $value){
                            $item = array(
                                'order_id' => $obj->id,
                                'line_num' => @$value['LineNum'],
                                'item_code' => @$value['ItemCode'],
                                'item_description' => @$value['ItemDescription'],
                                'quantity' => @$value['Quantity'],
                                'ship_date' => @$value['ShipDate'],
                                'price' => @$value['Price'],
                                'price_after_vat' => @$value['PriceAfterVAT'],
                                'currency' => @$value['Currency'],
                                'rate' => @$value['Rate'],
                                'discount_percent' => @$value['DiscountPercent'] != null ? @$value['DiscountPercent'] : 0.0,
                                'werehouse_code' => @$value['WarehouseCode'],
                                'sales_person_code' => @$value['SalesPersonCode'],
                                'gross_price' => @$value['GrossPrice'],
                                'gross_total' => @$value['GrossTotal'],
                                'gross_total_fc' => @$value['GrossTotalFC'],
                                'gross_total_sc' => @$value['GRossTotalSC'] != null ? @$value['GRossTotalSC'] : 0.0,
                                'ncm_code' => @$value['NCMCode'],
                                'ship_to_code' => @$value['ShipToCode'],
                                'ship_to_description' => @$value['ShipToDescription'],
                                //'response' => json_encode($value),
                            );

                            $item_obj = OrderItem::updateOrCreate([
                                            'order_id' => $obj->id,
                                            'item_code' => $value['ItemCode'],
                                        ],
                                        $item
                                    );
                        }

                    }
                }*/

                $where = array(
                            'db_name' => $this->database,
                            'user_name' => $this->username,
                        );

                $sap_connection = SapConnection::where($where)->first();

                // Store Data of Order in database
                StoreOrders::dispatch($data['value'], @$sap_connection->id);

                if(isset($data['odata.nextLink'])){

                    SyncNextOrders::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);

                    //$this->addOrdersDataInDatabase($data['odata.nextLink']);
                } else {
                    add_sap_log([
                        'status' => "completed",
                    ], $this->log_id);
                }
            }
        }
    }
}
