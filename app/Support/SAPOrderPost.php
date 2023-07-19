<?php

namespace App\Support;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use App\Support\SAPAuthentication;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\LocalOrder;
use Log;
use Mail;
use App\Models\Customer;
use App\Models\User;
use App\Models\CustomerGroup;
use App\Models\SapConnection;

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

                        'sap_connection_id' => $this->sap_connection_id,
                        'real_sap_connection_id' => $this->real_sap_connection_id,
                        'comments' => $data['Comments'],
                    );
            $obj = Quotation::updateOrCreate([
                                    'doc_entry' => $data['DocEntry'],
                                    'sap_connection_id' => $this->sap_connection_id,
                                    'comments' => $data['Comments'],
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

                        'line_status' => @$item['LineStatus'],
                        'u_itemstat' => @$item['U_ITEMSTAT'],

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
                $order->save();

                $this->pushOrderDetailsInDatabase($data);
                $this->updateNumAtCardInOrder($data['DocEntry']);

                // Sales Specialist Email
                if($order->sales_specialist_id != ""){
                    $sales_person_email = $order->sales_specialist->email;
                    $sales_name = $order->sales_specialist->sales_specialist_name;
                    $customer_name = $order->customer;
                    $sales_link = route('orders.show', @$order->quotation->id);
                    //Log::info(print_r($sales_link,true));
                    Mail::send('emails.order_placed', array('link'=>$sales_link, 'customer'=>@$customer_name->first_name." ".$customer_name->last_name), function($message) use($sales_person_email,$sales_name) {
                        $message->from('orders@northtrend.com', $sales_name);
                        $message->to($sales_person_email, $sales_person_email)
                                //->cc('itsupport@northtrend.com')
                                ->subject('Order Confirmation');
                    });
                }

                $emails = [];
                $customer_mails = [];
                $quotation = Quotation::where('doc_entry',$data['DocEntry'])->first();
                $user = Customer::where('card_code',$data['CardCode'])->where('sap_connection_id',$quotation->sap_connection_id)->get();

                foreach ($user as $key => $value) {
                    $group = CustomerGroup::where('code',@$value->group_code)->where('sap_connection_id',@$value->sap_connection_id)->first();

                    $link = route('orders.show', @$quotation->id);

                    if($value->sap_connection_id == 1){
                        $from_name = 'AP BLUE WHALE CORP';
                    }else if($value->sap_connection_id == 2){
                        $from_name = 'NORTH TREND MARKETING CORP';
                    }else if($value->sap_connection_id == 3){
                        $from_name = 'PHILCREST MARKETING CORP';
                    }else if($value->sap_connection_id == 5){
                        $from_name = 'SOLID TREND TRADE SALES INC.';
                    }

                    $user_mail = User::where('u_card_code',$value->u_card_code)->first();
                    //Log::info(print_r($user_mail->email,true));

                    if(@$user_mail->email != ""){
                        $mail_array = explode("; ", @$user_mail->email);
                        foreach($mail_array as $email){
                            if($email != 'COD ACCT'){
                                // Log::info("customer mail");
                                //Log::info(print_r($this->sap_connection_id));
                                Mail::send('emails.order_placed', array('link'=>$link, 'customer'=>@$user_mail->first_name." ".$user_mail->last_name), function($message) use($email,$from_name) {
                                    $message->from('orders@northtrend.com', $from_name);
                                    $message->to($email, $email)
                                            //->cc('itsupport@northtrend.com')
                                            ->subject('Order Confirmation');
                                });
                            }
                        }
                    }

                    // if($value->email != ""){
                    //     $customer_mails = explode("; ", @$value->email);
                    //     foreach($customer_mails as $email){
                    //         if($email != 'COD ACCT'){
                    //             Log::info("customer mail");
                    //             Mail::send('emails.order_placed', array('link'=>$link, 'customer'=>$value->card_name), function($message) use($email,$from_name) {
                    //                 $message->from('orders@northtrend.com', $from_name);
                    //                 $message->to($email, $email)
                    //                         ->cc('itsupport@northtrend.com')
                    //                         ->subject('Order Confirmation');
                    //             });
                    //         }
                    //     }
                    // }
                    
                    if(@$group->emails == null || @$group->emails == ""){
                        // Mail::send('emails.user_order_placed', array('link'=>$link, 'customer'=>$value->card_name), function($message) use($user,$from_name) {
                        //     $message->from('orders@northtrend.com', $from_name);
                        //     $message->to('mt@mailinator.com', 'orders@northtrend.com')
                        //             ->subject('Order Confirmation');
                        // });
                    }else{
                       $emails = explode("; ", @$group->emails);                    
                        foreach($emails as $email){
                            if($email != 'COD ACCT'){
                                Mail::send('emails.user_order_placed', array('link'=>$link, 'customer'=>@$value->card_name), function($message) use($email,$from_name) {
                                    $message->from('orders@northtrend.com', $from_name);
                                    $message->to($email, $email)
                                            ->subject('Order Confirmation');
                                });
                            }
                        }
                    }
                }
                /*$group = CustomerGroup::where('code',@$user->group_code)->where('sap_connection_id',@$user->sap_connection_id)->first();
                $emails = explode("; ", @$group->emails);
                $customer_mails = explode("; ", @$user->email);

                $link = route('orders.show', @$quotation->id);

                if($user->sap_connection_id == 1){
                    $from_name = 'AP BLUE WHALE CORP';
                }else if($user->sap_connection_id == 2){
                    $from_name = 'NORTH TREND MARKETING CORP';
                }else if($user->sap_connection_id == 3){
                    $from_name = 'PHILCREST MARKETING CORP';
                }else if($user->sap_connection_id == 5){
                    $from_name = 'SOLID TREND TRADE SALES INC.';
                }
                foreach($customer_mails as $email){
                    Log::info(print_r("here",true));
                    Mail::send('emails.order_placed', array('link'=>$link, 'customer'=>$user->card_name), function($message) use($email,$from_name) {
                        $message->from('orders@northtrend.com', $from_name);
                        $message->to($email, $email)
                                ->subject('Order Confirmation');
                    });
                }
                if(@$group->emails == null || @$group->emails == ""){
                    Log::info(print_r("heresfd",true));
                    Mail::send('emails.user_order_placed', array('link'=>$link, 'customer'=>$user->card_name), function($message) use($user,$from_name) {
                        $message->from('orders@northtrend.com', $from_name);
                        $message->to('mt@mailinator.com', 'orders@northtrend.com')
                                ->subject('Order Confirmation');
                    });
                }else{
                   
                    foreach($emails as $email){
                        Mail::send('emails.user_order_placed', array('link'=>$link, 'customer'=>@$user->card_name), function($message) use($email,$from_name) {
                            $message->from('orders@northtrend.com', $from_name);
                            $message->to($email, $email)
                                    ->subject('Order Confirmation');
                        });
                    }
                } */   

            } else {
                $order->confirmation_status = 'ERR';
                $order->message = $data;
                $order->save();
            }
            
        }

        return $response;
    }

    public function madeSapData($id){

        $response = [];
        $order = LocalOrder::where('id', $id)->with(['sales_specialist', 'customer', 'address', 'items.product'])->first();

        $response['CardCode'] = @$order->customer->card_code;
        $response['CardName'] = @$order->customer->card_name;
        $response['DocDueDate'] = @$order->due_date;
        $response['DocCurrency'] = @$order->customer->currency; //previous [PHP] 
        $response['Comments'] = @$order->remarks;

        if(strtolower(@$order->address->street) == strtolower(@$order->customer->card_name)){
            $response['Address'] = @$order->address->street;
        }else{
            $response['Address'] = '';
            if(!empty(@$order->address->street)){
                $response['Address'] .= @$order->address->street;
            }
        }
                
        if(!empty(@$order->address->city)){
            $response['Address'] .= ", ".@$order->address->city;
        }
        if(!empty(@$order->address->state)){
            $response['Address'] .= ", ".@$order->address->state;
        }
        if(!empty(@$order->address->zip_code)){
            $response['Address'] .= ", ".@$order->address->zip_code;
        }
        if(!empty(@$order->address->country)){
            $response['Address'] .= ", ".@$order->address->country;
        }

        if(@$order->sales_specialist->sales_employee_code && @$order->sales_specialist->is_active){
            $response['SalesPersonCode'] = @$order->sales_specialist->sales_employee_code;
        }

        $response['DocumentLines'] = [];
        
        $item_currency = [];
        $cust_price_list =  @$order->customer->price_list_num - 1; //$item_prices json decode starts with index zero
        foreach($order->items as $item){
            $item_currency = json_decode(@$item->product->item_prices);
            $temp = array(
                'ItemCode' => @$item->product->item_code,
                'ItemDescription' => @$item->product->item_name,
                'Quantity' => @$item->quantity,
                'Price' => @$item->price,
                'Currency' => $item_currency[$cust_price_list]->Currency,
                'UnitPrice' => @$item->price,
                'ShipDate' => @$order->due_date,
                'WarehouseCode' => '01',
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
                            //'NumAtCard' => $num_at_card,
                            'U_OMSNo' => $doc_entry,
                        );

            $response = $this->requestSapApi('/b1s/v1/Quotations('.$doc_entry.')', "PATCH", $body);

            $status = $response['status'];
            $data = $response['data'];
            if($data == '204'){
                //$quotation->num_at_card = $num_at_card;
                $quotation->u_omsno = $doc_entry;
                //$quotation->comments = @$data['Comments'];
                $quotation->save();
            }
        }

        return $response;
    }
}
