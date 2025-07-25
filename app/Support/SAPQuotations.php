<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Jobs\StoreQuotations;
use App\Jobs\SyncNextQuotations;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SapConnection;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Log;

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
    public function addQuotationsDataInDatabase($url = false)
    {
        $where = array(
                    'db_name' => $this->database,
                    'user_name' => $this->username,
                );

        $sap_connection = SapConnection::where($where)->first();


        if($url){
            $response = $this->getQuotationData($url);
        }else{
            $latestData = Quotation::orderBy('updated_date','DESC')->where('sap_connection_id', $sap_connection->id)->first();
            if(!empty($latestData)){
                //$latestData->updated_date = '2020-03-20';
                $time = Carbon::now()->subMinutes(60);
                $url = '/b1s/v1/Quotations?$filter=UpdateDate ge \''.$time->toDateString().'\' and UpdateTime ge \''.$time->toTimeString().'\'';
                $response = $this->getQuotationData($url);
            } else {
                $response = $this->getQuotationData();
            }
        }

        if($response['status']){
            $data = $response['data'];

            if($data['value']){

                // Store Data of Quotations in database
                StoreQuotations::dispatch($data['value'],@$sap_connection->id);

                if(!empty($data['odata.nextLink'])){
                    // dd($data);
                    SyncNextQuotations::dispatch($this->database, $this->username, $this->password, $data['odata.nextLink'], $this->log_id);

                    //$this->addQuotationsDataInDatabase($data['odata.nextLink']);
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


    // Store Specific Quotations Data
    public function addSpecificQuotationsDataInDatabase($doc_entry = false)
    {
        if($doc_entry){
            $url = '/b1s/v1/Quotations('.$doc_entry.')';
            $response = $this->getQuotationData($url);
            if($response['status']){
                $value = $response['data'];

                if(!empty($value)){

                    $where = array(
                                'db_name' => $this->database,
                                'user_name' => $this->username,
                            );

                    $sap_connection = SapConnection::where($where)->first();
                    $sap_connection_id = $sap_connection->id;

                    if($sap_connection->id == 1){ // GROUP Cagayan, Davao NEED TO STORE in Solid Trend 
                        $customer = Customer::where('card_code', $value['CardCode'])->where('sap_connection_id', 5)->first();
                        if(!empty($customer)){
                            $sap_connection_id = 5;
                        }
                    }

                    $insert = array(
                                'doc_entry' => $value['DocEntry'],
                                'doc_num' => $value['DocNum'],
                                'doc_type' => $value['DocType'],
                                'num_at_card' => $value['NumAtCard'],
                                'document_status' => $value['DocumentStatus'],
                                'doc_date' => $value['DocDate'],
                                'doc_time' => $value['DocTime'],
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
                                'cancelled' => @$value['Cancelled'] == 'tYES' ? 'Yes' : 'No',
                                'cancel_date' => $value['CancelDate'],
                                'u_omsno' => @$value['U_OMSNo'],
                                //'response' => json_encode($order),

                                'updated_date' => $value['UpdateDate'],
                                'last_sync_at' => current_datetime(),
                                'sap_connection_id' => $sap_connection_id,
                                'real_sap_connection_id' => $sap_connection->id,
                                'comments' => $value['Comments'],
                            );

                    if(!empty($value['DocumentLines'])){
                        array_push($insert, array('base_entry' => $value['DocumentLines'][0]['BaseEntry']));
                    }

                    $obj = Quotation::updateOrCreate([
                                                'doc_entry' => $value['DocEntry'],
                                                'sap_connection_id' => $sap_connection_id,
                                            ],
                                            $insert
                                        );
                                        
                    $quot_stat = ($obj->cancelled === "Yes") ? 'Cancelled' : 'Pending';
                    Quotation::where('id', $obj->id)->update(['status' => $quot_stat]);

                    if(!empty($value['DocumentLines'])){

                        $item_codes = [];
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
                                'open_amount' => @$item['OpenAmount'],
                                'remaining_open_quantity' => @$item['RemainingOpenQuantity'],
                                //'response' => json_encode($item),

                                'line_status' => @$item['LineStatus'],
                                'u_itemstat' => @$item['U_ITEMSTAT'],

                                'sap_connection_id' => $sap_connection_id,
                                'real_sap_connection_id' => $sap_connection->id,
                            );

                            $item_obj = QuotationItem::updateOrCreate([
                                            'quotation_id' => $obj->id,
                                            'item_code' => @$item['ItemCode'],
                                            'line_num' => $fields['line_num']
                                        ],
                                        $fields
                                    );

                            array_push($item_codes, @$item['ItemCode']);

                            if(!is_null(@$value['BaseEntry'])){
                                $obj->base_entry = @$value['BaseEntry'];
                                $obj->save();
                            }
                        }

                        QuotationItem::where('quotation_id', $obj->id)->whereNotIn('item_code', $item_codes)->delete();
                    }
                }
            }
        }
    }

    // Cancel Specific Quotations Data
    public function cancelSpecificQuotation($id, $doc_entry){

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
                   
                    $quotation = Quotation::find($id);
                    if(!is_null($quotation)){
                        $quotation->document_status = "Cancelled";
                        $quotation->status = "Cancelled";
                        $quotation->cancelled = "Yes";
                        $quotation->cancelled_by = Auth::id();
                        $quotation->cancel_date = date('Y-m-d');
                        $quotation->save();
                    }

                    return array(
                                'status' => true,
                                'data' => []
                            );
                }

            } catch (\Exception $e) {
                return array(
                                'status' => false,
                                'data' => []
                            );
            }

        }

        return $response;
    }

    // Store Specific Quotations Data
    public function addComments($doc_entry = false)
    {
        if($doc_entry){
            $url = '/b1s/v1/Quotations('.$doc_entry.')';
            $response = $this->getQuotationData($url);            
            if($response['status']){
                $value = $response['data'];
                if(!empty($value)){
                    $quotation = Quotation::where('doc_entry',$doc_entry)->first();
                    $quotation->comments = $value['Comments'];
                    $quotation->save();
                }
            }
        }
    }
}
