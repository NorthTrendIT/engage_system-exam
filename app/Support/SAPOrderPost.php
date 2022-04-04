<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\LocalOrder;

class SAPOrderPost
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $headers;

    protected $sap_connection_id;
    protected $real_sap_connection_id;

	protected $database;
	protected $username;
	protected $password;

    public function __construct($database, $username, $password, $sap_connection_id)
    {
        $this->headers = array();
        $this->authentication = new SAPAuthentication($database, $username, $password);
        $this->headers['Cookie'] = $this->authentication->getSessionCookie();
        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Accept'] = 'application/json';

        $this->httpClient = new Client();

        $this->sap_connection_id = $sap_connection_id;
        if($sap_connection_id == 5){
            $this->real_sap_connection_id = 1;
        } else {
            $this->real_sap_connection_id = $sap_connection_id;
        }
    }

    public function requestSapApi($url = '/b1s/v1/Quotations', $method = "POST", $body = ""){
    	try {
            $response = $this->httpClient->request(
                $method,
                get_sap_api_url().$url,
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
            } else {
                return array(
                    'status' => false,
                    'data' => $response->getStatusCode(),
                );
            }

        } catch (\Exception $e) {
            // dd($e);
            $code = $e->getCode();
            if($code){
                $message = "API Error:";
                $statusCode = !empty($e->getResponse()->getStatusCode()) ? $e->getResponse()->getStatusCode() : NULL;
                $responsePhrase = !empty($e->getResponse()->getReasonPhrase()) ? $e->getResponse()->getReasonPhrase() : NULL;
                if($statusCode == 401){
                    $message = $message.' Username and password do not match.';
                } else {
                    $message = $message.' '.$statusCode.' '.$responsePhrase;
                }
            } else {
                $message = $message." API is Down.";
            }

            return array(
                        'status' => false,
                        'data' => $message
                    );
        }
    }

    public function pushOrderDetailsInDatabase($data){
        if($data){
            $insert = array(
                        'doc_entry' => $data['DocEntry'],
                        'doc_num' => $data['DocNum'],
                        'num_at_card' => $data['NumAtCard'],
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
                        'last_sync_at' => current_datetime(),

                        'sap_connection_id' => $this->sap_connection_id,
                        'real_sap_connection_id' => $this->real_sap_connection_id,
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
                        'quotation_id' => $obj->id,
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

                        'sap_connection_id' => $this->sap_connection_id,
                        'real_sap_connection_id' => $this->real_sap_connection_id,
                    );

                    $item_obj = QuotationItem::updateOrCreate([
                                    'quotation_id' => $fields['quotation_id'],
                                    'item_code' => $item['ItemCode'],
                                ],
                                $fields
                            );
                }
            }
        }
    }


    public function pushOrder($id){
        $body = $this->madeSapData($id);
        $response = array();

        if(!empty($body)){
            $response = $this->requestSapApi('/b1s/v1/Quotations', "POST", $body);

            $status = $response['status'];
            $data = $response['data'];

            $order = LocalOrder::where('id', $id)->first();
            if($status){
                $order->confirmation_status = 'C';
                $order->doc_entry = $data['DocEntry'];
                $order->doc_num = $data['DocNum'];
                $order->message = null;

                $this->pushOrderDetailsInDatabase($data);
                $this->updateNumAtCardInOrder($data['DocEntry']);
            } else {
                $order->confirmation_status = 'ERR';
                $order->message = $data;
            }
            $order->save();
        }

        return $response;
    }

    public function madeSapData($id){

        $response = [];
        $order = LocalOrder::where('id', $id)->with(['sales_specialist', 'customer', 'address', 'items.product'])->first();

        $response['CardCode'] = @$order->customer->card_code;
        $response['CardName'] = @$order->customer->card_name;
        $response['DocDueDate'] = @$order->due_date;
        $response['DocCurrency'] = 'PHP';
        $response['Address'] = @$order->address->address;

        // if(@$order->sales_specialist->sales_employee_code && @$order->sales_specialist->is_active){
        //     $response['SalesPersonCode'] = @$order->sales_specialist->sales_employee_code;
        // }

        $response['DocumentLines'] = [];

        foreach($order->items as $item){
            $temp = array(
                'ItemCode' => @$item->product->item_code,
                'ItemDescription' => @$item->product->item_name,
                'Quantity' => @$item->quantity,
                'Price' => @$item->price,
                'UnitPrice' => @$item->price,
                'ShipDate' => @$order->due_date,
            );
            array_push($response['DocumentLines'], $temp);
        }

        return $response;
    }

    public function updateNumAtCardInOrder($doc_entry){
        // \Log::debug('The updateNumAtCardInOrder called -->'. $doc_entry);
        // \Log::debug('The updateNumAtCardInOrder called -->'. @$this->sap_connection_id);

        $quotation = Quotation::where('doc_entry', $doc_entry)->where('sap_connection_id', @$this->sap_connection_id)->first();
        $response = array();

        if(!empty($quotation)){
            $num_at_card = "OMS# ".$doc_entry;

            $body = array(
                            'NumAtCard' => $num_at_card,
                        );

            $response = $this->requestSapApi('/b1s/v1/Quotations('.$doc_entry.')', "PATCH", $body);

            $status = $response['status'];
            $data = $response['data'];
            if($data == '204'){
                $quotation->num_at_card = $num_at_card;
                $quotation->save();
            }
        }

        return $response;
    }
}
