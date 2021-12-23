<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Quotation;
use App\Models\QuotationItem;

class PostOrder
{
	/** @var Client */
	protected $httpClient;

	/** @var string */
	protected $headers;

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
    }

    public function pushOrderInSAP($body)
    {
    	try {
            $response = $this->httpClient->request(
                'POST',
                env('SAP_API_URL').'/b1s/v1/Quotations',
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
            $message = "API Error:";
            $statusCode = !empty($e->getResponse()->getStatusCode()) ? $e->getResponse()->getStatusCode() : NULL;
            $responsePhrase = !empty($e->getResponse()->getReasonPhrase()) ? $e->getResponse()->getReasonPhrase() : NULL;
            if($statusCode == 401){
                $message = $message.' Username and password do not match.';
            } else {
                $message = $message.' '.$statusCode.' '.$responsePhrase;
            }
            return array(
                        'status' => false,
                        'data' => $message
                    );
        }
    }

    public function pushOrder($data)
    {
        if(!empty($data)){
            $response = $this->pushOrderInSAP($data);
        }

        if($response['status']){
            $data = $response['data'];

            if($data){

                $status = '';

                if($data['Cancelled'] == "tYES" ){
                    $status = "Cancelled";
                } else {
                    if($data['DocumentStatus'] == 'bost_Open') {
                        $status = 'On Process';
                    }
                    if($data['DocumentStatus'] == 'bost_Open' && $data['U_SOSTAT'] == 'For Delivery'){
                        $status = 'For Delivery';
                    }
                    if($data['DocumentStatus'] == 'bost_Open' && $data['U_SOSTAT'] == 'Delivered'){
                        $status = 'Delivered';
                    }
                    if($data['DocumentStatus'] == 'bost_Open' && $data['U_SOSTAT'] == 'Confirmed'){
                        $status = 'Complated';
                    }
                }

                $insert = array(
                            'doc_entry' => $data['DocEntry'],
                            'doc_num' => $data['DocNum'],
                            'doc_type' => $data['DocType'],
                            'document_status' => $status,
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
                        );

                $obj = Quotation::updateOrCreate([
                                            'doc_entry' => $data['DocEntry'],
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

        return ['status' => $response['status'], 'message' => $response['data']];
    }
}
