<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Jobs\StoreOrders;
use App\Jobs\SyncNextOrders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SapConnection;
use App\Models\Customer;

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

    // Store All Customer Records In DB
    public function addOrdersDataInDatabase($url = false)
    {
        $where = array(
                    'db_name' => $this->database,
                    'user_name' => $this->username,
                );

        $sap_connection = SapConnection::where($where)->first();
                
        if($url){
            $response = $this->getOrderData($url);
        }else{
            $latestData = Order::orderBy('updated_date','DESC')->where('sap_connection_id', $sap_connection->id)->first();
            if(!empty($latestData)){
                $time = Carbon::now()->subMinutes(30);
                $url = '/b1s/v1/Orders?$filter=UpdateDate ge \''.$latestData->updated_date.'\' and UpdateTime ge \''.$time->toTimeString().'\'';

                $response = $this->getOrderData($url);
            } else {
                $response = $this->getOrderData();
            }
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                // Store Data of Order in database
                StoreOrders::dispatch($data['value'], @$sap_connection->id);

                if(isset($data['odata.nextLink'])){

                    SyncNextOrders::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);

                    //$this->addOrdersDataInDatabase($data['odata.nextLink']);
                } else {
                    if(!empty($this->log_id)){
                        add_sap_log([
                            'status' => "completed",
                        ], $this->log_id);
                    }
                }
            }
        }
    }


    // Store Specific Orders Data
    public function addSpecificOrdersDataInDatabase($doc_entry = false)
    {
        if($doc_entry){
            $url = '/b1s/v1/Orders('.$doc_entry.')';
            $response = $this->getOrderData($url);

            if($response['status']){
                $order = $response['data'];

                if(!empty($order)){

                    $where = array(
                                'db_name' => $this->database,
                                'user_name' => $this->username,
                            );

                    $sap_connection = SapConnection::where($where)->first();
                    $sap_connection_id = $sap_connection->id;

                    if($sap_connection->id == 1){ // GROUP Cagayan, Davao NEED TO STORE in Solid Trend 
                        $customer = Customer::where('card_code', $order['CardCode'])->where('sap_connection_id', 5)->first();
                        if(!empty($customer)){
                            $sap_connection_id = 5;
                        }
                    }

                    
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
                                'document_status' => $order['DocumentStatus'],
                                'u_omsno' => $order['U_OMSNo'],
                                //'response' => json_encode($order),

                                'updated_date' => $order['UpdateDate'],
                                'last_sync_at' => current_datetime(),
                                'sap_connection_id' => $sap_connection_id,
                                'real_sap_connection_id' => $sap_connection->id,
                            );

                    if(!empty($order['DocumentLines'])){
                        array_push($insert, array('base_entry' => $order['DocumentLines'][0]['BaseEntry']));
                    }

                    $obj = Order::updateOrCreate([
                                                'doc_entry' => $order['DocEntry'],
                                                'sap_connection_id' => $sap_connection_id,
                                            ],
                                            $insert
                                        );

                    if(!empty($order['DocumentLines'])){

                        $item_codes = [];
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
                                'open_amount' => @$value['OpenAmount'],
                                'remaining_open_quantity' => @$value['RemainingOpenQuantity'],
                                //'response' => json_encode($value),

                                'sap_connection_id' => $sap_connection_id,
                                'real_sap_connection_id' => $sap_connection->id,
                                'line_status' => @$value['LineStatus'],
                                'u_itemstat' => @$value['U_ITEMSTAT'],
                            );

                            $item_obj = OrderItem::updateOrCreate([
                                            'order_id' => $obj->id,
                                            'item_code' => @$value['ItemCode'],
                                        ],
                                        $item
                                    );

                            array_push($item_codes, @$value['ItemCode']);

                            if(!is_null(@$value['BaseEntry'])){
                                $obj->base_entry = @$value['BaseEntry'];
                                $obj->save();
                            }
                        }

                        OrderItem::where('order_id', $obj->id)->whereNotIn('item_code', $item_codes)->delete();

                    }
                }
            }
        }
    }
}
