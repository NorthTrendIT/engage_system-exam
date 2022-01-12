<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Quotation;
use App\Models\QuotationItem;

class SAPQuotations
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
    public function getQuotationData($url = '/b1s/v1/Quotations')
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
    public function addQuotationsDataInDatabase($url = false)
    {
        if($url){
            $response = $this->getQuotationData($url);
        }else{
            $response = $this->getQuotationData();
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                foreach ($data['value'] as $value) {

                    $insert = array(
                                'doc_entry' => $value['DocEntry'],
                                'doc_num' => $value['DocNum'],
                                'doc_type' => $value['DocType'],
                                'document_status' => $value['DocumentStatus'],
                                'doc_date' => $value['DocDate'],
                                'doc_time' => $data['DocTime'],
                                'doc_due_date' => $value['DocDueDate'],
                                'card_code' => $value['CardCode'],
                                'card_name' => $value['CardName'],
                                'address' => $value['Address'],
                                'doc_total' => $value['DocTotal'],
                                'doc_currency' => $value['DocCurrency'],
                                'journal_memo' => $value['JournalMemo'],
                                'payment_group_code' => $value['PaymentGroupCode'],
                                'sales_person_code' => (int)$value['SalesPersonCode'],
                                'u_brand' => $value['U_BRAND'],
                                'u_branch' => $value['U_BRANCH'],
                                'u_commitment' => @$value['U_COMMITMENT'],
                                'u_time' => $value['U_TIME'],
                                'u_posono' => $value['U_POSONO'],
                                'u_posodate' => $value['U_POSODATE'],
                                'u_posotime' => $value['U_POSOTIME'],
                                'u_remarks' => $value['U_REMARKS'],
                                'created_at' => $value['CreationDate'],
                                'updated_at' => $value['UpdateDate'],
                                //'response' => json_encode($order),
                            );

                    $obj = Quotation::updateOrCreate([
                                                'doc_entry' => $value['DocEntry'],
                                            ],
                                            $insert
                                        );

                    if(!empty($value['DocumentLines'])){

                        $quo_items = @$value['DocumentLines'];

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
                                        ],
                                        $fields
                                    );
                        }

                    }
                }

                if(!empty($data['odata.nextLink'])){
                    $this->addQuotationsDataInDatabase($data['odata.nextLink']);
                } else {
                    add_sap_log([
                        'status' => "completed",
                    ], $this->log_id);
                }
            }
        }
    }
}
