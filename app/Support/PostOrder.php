<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Log;

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
        Log::info(print_r($body,true));
    	try {
            $response = $this->httpClient->request(
                'POST',
                get_sap_api_url().'/b1s/v1/Quotations',
                [
                    'headers' => $this->headers,
                    'verify' => false,
                    'body' => json_encode($body),
                    'timeout' => 15,
                ]
            );
            Log::info(print_r($response,true));
            if(in_array($response->getStatusCode(), [200,201])){
                $response = json_decode($response->getBody(),true);
                return array(
                            'status' => true,
                            'data' => $response
                        );
            } else {
                // $statusCode = $response->getStatusCode();
                return array(
                    'status' => false,
                    'data' => $response->getStatusCode(),
                );
            }

        } catch (\Exception $e) {
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

    public function pushOrder($data)
    {
        Log::info(print_r($data,true));
        if(!empty($data)){
            $response = $this->pushOrderInSAP($data);
        }

        if($response['status']){
            $data = $response['data'];

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
                            //'response' => json_encode($order),

                            'last_sync_at' => current_datetime(),
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
                        );

                        $item_obj = QuotationItem::updateOrCreate([
                                        'quotation_id' => $obj->id,
                                        'item_code' => $item['ItemCode'],
                                    ],
                                    $fields
                                );
                    }

                }
            }
        } // else {
        //     $data = $response['data'];
        //     // dd($data->getBody());
        //     $message = 'API Error: '.$data->getStatusCode();
        //     return ['status' => $response['status'], 'message' => $message];
        // }
        return ['status' => $response['status'], 'message' => $response['data']];
    }
}
