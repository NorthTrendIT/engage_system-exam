<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Jobs\StoreInvoices;
use App\Jobs\SyncNextInvoices;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SapConnection;
use App\Models\Customer;
use Log;

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
    public $invoice_data      = [];
    public $grand_total_qty   = 0;
    public $grand_total_price = 0;
    public $grand_total_price_after_vat = 0;

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
    public function getInvoiceData($url = '/b1s/v1/Invoices')
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
    public function addInvoicesDataInDatabase($url = false)
    {   

        $where = array(
                    'db_name' => $this->database,
                    'user_name' => $this->username,
                );

        $sap_connection = SapConnection::where($where)->first();
                
        if($url){
            $response = $this->getInvoiceData($url);
        }else{
            $latestData = Invoice::orderBy('updated_date','DESC')->where('sap_connection_id', $sap_connection->id)->first();
            if(!empty($latestData)){
                $time = Carbon::now()->subMinutes(30);
                $url = '/b1s/v1/Invoices?$filter=UpdateDate ge \''.$latestData->updated_date.'\' and UpdateTime ge \''.$time->toTimeString().'\'';
                $response = $this->getInvoiceData($url);
            } else {
                $response = $this->getInvoiceData();
            }
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                // Store Data of Invoices in database
                StoreInvoices::dispatch($data['value'],@$sap_connection->id);

                if(isset($data['odata.nextLink'])){

                    SyncNextInvoices::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);

                    //$this->addInvoicesDataInDatabase($data['odata.nextLink']);
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


    // Store Specific Invoices Data
    public function addSpecificInvoicesDataInDatabase($doc_entry = false)
    {
        if($doc_entry){
            // $url = '/b1s/v1/Invoices('.$doc_entry.')';
            $url = '/b1s/v1/Invoices?$filter=U_OMSNo eq '.$doc_entry.'';
            $response = $this->getInvoiceData($url);

            if($response['status']){
                $invoice = $response['data'];

                if(!empty($invoice['value'])){
                    $invoices = $invoice['value'];

                    $grand_total_of_invoice_items = 0;
                    foreach($invoices as $invoice){

                        $where = array(
                                    'db_name' => $this->database,
                                    'user_name' => $this->username,
                                );

                        $sap_connection = SapConnection::where($where)->first();
                        $sap_connection_id = $sap_connection->id;

                        if($sap_connection->id == 1){ // GROUP Cagayan, Davao NEED TO STORE in Solid Trend 
                            $customer = Customer::where('card_code', $invoice['CardCode'])->where('sap_connection_id', 5)->first();
                            if(!empty($customer)){
                                $sap_connection_id = 5;
                            }
                        }

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
                                    // 'updated_at' => $invoice['UpdateDate'],
                                    'document_status' => $invoice['DocumentStatus'],
                                    'cancelled' => @$invoice['Cancelled'] == 'tYES' ? 'Yes' : 'No',
                                    'u_omsno' => @$invoice['U_OMSNo'],
                                    'update_date' => @$invoice['UpdateDate'],
                                    //'response' => json_encode($invoice),

                                    'updated_date' => $invoice['UpdateDate'],
                                    'end_delivery_date' => $invoice['EndDeliveryDate'],
                                    'u_delivery' => $invoice['U_DELIVERY'],
                                    'last_sync_at' => current_datetime(),
                                    'sap_connection_id' => $sap_connection_id,
                                    'real_sap_connection_id' => $sap_connection->id,
                                );
                        if(!empty($invoice['DocumentLines'])){
                            array_push($insert, array('base_entry' => $invoice['DocumentLines'][0]['BaseEntry']));
                        }

                        $obj = Invoice::updateOrCreate([
                                                    'doc_entry' => @$invoice['DocEntry'],
                                                    'sap_connection_id' => $sap_connection_id,
                                                ],
                                                $insert
                                            );

                        if(!empty($invoice['DocumentLines'])){

                            $item_codes = [];
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
                                    'open_amount' => @$value['OpenAmount'],
                                    'remaining_open_quantity' => @$value['RemainingOpenQuantity'],
                                    //'response' => json_encode($value),

                                    'sap_connection_id' => $sap_connection_id,
                                    'real_sap_connection_id' => $sap_connection->id,
                                );

                                $currentInvItemCount = InvoiceItem::where([
                                                            'invoice_id' => $obj->id,
                                                            'item_code' => @$value['ItemCode'],
                                                            'price' => @$value['Price']
                                                        ])->count();
 
                                if($currentInvItemCount > 1){
                                    InvoiceItem::where([
                                                            'invoice_id' => $obj->id,
                                                            'item_code' => @$value['ItemCode'],
                                                            'price' => @$value['Price']
                                                        ])->orderBy('id','desc')->first()->delete();
                                } 

                                $item_obj = InvoiceItem::updateOrCreate([
                                                'invoice_id' => $obj->id,
                                                'item_code' => @$value['ItemCode'],
                                            ],
                                            $item
                                        );

                                array_push($item_codes, @$item['ItemCode']);

                                if(!is_null(@$value['BaseEntry'])){
                                    $obj->base_entry = @$value['BaseEntry'];
                                    $obj->save();
                                }
                            }

                            InvoiceItem::where('invoice_id', $obj->id)->whereNotIn('item_code', $item_codes)->delete();
                        }

                        if(@$obj->order->cancelled === "Yes"){
                            $obj->order->quotation()->update(['status' =>'Cancelled']);
                        }
                        if(@$obj->cancelled === "No" && @$obj->order->cancelled === "No"){ //invoice is not cancelled
                            $check = $obj->order->quotation->items ?? '-';
                            if($check !== '-'){
                                $q_items = $obj->order->quotation->items->sum('quantity');
                                $grand_total_of_invoice_items = $grand_total_of_invoice_items + $obj->items->sum('quantity');
                                $inv_stat = ($q_items === $grand_total_of_invoice_items)? 'Completed' : 'Partially Served';
                                
                                $obj->order->quotation()->update(['status' =>$inv_stat]);
                            }
                        }
                    } //end sa foreach


                } //end sa if
            }
        }
    }

    public function fetchInvoiceDataForReporting($url){

        $response = $this->getInvoiceData($url);
        if($response['status']){
            $invoice = $response['data'];

            if(!empty($invoice['value'])){

                $invoices = $invoice['value'];
                foreach($invoices as $invoice){ //invoice details

                    if(!empty($invoice['DocumentLines'])){

                        $invoice_items = @$invoice['DocumentLines'];
                        foreach($invoice_items as $line){ //invoice items

                            $status = ($invoice['DocumentStatus'] === 'bost_Open' && $invoice['Cancelled'] === 'tNO') ? 'Unpaid' : 'Paid';
                            $this->grand_total_qty   += $line['Quantity'];
                            $this->grand_total_price += $line['Price'];
                            $this->grand_total_price_after_vat += $line['PriceAfterVAT'];

                            $this->invoice_data[] = [
                                            'DocNum'   => $invoice['DocNum'],
                                            'DocDate'  => date("m-d-Y", strtotime($invoice['DocDate'])),
                                            'ItemCode' => $line['ItemCode'],
                                            'ItemDescription' => $line['ItemDescription'],
                                            'Brand'      => $line['CostingCode2'],
                                            'Quantity'   => $line['Quantity'],
                                            'UoM'      => $line['MeasureUnit'],
                                            'Price'    => $line['Price'],
                                            'GrossTotal' => $line['GrossTotal'],
                                            'Status'    => $status
                                        ];
                        }
                    }
                }
            }

            if(isset($response['data']['odata.nextLink'])){ //call loop again
                $this->fetchInvoiceDataForReportingNext($response['data']['odata.nextLink']);
            }
            // else{
            //     Log::info(print_r($this->invoice_data,true));
            // }
        }    
    }


    public function fetchInvoiceDataForReportingV2($url){

        $response = $this->getInvoiceData($url);
        if($response['status']){
            $invoice = $response['data'];

            if(!empty($invoice['value'])){

                $invoices = $invoice['value'];
                foreach($invoices as $invoice){ //invoice details
                    $inv     = $invoice['Invoices'];
                    $line    = $invoice['Invoices/DocumentLines'];

                    $status = ($inv['DocumentStatus'] === 'C' && $inv['Cancelled'] === 'N') ? 'Paid' : 'Unpaid';
                    $this->grand_total_qty   += $line['Quantity'];
                    $this->grand_total_price += $line['Price'];
                    $this->grand_total_price_after_vat += $line['GrossTotal'];

                    $this->invoice_data[] = [
                                    'DocNum'   => $inv['DocNum'],
                                    'DocDate'  => date("m-d-Y", strtotime($inv['DocDate'])),
                                    'ItemCode' => $line['ItemCode'],
                                    'ItemDescription' => $line['ItemDescription'],
                                    'Brand'      => $line['CostingCode2'],
                                    'Quantity'   => $line['Quantity'],
                                    'UoM'      => $line['MeasureUnit'],
                                    'Price'    => $line['Price'],
                                    'GrossTotal' => $line['GrossTotal'],
                                    'Status'    => $status
                                ];
                    
                }
            }

            if(isset($response['data']['odata.nextLink'])){ //call loop again
                $this->fetchInvoiceDataForReportingNext($response['data']['odata.nextLink']);
            }
        }
    }    

    public function fetchInvoiceDataForReportingNext($url){
        $this->fetchInvoiceDataForReportingV2($url);
    }


    public function fetchInvoiceDataForCollectionReport($url){

        $response = $this->getInvoiceData($url);
        if($response['status']){
            $invoice = $response['data'];

            if(!empty($invoice['value'])){

                $invoices = $invoice['value'];
                foreach($invoices as $invoice){ //invoice details
                    $inv     = $invoice;
                    $this->invoice_data[] = [
                                    'DocNum'   => $inv['DocNum'],
                                    'DocDate'  => $inv['DocDate'],
                                    'DocTotal' => $inv['DocTotal'],
                                    'DeliveryDate' => $inv['U_COMMITMENT']
                                ];  
                }
            }

            if(isset($response['data']['odata.nextLink'])){ //call loop again
                $this->fetchInvoiceDataForCollectionReportNext($response['data']['odata.nextLink']);
            }
        }
    } 

    public function fetchInvoiceDataForCollectionReportNext($url){
        $this->fetchInvoiceDataForCollectionReport($url);
    }







}
