<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Jobs\StoreInvoices;
use App\Jobs\SyncNextInvoices;
use App\Models\SapConnection;

class SAPInvoices
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
    public function getInvoiceData($url = '/b1s/v1/Orders')
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
    public function addInvoicesDataInDatabase($url = false)
    {
        if($url){
            $response = $this->getInvoiceData($url);
        }else{
            $response = $this->getInvoiceData();
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                /*foreach ($data['value'] as $invoice) {

                    $insert = array(
                                'doc_entry' => $invoice['DocEntry'],
                                'doc_num' => $invoice['DocNum'],
                                'doc_type' => $invoice['DocType'],
                                'doc_date' => $invoice['DocDate'],
                                'doc_due_date' => $invoice['DocDueDate'],
                                'card_code' => $invoice['CardCode'],
                                'card_name' => $invoice['CardName'],
                                'address' => $invoice['Address'],
                                'doc_total' => $invoice['DocTotal'],
                                'doc_currency' => $invoice['DocCurrency'],
                                'journal_memo' => $invoice['JournalMemo'],
                                'payment_group_code' => $invoice['PaymentGroupCode'],
                                'sales_person_code' => (int)$invoice['SalesPersonCode'],
                                'u_brand' => $invoice['U_BRAND'],
                                'u_branch' => $invoice['U_BRANCH'],
                                'u_commitment' => @$invoice['U_COMMITMENT'],
                                'u_time' => $invoice['U_TIME'],
                                'u_posono' => $invoice['U_POSONO'],
                                'u_posodate' => $invoice['U_POSODATE'],
                                'u_posotime' => $invoice['U_POSOTIME'],
                                'u_sostat' => $invoice['U_SOSTAT'],
                                'created_at' => $invoice['CreationDate'],
                                'updated_at' => $invoice['UpdateDate'],
                                //'response' => json_encode($invoice),
                            );

                    $obj = Invoice::updateOrCreate([
                                                'doc_entry' => @$invoice['DocEntry'],
                                            ],
                                            $insert
                                        );

                    if(!empty($invoice['DocumentLines'])){

                        $invoice_items = @$invoice['DocumentLines'];

                        foreach($invoice_items as $value){
                            $item = array(
                                'invoice_id' => $obj->id,
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

                            $item_obj = InvoiceItem::updateOrCreate([
                                            'invoice_id' => $obj->id,
                                            'item_code' => @$value['ItemCode'],
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

                // Store Data of Invoices in database
                StoreInvoices::dispatch($data['value'],@$sap_connection->id);

                if(isset($data['odata.nextLink'])){

                    SyncNextInvoices::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);

                    //$this->addInvoicesDataInDatabase($data['odata.nextLink']);
                } else {
                    add_sap_log([
                        'status' => "completed",
                    ], $this->log_id);
                }
            }
        }
    }
}
