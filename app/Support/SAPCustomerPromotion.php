<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\CustomerPromotion;

class SAPCustomerPromotion
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $headers;

    protected $sap_connection_id;
    protected $customer_promotion_id;

	protected $database;
	protected $username;
	protected $password;

    public function __construct($database, $username, $password)
    {
        $this->headers = array();
        $this->authentication = new SAPAuthentication($database, $username, $password);
        $this->headers['Cookie'] = $this->authentication->getSessionCookie();
        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Accept'] = 'application/json';

        $this->httpClient = new Client();

        $this->sap_connection_id = $this->customer_promotion_id = null;
    }

    public function requestSapApi($url = '/b1s/v1/Quotations', $method = "POST", $body = "")
    {
    	try {
            $response = $this->httpClient->request(
                $method,
                env('SAP_API_URL').$url,
                [
                    'headers' => $this->headers,
                    'verify' => false,
                    'body' => json_encode($body),
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
            return array(
                        'status' => false,
                        'data' => []
                    );
        }
    }

    public function pushOrderDetailsInDatabase($data)
    {
        if($data){
            $insert = array(
                        'doc_entry' => $data['DocEntry'],
                        'doc_num' => $data['DocNum'],
                        'doc_type' => $data['DocType'],
                        'document_status' => $data['DocumentStatus'],
                        'doc_date' => $data['DocDate'],
                        'doc_due_date' => $data['DocDueDate'],
                        'card_code' => $data['CardCode'],
                        'card_name' => $data['CardName'],
                        'address' => $data['Address'],
                        'doc_total' => $data['DocTotal'],
                        'doc_currency' => $data['DocCurrency'],
                        'journal_memo' => $data['JournalMemo'],
                        'payment_group_code' => $data['PaymentGroupCode'],
                        'sales_person_code' => (int)$data['SalesPersonCode'],
                        'u_brand' => $data['U_BRAND'],
                        'u_branch' => $data['U_BRANCH'],
                        'u_commitment' => @$data['U_COMMITMENT'],
                        'u_time' => $data['U_TIME'],
                        'u_posono' => $data['U_POSONO'],
                        'u_posodate' => $data['U_POSODATE'],
                        'u_posotime' => $data['U_POSOTIME'],
                        'created_at' => $data['CreationDate'],
                        'updated_at' => $data['UpdateDate'],
                        //'response' => json_encode($order),

                        'sap_connection_id' => $this->sap_connection_id,
                        'customer_promotion_id' => $this->customer_promotion_id,

                    );

            $obj = Quotation::updateOrCreate([
                                        'doc_entry' => $data['DocEntry'],
                                        'sap_connection_id' => $this->sap_connection_id,
                                    ],
                                    $insert
                                );

            if(!empty($data['DocumentLines'])){

                $quo_items = $data['DocumentLines'];

                foreach($quo_items as $item){
                    $fields = array(
                        'order_id' => $obj->id,
                        'line_num' => @$item['LineNum'],
                        'item_code' => @$item['ItemCode'],
                        'item_description' => @$item['ItemDescription'],
                        'quantity' => @$item['Quantity'],
                        'ship_date' => @$item['ShipDate'],
                        'price' => @$item['Price'],
                        'price_after_vat' => @$item['PriceAfterVAT'],
                        'currency' => @$item['Currency'],
                        'rate' => @$item['Rate'],
                        'discount_percent' => @$item['DiscountPercent'] != null ? @$item['DiscountPercent'] : 0.0,
                        'werehouse_code' => @$item['WarehouseCode'],
                        'sales_person_code' => @$item['SalesPersonCode'],
                        'gross_price' => @$item['GrossPrice'],
                        'gross_total' => @$item['GrossTotal'],
                        'gross_total_fc' => @$item['GrossTotalFC'],
                        'gross_total_sc' => @$item['GRossTotalSC'] != null ? @$item['GRossTotalSC'] : 0.0,
                        'ncm_code' => @$item['NCMCode'],
                        'ship_to_code' => @$item['ShipToCode'],
                        'ship_to_description' => @$item['ShipToDescription'],
                        //'response' => json_encode($item),
                    );

                    $item_obj = QuotationItem::updateOrCreate([
                                    'order_id' => $obj->id,
                                    'item_code' => $item['ItemCode'],
                                ],
                                $fields
                            );
                }
            }
        }
    }


    public function createOrder($id){
        $body = $this->madeSapData($id);

        $response = array();

        if(!empty($body)){
            $response = $this->requestSapApi('/b1s/v1/Quotations', "POST", $body);

            $status = $response['status'];
            $data = $response['data'];

            if($status){
                $customer_promotion = CustomerPromotion::find($id);
                $customer_promotion->doc_entry = $data['DocEntry'];
                $customer_promotion->is_sap_pushed = true;
                $customer_promotion->save();

                $this->pushOrderDetailsInDatabase($data);
            }
        }

        return $response;
    }

    public function updateOrder($id, $doc_entry){
        $body = $this->madeSapData($id);

        $response = array();

        if(!empty($body)){
            $response = $this->requestSapApi('/b1s/v1/Quotations('.$doc_entry.')', "PUT", $body);

            $status = $response['status'];
            $data = $response['data'];

            if($status){

                $customer_promotion = CustomerPromotion::find($id);
                $customer_promotion->doc_entry = $data['DocEntry'];
                $customer_promotion->is_sap_pushed = true;
                $customer_promotion->save();

                $this->pushOrderDetailsInDatabase($data);
            }
        }

        return $response;
    }

    public function cancelOrder($id, $doc_entry){

        $response = array(
                            'status' => false,
                            'data' => []
                        );

        if(!empty($doc_entry)){

            try {
                $response = $this->httpClient->request(
                    "POST",
                    env('SAP_API_URL').'/b1s/v1/Quotations('.$doc_entry.')/Cancel',
                    [
                        'headers' => $this->headers,
                        'verify' => false,
                    ]
                );

                if(in_array($response->getStatusCode(), [200,201,204])){
                    $response = json_decode($response->getBody(),true);

                    $customer_promotion = CustomerPromotion::find($id);
                    $customer_promotion->doc_entry = null;
                    $customer_promotion->is_sap_pushed = false;
                    $customer_promotion->save();


                    $where = array(
                                'doc_entry' => $doc_entry,
                                'customer_promotion_id' => $id,
                            );

                    $quotation = Quotation::where($where)->first();
                    $quotation->document_status = "Cancelled";
                    $quotation->save();

                    return array(
                                'status' => true,
                                'data' => []
                            );
                }

            } catch (\Exception $e) {

            }

        }

        return $response;
    }

    public function madeSapData($id){

        $response = [];
        $customer_promotion = CustomerPromotion::find($id);

        $this->customer_promotion_id = $id;

        if(!is_null($customer_promotion)){

            if(@$customer_promotion->user->customer->card_code){

                $this->sap_connection_id = @$customer_promotion->sap_connection_id;

                $response['CardCode'] = @$customer_promotion->user->customer->card_code;
                $response['CardName'] = @$customer_promotion->user->customer->card_name;
                $response['DocTotal'] = @$customer_promotion->total_amount;
                $response['Address'] = @$customer_promotion->customer_bp_address->address;
                $response['DocCurrency'] = "PHP";
                $response['DocumentLines'] = [];

                if(@$customer_promotion->products){

                    foreach (@$customer_promotion->products as $p) {

                        foreach (@$p->deliveries as $d) {

                            $temp = array(

                                        'ItemCode' => @$p->product->item_code,
                                        'ItemDescription' => @$p->product->item_name,
                                        'UnitPrice' => @$p->price - @$p->discount,
                                        'Price' => @$p->price,
                                        'Quantity' => @$d->delivery_quantity,
                                        'ShipDate' => @$d->delivery_date,

                                    );

                            array_push($response['DocumentLines'], $temp);
                        }

                    }

                }

            }

        }


        return $response;

    }
}
