<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\CustomerPromotion;
use App\Models\CustomerPromotionProduct;
use App\Models\CustomerPromotionProductDelivery;

class SAPCustomerPromotion
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $headers;

    protected $sap_connection_id;
    protected $real_sap_connection_id;
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

        $this->sap_connection_id = $this->real_sap_connection_id = $this->customer_promotion_id = null;
    }

    public function requestSapApi($url = '/b1s/v1/Quotations', $method = "POST", $body = "")
    {
        // \Log::debug('The requestSapApi called.');
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
            }elseif(in_array($response->getStatusCode(), [204])){
                return array(
                            'status' => true,
                            'data' => []
                        );
            }

        } catch (\Exception $e) {
            \Log::error('The requestSapApi called error : -->'. $e->getMessage());
            return array(
                        'status' => false,
                        'data' => []
                    );
        }
    }

    public function pushOrderDetailsInDatabase($data)
    {
        // \Log::debug('The pushOrderDetailsInDatabase called.');

        if($data){
            $insert = array(
                        'doc_entry' => $data['DocEntry'],
                        'doc_num' => $data['DocNum'],
                        'doc_type' => $data['DocType'],
                        'document_status' => $data['DocumentStatus'],
                        'doc_date' => $data['DocDate'],
                        'doc_time' => $data['DocTime'],
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
                        //'response' => json_encode($order),

                        'sap_connection_id' => $this->sap_connection_id,
                        'real_sap_connection_id' => $this->real_sap_connection_id,

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
                        'open_amount' => @$item['OpenAmount'],
                        'remaining_open_quantity' => @$item['RemainingOpenQuantity'],
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


    public function createOrder($id){
        // \Log::debug('The createOrder called -->'. $id);

        $body = $this->madeSapData($id);

        $response = array();

        if(!empty($body)){
            $response = $this->requestSapApi('/b1s/v1/Quotations', "POST", $body);
            $status = $response['status'];
            $data = $response['data'];

            if($status){
                $obj = CustomerPromotionProductDelivery::find($id);
                $obj->doc_entry = $data['DocEntry'];
                $obj->is_sap_pushed = true;
                $obj->sap_connection_id = $this->sap_connection_id;
                $obj->real_sap_connection_id = $this->real_sap_connection_id;
                $obj->save();

                $this->pushOrderDetailsInDatabase($data);

                $this->updateNumAtCardInOrder($data['DocEntry']);
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

                $obj = CustomerPromotionProductDelivery::find($id);
                $obj->doc_entry = $data['DocEntry'];
                $obj->is_sap_pushed = true;
                $obj->sap_connection_id = $this->sap_connection_id;
                $obj->real_sap_connection_id = $this->real_sap_connection_id;
                $obj->save();

                $this->pushOrderDetailsInDatabase($data);
            }
        }

        return $response;
    }

    public function cancelOrder($id, $doc_entry){
        // \Log::debug('The cancelOrder called -->'. $id);


        $response = array(
                            'status' => false,
                            'data' => []
                        );

        if(!empty($doc_entry)){

            try {
                $response = $this->httpClient->request(
                    "POST",
                    get_sap_api_url().'/b1s/v1/Quotations('.$doc_entry.')/Cancel',
                    [
                        'headers' => $this->headers,
                        'verify' => false,
                    ]
                );

                if(in_array($response->getStatusCode(), [200,201,204])){
                    $response = json_decode($response->getBody(),true);

                    $obj = CustomerPromotionProductDelivery::find($id);
                    $obj->doc_entry = null;
                    $obj->is_sap_pushed = false;
                    $obj->sap_connection_id = $this->sap_connection_id;
                    $obj->real_sap_connection_id = $this->real_sap_connection_id;
                    $obj->save();


                    $where = array(
                                'doc_entry' => $doc_entry,
                                'customer_promotion_id' => @$obj->customer_promotion_product->customer_promotion_id,
                            );

                    $quotation = Quotation::where($where)->first();
                    if(!is_null($quotation)){
                        $quotation->document_status = "Cancelled";
                        $quotation->cancelled = "Yes";
                        $quotation->save();
                    }

                    return array(
                                'status' => true,
                                'data' => []
                            );
                }

            } catch (\Exception $e) {
                \Log::error('The requestSapApi called error : -->'. $e->getMessage());
            }

        }

        return $response;
    }

    public function madeSapData($id){

        $response = [];
        $customer_promotion_product_delivery = CustomerPromotionProductDelivery::find($id);
        $customer_promotion_product = @$customer_promotion_product_delivery->customer_promotion_product;
        $customer_promotion = @$customer_promotion_product->customer_promotion;

        $this->customer_promotion_id = @$customer_promotion->id;

        if(!is_null($customer_promotion)){

            if(@$customer_promotion->user->customer->card_code){

                $this->sap_connection_id = @$customer_promotion->sap_connection_id;
                $this->real_sap_connection_id = @$customer_promotion->user->customer->real_sap_connection_id;

                $response['CardCode'] = @$customer_promotion->user->customer->card_code;
                $response['CardName'] = @$customer_promotion->user->customer->card_name;
                // $response['DocTotal'] = @$customer_promotion->total_amount;

                if(strtolower(@$customer_promotion->customer_bp_address->street) == strtolower(@$customer_promotion->user->customer->card_name)){
                    $response['Address'] = @$customer_promotion->customer_bp_address->street;
                }else{
                    $response['Address'] = @$customer_promotion->customer_bp_address->address;
                    if(!empty(@$customer_promotion->customer_bp_address->street)){
                        $response['Address'] .= ", ".@$customer_promotion->customer_bp_address->street;
                    }
                }

                if(!empty(@$customer_promotion->customer_bp_address->city)){
                    $response['Address'] .= ", ".@$customer_promotion->customer_bp_address->city;
                }
                if(!empty(@$customer_promotion->customer_bp_address->state)){
                    $response['Address'] .= ", ".@$customer_promotion->customer_bp_address->state;
                }
                if(!empty(@$customer_promotion->customer_bp_address->zip_code)){
                    $response['Address'] .= ", ".@$customer_promotion->customer_bp_address->zip_code;
                }
                if(!empty(@$customer_promotion->customer_bp_address->country)){
                    $response['Address'] .= ", ".@$customer_promotion->customer_bp_address->country;
                }
                

                if(@$customer_promotion->sales_specialist->sales_employee_code && @$customer_promotion->sales_specialist->is_active){
                    $response['SalesPersonCode'] = @$customer_promotion->sales_specialist->sales_employee_code;
                }

                $response['DocCurrency'] = "PHP";
                $response['DocumentLines'] = [];

                if(@$customer_promotion->products){

                    $temp = array(
                                'ItemCode' => @$customer_promotion_product->product->item_code,
                                'ItemDescription' => @$customer_promotion_product->product->item_name,
                                'Price' => @$customer_promotion_product->price,
                                'UnitPrice' => @$customer_promotion_product->price - @$customer_promotion_product->discount,
                                'Quantity' => @$customer_promotion_product_delivery->delivery_quantity,
                                'ShipDate' => @$customer_promotion_product_delivery->delivery_date,
                            );

                    array_push($response['DocumentLines'], $temp);
                }

            }

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

            if($status){
                $quotation->num_at_card = $num_at_card;
                $quotation->save();
            }
        }

        return $response;
    }

}
