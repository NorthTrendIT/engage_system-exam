<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\LocalOrder;
use App\Models\CustomerPromotion;
use App\Models\SapConnection;
use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationConnection;
use App\Models\CustomerPromotionProductDelivery;

use App\Support\SAPOrders;
use App\Support\SAPInvoices;
use App\Support\SAPQuotations;
use App\Support\SAPCreditNote;
use App\Support\SAPOrderPost;

use App\Jobs\SyncOrders;
use App\Jobs\SyncInvoices;
use App\Jobs\SyncQuotations;
use App\Jobs\SyncCreditNote;
use App\Jobs\SAPAllOrderPost;
use App\Jobs\SAPAllCustomerPromotionPost;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\InvoiceItem;
use App\Models\SapConnectionApiFieldValue;
use Mail;
use DataTables;
use Illuminate\Support\Facades\Auth;
use OneSignal;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;
use App\Models\TerritorySalesSpecialist;
use App\Models\CustomersSalesSpecialist;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    public function __construct(){

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = collect();
        if(in_array(userrole(), [1, 10, 11])){
            $company = SapConnection::all();
        }
        $approvalStatus = LocalOrder::getApproval();
        return view('orders.index', compact('company', 'approvalStatus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $total = 0;
        $data = Quotation::where('id', $id);

        $branchIds = Auth::user()->branch;
        if(isset($branchIds) && !empty($branchIds)){
            $branch_ids = Auth::user()->customerBranch->pluck('id');
            $data->whereHas('customer.group', function($q) use ($branch_ids) {
                $q->whereIn('id', $branch_ids);
            });
        }

        if(userrole() == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }elseif(userrole() == 14){

            $territory = TerritorySalesSpecialist::where('user_id', userid())->with('territory:id,territory_id')->get();
            $sapConnections = TerritorySalesSpecialist::where('user_id', userid())->groupBy('sap_connection_id')->pluck('sap_connection_id')->toArray();
            $territoryIds= [];
            foreach($territory as $id){
                $territoryIds[] = $id->territory->territory_id;
            }

            $territoryIds = (@$territoryIds)? $territoryIds : [-3];
            $sapConnections = (@$sapConnections)? $sapConnections : [-3];

            $data->whereHas('customer', function($q) use($territoryIds, $sapConnections){
                $q->whereIn('real_sap_connection_id', $sapConnections);
                $q->whereIn('territory', $territoryIds);
            });
            
            // $data->whereHas('local_order', function($q){
            //     $q->where('sales_specialist_id', userid());
            // });
        }elseif(!is_null(Auth::user()->created_by)){
            $customers = Auth::user()->created_by_user->get_multi_customer_details();
            $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }
        // }elseif(userrole() != 1){
        //     return abort(404);
        // }

        $data = $data->firstOrFail();  
         
        $invoiceDetails = [];
        $line_stat = [];
        $Weight = 0;
        $Volume = 0;
        $currency_symbol = '';
        foreach($data->items as $key=>$value){
            if($value->product1->sap_connection_id === @$data->customer->real_sap_connection_id){
                $currency_symbol = get_product_customer_currency(@$value->product1->item_prices, $data->customer->price_list_num);
            }
            $invoiceDetails[$key]['key'] = $key + 1;
            $invoiceDetails[$key]['product'] = @$value->product1->item_name."(Code:".@$value->product1->item_code.")";
            $invoiceDetails[$key]['key'] = @$data->order->id;
            $invoiceDetails[$key]['unit'] = @$value->product1->sales_unit;
            $invoiceDetails[$key]['order_quantity'] = number_format(@$value->quantity);
            $invoiceDetails[$key]['item_code'] = @$value->item_code;

            $invoice = @$data->order->invoice1;
            $invoiceIds = [];
            if(!empty($invoice)){
                foreach($invoice as $val){
                    $invoiceIds[] = @$val->id;
                }
            }

            $quantityDetails[] = InvoiceItem::whereIn('invoice_id',$invoiceIds)->where('item_code',$value->item_code)->sum('quantity');
            
            $invoiceDetails[$key]['serverd_quantity'] = $quantityDetails[$key];
            $invoiceDetails[$key]['price'] = number_format_value(@$value->price);
            $invoiceDetails[$key]['price_after_vat'] = $currency_symbol.number_format_value($value->price_after_vat);
            $invoiceDetails[$key]['amount'] = '₱'. number_format_value(round($value->gross_total,1));

            $Weight = $Weight + (@$value->quantity * @$value->product1->sales_unit_weight);
            $Volume = $Volume + (@$value->quantity * @$value->product1->sales_unit_volume);

            $invoiceDetails[$key]['orderd_weight'] = number_format_value(@$value->quantity * @$value->product1->sales_unit_weight);

            $invoiceDetails[$key]['served_weight'] = number_format_value(@$quantityDetails[$key] * @$value->product1->sales_unit_weight);
            
            if(@$quantityDetails[$key] == 0){
                $status1 = 'Unserved';
            }else if(@$quantityDetails[$key] > 0 && @$value->quantity > $quantityDetails[$key]){
                $status1 = 'Partial Served';
            }else if(@$quantityDetails[$key] == @$value->quantity){
                $status1 = 'Fully Served';
            }else if(@$quantityDetails[$key] > @$value->quantity){
                $status1 = 'Over Served';
            }

            $inv_item = @$value->item_code;
            if(@$data->order[$key]->line_status == 'bost_Close'){
                $remarks = 'Served';
            }else if($value->line_status == 'bost_Open'){
                if(@$data->order[$key]->line_status != 'NA'){
                  $value = SapConnectionApiFieldValue::where('key',@$data->order[$key]->line_status)->first();
                  $remarks = @$data->order[$key]->line_status;
                }else{
                  $remarks = '-';
                }
            }else{
                $remarks = '-';
            }

            $num = InvoiceItem::with('invoice')->whereIn('invoice_id',$invoiceIds)->where('item_code', $inv_item)->pluck('invoice_id');
            
            $num1 = [];
            $invoice_num = Invoice::whereIn('id',$num)->pluck('doc_num')->implode(',');
           
            $invoiceDetails[$key]['line_status'] = @$status1;
            $invoiceDetails[$key]['id'] = @$num[0];
            $invoiceDetails[$key]['line_remarks'] = @$remarks;
            $invoiceDetails[$key]['invoice_num'] = @$invoice_num;

            if($data->order_type == 'Promotion'){
                $invoiceDetails[$key]['promotion'] = '-';

            }            

        }
        $orderRemarks = LocalOrder::where('doc_entry',@$data->doc_entry)->first();
        
        // $line_stat   = array_unique(array_column($invoiceDetails, 'line_status'));
        // $line_status = getOrderStatusV2($line_stat);

        return view('orders.order_view', compact('data','orderRemarks','invoiceDetails','Weight','Volume'));
    }

    public function showApproval($id){
        $data = LocalOrder::where('id', $id);

        $branchIds = Auth::user()->branch;
        if(isset($branchIds) && !empty($branchIds)){
            $branch_ids = Auth::user()->customerBranch->pluck('id');
            $data->whereHas('customer.group', function($q) use ($branch_ids) {
                $q->whereIn('id', $branch_ids);
            });
        }

        if(userrole() == 4){
            $data->whereHas('customer', function($q){
                $customers = Auth::user()->get_multi_customer_details();
                $q->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                $q->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
            });
        }elseif(userrole() == 14){

            $territory = TerritorySalesSpecialist::where('user_id', userid())->with('territory:id,territory_id')->get();
            $sapConnections = TerritorySalesSpecialist::where('user_id', userid())->groupBy('sap_connection_id')->pluck('sap_connection_id')->toArray();
            $territoryIds= [];
            foreach($territory as $id){
                $territoryIds[] = $id->territory->territory_id;
            }

            $territoryIds = (@$territoryIds)? $territoryIds : [-3];
            $sapConnections = (@$sapConnections)? $sapConnections : [-3];

            $data->whereHas('customer', function($q) use($territoryIds, $sapConnections){
                $q->whereIn('real_sap_connection_id', $sapConnections);
                $q->whereIn('territory', $territoryIds);
            });

            // $data->where('sales_specialist_id', userid());
        }elseif(!is_null(Auth::user()->created_by)){
            $data->whereHas('customer', function($q){
                $customers = Auth::user()->created_by_user->get_multi_customer_details();
                $q->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                $q->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
            });
        }
        // }elseif(userrole() != 1){
        //     return abort(404);
        // }

        $data = $data->firstOrFail();  
         
        $invoiceDetails = [];
        $line_stat = [];
        $Weight = 0;
        $Volume = 0;
        $currency_symbol = '';
        foreach($data->items as $key=>$value){
            if($value->product->sap_connection_id === @$data->customer->real_sap_connection_id){
                $currency_symbol = get_product_customer_currency(@$value->product->item_prices, $data->customer->price_list_num);
            }
            $invoiceDetails[$key]['key'] = $key + 1;
            $invoiceDetails[$key]['product'] = @$value->product->item_name."(Code:".@$value->product->item_code.")";
            $invoiceDetails[$key]['key'] = @$data->order->id;
            $invoiceDetails[$key]['unit'] = @$value->product->sales_unit;
            $invoiceDetails[$key]['order_quantity'] = number_format(@$value->quantity);
            $invoiceDetails[$key]['item_code'] = @$value->item_code;

            $invoice = @$data->order->invoice1;
            $invoiceIds = [];
            if(!empty($invoice)){
                foreach($invoice as $val){
                    $invoiceIds[] = @$val->id;
                }
            }

            $quantityDetails[] = InvoiceItem::whereIn('invoice_id',$invoiceIds)->where('item_code',$value->item_code)->sum('quantity');
            
            $invoiceDetails[$key]['serverd_quantity'] = $quantityDetails[$key];
            $invoiceDetails[$key]['price'] = number_format_value(@$value->price);
            $invoiceDetails[$key]['price_after_vat'] = $currency_symbol.number_format_value($value->price_after_vat);
            $invoiceDetails[$key]['amount'] = '₱'. number_format_value(round($value->gross_total,1));

            $Weight = $Weight + (@$value->quantity * @$value->product->sales_unit_weight);
            $Volume = $Volume + (@$value->quantity * @$value->product->sales_unit_volume);

            $invoiceDetails[$key]['orderd_weight'] = number_format_value(@$value->quantity * @$value->product->sales_unit_weight);

            $invoiceDetails[$key]['served_weight'] = number_format_value(@$quantityDetails[$key] * @$value->product->sales_unit_weight);
            
            if(@$quantityDetails[$key] == 0){
                $status1 = 'Unserved';
            }else if(@$quantityDetails[$key] > 0 && @$value->quantity > $quantityDetails[$key]){
                $status1 = 'Partial Served';
            }else if(@$quantityDetails[$key] == @$value->quantity){
                $status1 = 'Fully Served';
            }else if(@$quantityDetails[$key] > @$value->quantity){
                $status1 = 'Over Served';
            }

            $inv_item = @$value->item_code;
            if(@$data->order[$key]->line_status == 'bost_Close'){
                $remarks = 'Served';
            }else if($value->line_status == 'bost_Open'){
                if(@$data->order[$key]->line_status != 'NA'){
                  $value = SapConnectionApiFieldValue::where('key',@$data->order[$key]->line_status)->first();
                  $remarks = @$data->order[$key]->line_status;
                }else{
                  $remarks = '-';
                }
            }else{
                $remarks = '-';
            }

            $num = InvoiceItem::with('invoice')->whereIn('invoice_id',$invoiceIds)->where('item_code', $inv_item)->pluck('invoice_id');
            
            $num1 = [];
            $invoice_num = Invoice::whereIn('id',$num)->pluck('doc_num')->implode(',');
           
            $invoiceDetails[$key]['line_status'] = @$status1;
            $invoiceDetails[$key]['id'] = @$num[0];
            $invoiceDetails[$key]['line_remarks'] = @$remarks;
            $invoiceDetails[$key]['invoice_num'] = @$invoice_num;

            if($data->order_type == 'Promotion'){
                $invoiceDetails[$key]['promotion'] = '-';

            }            

        }
        $orderRemarks = LocalOrder::where('doc_entry',@$data->doc_entry)->first();

        return view('orders.order_view_approval', compact('data','orderRemarks','invoiceDetails','Weight','Volume'));
    }

    public function confirmationOrder(Request $request){
        $order = LocalOrder::where('id', $request->id)
                             ->whereIn('approval', ['Pending', 'Rejected'])
                             ->doesntHave('quotation')
                             ->firstOrFail();

        $response = ['status' => false, 'message' => 'Something went wrong!'];
        if($request->approval === "Approve"){
            $order->approval = "Approved";
            $response = $this->approvedOrderPushToSap($order->id);
        }elseif($request->approval === "Reject"){
            $order->approval = "Rejected";
            $response = ['status' => true, 'message' => 'Order was rejected!'];
        }
        
        $order->disapproval_remarks = $request->reason;
        $order->approved_at = Carbon::now();
        $order->approved_by = Auth::id();
        $order->save();

        return $response;
    }

    public function approvedOrderPushToSap($id){

        $order = LocalOrder::where('id', $id)->with(['sales_specialist', 'customer', 'address', 'items.product'])->first();
        $response = [];

        try{
            $sap_connection = SapConnection::find(@$order->customer->sap_connection_id);                
            if(!is_null($sap_connection)){                   
                $sap = new SAPOrderPost($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $sap_connection->id);
                if($id){                        
                    $sap_response = $sap->pushOrder($order->id);
                    if($sap_response['status']){
                        $response = ['status' => true, 'message' => 'Order placed successfully!'];
                    }
                    $response = ['status' => true, 'message' => 'Order placed successfully!'];
                }
            }
        } catch (\Exception $e) {
            report($e);
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }

        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function syncOrders(Request $request){
        try {

            if($request->filter_company != ""){
                $sap_connections = SapConnection::where('id', $request->filter_company)->get();
            }else{
                $sap_connections = SapConnection::where('id', '!=', 5)->get();
            }
            
            foreach ($sap_connections as $value) {

                $order_log_id = add_sap_log([
                                        'ip_address' => userip(),
                                        'activity_id' => 34,
                                        'user_id' => userid(),
                                        'data' => null,
                                        'type' => "S",
                                        'status' => "in progress",
                                        'sap_connection_id' => $value->id,
                                    ]);
                $quotation_log_id = add_sap_log([
                                        'ip_address' => userip(),
                                        'activity_id' => 35,
                                        'user_id' => userid(),
                                        'data' => null,
                                        'type' => "S",
                                        'status' => "in progress",
                                        'sap_connection_id' => $value->id,
                                    ]);
                $invoice_log_id = add_sap_log([
                                        'ip_address' => userip(),
                                        'activity_id' => 36,
                                        'user_id' => userid(),
                                        'data' => null,
                                        'type' => "S",
                                        'status' => "in progress",
                                        'sap_connection_id' => $value->id,
                                    ]);

                $credit_note_log_id = add_sap_log([
                                        'ip_address' => userip(),
                                        'activity_id' => 59,
                                        'user_id' => userid(),
                                        'data' => null,
                                        'type' => "S",
                                        'status' => "in progress",
                                        'sap_connection_id' => $value->id,
                                    ]);

                SyncQuotations::dispatch($value->db_name, $value->user_name , $value->password, $quotation_log_id);
                SyncOrders::dispatch($value->db_name, $value->user_name , $value->password, $order_log_id);
                SyncInvoices::dispatch($value->db_name, $value->user_name , $value->password, $invoice_log_id);
                SyncCreditNote::dispatch($value->db_name, $value->user_name , $value->password, $credit_note_log_id);
            }

            $response = ['status' => true, 'message' => 'Sync Orders successfully !'];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
        }
        return $response;
    }

    public function syncSpecificOrder(Request $request){

        $response = ['status' => false, 'message' => 'Something went wrong !'];
        if(@$request->id){

            $quotation = Quotation::find($request->id);
            if(!is_null($quotation) && !is_null(@$quotation->sap_connection)){
                try {

                    $sap_connection = @$quotation->sap_connection;

                    // Sync Quotation Data
                    $sap_quotations = new SAPQuotations($sap_connection->db_name, $sap_connection->user_name, $sap_connection->password);
                    $sap_quotations->addSpecificQuotationsDataInDatabase(@$quotation->doc_entry);
                    
                    // Sync Order Data
                    $sap_orders = new SAPOrders($sap_connection->db_name, $sap_connection->user_name, $sap_connection->password);
                    $sap_orders->addSpecificOrdersDataInDatabase(@$quotation->doc_entry);

                    // Sync Invoice Data
                    $sap_invoices = new SAPInvoices($sap_connection->db_name, $sap_connection->user_name, $sap_connection->password);
                    $sap_invoices->addSpecificInvoicesDataInDatabase(@$quotation->doc_entry);

                    $response = ['status' => true, 'message' => 'Sync order details successfully !'];
                } catch (\Exception $e) {
                    dd($e);
                    $response = ['status' => false, 'message' => 'Something went wrong !'];
                }
            }
        }
        return $response;
    }

    public function getAll(Request $request){
        $data = LocalOrder::query();
        
        $branchIds = Auth::user()->branch;
        if(isset($branchIds) && !empty($branchIds)){
            $branch_ids = Auth::user()->customerBranch->pluck('id');
            $data->whereHas('customer.group', function($q) use ($branch_ids) {
                $q->whereIn('id', $branch_ids);
            });
        }

        // if($request->engage_transaction != 0){
        //     $data->whereHas('quotation', function($q){
        //         $q->whereNotNull('u_omsno');
        //     });
        // }

        if(userrole() == 4){
            $data->whereHas('customer', function($q){
                $cus = explode(',', Auth::user()->multi_customer_id);
                $q->whereIn('id', $cus);
            });
        }elseif(userrole() == 14){ //sales personnel
            if($request->orderAll === "true"){
                if($request->filter_customer != "")
                {
                    $data->whereHas('customer', function($q) use ($request) {
                        $q->where('id',$request->filter_customer);
                    });

                    $request->filter_customer = '';
                }else{
                    $data->where('sales_specialist_id', 'dummy');
                }
            }else{
                $data->where('sales_specialist_id', userid());
            }

            // $data->whereHas('customer', function($q) use($territoryIds, $sapConnections){
            //     // $cus = CustomersSalesSpecialist::where(['ss_id' => Auth::user()->id])->pluck('customer_id')->toArray();
            //     $q->whereIn('real_sap_connection_id', $sapConnections);
            //     $q->whereIn('territory', $territoryIds);
            // });

        }elseif(!in_array(userrole(), [1,10,11] )){
            if (!is_null(@Auth::user()->created_by)) {
                $customers = @Auth::user()->created_by_user->get_multi_customer_details();
                $data->whereHas('customer', function($q) use ($customers){
                    $q->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                });
                $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
            
                // $data->where('card_code', @Auth::user()->created_by_user->customer->card_code);
            } else {
                return DataTables::of(collect())->make(true);
            }
        }

        if($request->filter_brand != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where(function($que) use ($request) {
                        $que->whereHas('product_groups', function($q2) use ($request){
                            $q2->where('product_group_id', $request->filter_brand);
                        });
                    });
                });
            });
        }

        if($request->filter_class != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('u_classification', $request->filter_class);
                });
            });
        }

        if($request->filter_sales_specialist != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where(function($que) use ($request) {
                        $que->whereHas('sales_specialist', function($q2) use ($request){
                            $q2->where('id', $request->filter_sales_specialist);
                        });
                    });
                });
            });
        }

        if($request->filter_market_sector != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('u_sector', $request->filter_market_sector);
                });
            });
        }

        if($request->filter_group != ""){
            $data->whereHas('customer.group', function($q) use ($request){
                $q->where('code',$request->filter_group);
            });
        }
        
        $territory = @$request->filter_territory;
        if(!empty($territory) && $territory[0] !== null){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer.territories', function($q) use ($request) {
                    $q->whereIn('id', $request->filter_territory);
                });
            });
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('doc_num','LIKE',"%".$request->filter_search."%");
                $q->orwhere('doc_entry','LIKE',"%".$request->filter_search."%");
                $q->orWhereHas('customer', function($c) use ($request){
                    $c->where('card_name','LIKE',"%".$request->filter_search."%");
                });
                $q->orWhereHas('sales_specialist', function($c) use ($request){
                    $c->where('sales_specialist_name','LIKE',"%".$request->filter_search."%");
                });
            });
        }

        if($request->filter_customer != ""){
            $data->whereHas('customer', function($q) use ($request) {
                $q->where('card_code',$request->filter_customer);
            });
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }


        if($request->filter_approval != ""){
            $data->where('approval',$request->filter_approval);
            if($request->filter_approval == 'Pending'){
                $data->whereDoesntHave('quotation')
                     ->whereDate('created_at', '>=', '2025-02-26');
            }
        }
        
        $status = @$request->filter_status;
        if(!empty($status) && $status[0] !== null){
            $data->whereHas('quotation', function($query) use ($status){
                $selected_status = [];

                if(in_array("PN",$status)){
                    array_push($selected_status, 'Pending');
                }
                if(in_array("OP",$status)){
                    array_push($selected_status, 'On Process');
                }
                if(in_array("CL", $status)){
                    array_push($selected_status, 'Cancelled');
                }

                if(in_array("CM", $status)){
                    array_push($selected_status, 'Completed');
                }

                if(in_array("PS", $status)){
                    array_push($selected_status, 'Partially Served');
                }

                $query->whereIn('status', $selected_status);

                if(in_array("DL", $status)){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', 'DL');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }

                if(in_array("IN", $status)){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', 'IN');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }
                if(in_array("CF", $status)){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', 'CF');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }
                if(in_array("FD", $status)){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', '!=','CM')->where('u_sostat', '!=','DL');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }
                
            });
        }
        
        if($request->filter_order_type != ""){
            $data->whereHas('quotation', function($query) use ($request){   
                if($request->filter_order_type == "Standard"){
                    $query->whereNull('customer_promotion_id');
                }elseif($request->filter_order_type == "Promotion"){
                    $query->whereNotNull('customer_promotion_id');
                }
            });
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('created_at', '>=' , $start);
            $data->whereDate('created_at', '<=' , $end);
        }

        $data->when(!isset($request->order), function ($q) {

            $q->orderBy('created_at', 'desc');
            // $q->whereHas('quotation', function($query){
            //     $query->orderBy('doc_date', 'desc');
            //     $query->orderBy('doc_time', 'desc');
            // });
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('name', function($row) {
                                return  @$row->customer->card_name ?? @$row->card_name ?? "-";
                            })
                            ->addColumn('status', function($row) use ($status) {
                               
                                return getOrderStatusBtnHtml(@$row->quotation->status);

                                // if(@$status[0] == "PS"){
                                //     return getOrderStatusBtnHtml(getOrderStatusArray('PS'));
                                // }else{
                                //     return getOrderStatusBtnHtml(getOrderStatusByQuotation($row));
                                // }
                                
                            })
                            ->addColumn('order_approval', function($row) {
                                return (@$row->quotation) ? 'Approved' : $row->approval;
                            })
                            ->addColumn('approval_duration', function($row) {
                                if($row->approved_at){
                                    $approvedAt = Carbon::parse($row->approved_at);
                                    $currentDateTime = Carbon::parse($row->created_at);

                                    $days = $currentDateTime->diffInDays($approvedAt, false);
                                    $noun = ($days > 1) ? 'days' : 'day';

                                    // Calculate the duration
                                    $duration = $currentDateTime->diff($approvedAt);

                                    // Format the duration as "1 day 02:52:01"
                                    $formattedDuration = $duration->format('%d '.$noun.' %H:%I:%S');
                                }else{
                                    $formattedDuration = '-';
                                }
                                return $formattedDuration;
                            })
                            ->addColumn('doc_entry', function($row) {
                                $route = ($row->quotation) ? [route('orders.show',$row->quotation->id), $row->quotation->doc_entry] : [route('orders.approval.show', $row->id), $row->id];
                                return '<a href="' . $route[0]. '" title="View details">'.$route[1].'</a>';
                            })
                            ->addColumn('total', function($row) {
                                $total = (@$row->quotation) ? $row->quotation->doc_total : $row->items()->sum('total');
                                return '₱ '. number_format_value(@$total);
                            })
                            ->addColumn('created_by', function($row) {
                                if($row->placed_by == 'S'){
                                    return ($row->sales_specialist)? $row->sales_specialist->first_name.' '.$row->sales_specialist->last_name : '-';
                                } else {
                                    return "Customer";
                                }
                            })
                            ->addColumn('date', function($row) {
                                $created_date =  @$row->created_at;
                                $date = date('M d, Y',strtotime($created_date));
                                $time = $row->doc_time ? date('H:i A',strtotime($created_date)) : "";
                                
                                return $date;
                            })
                            ->addColumn('due_date', function($row) {
                                return date('M d, Y',strtotime(@$row->quotation->doc_due_date));
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->addColumn('order_type', function($row) {
                                $type = "Standard";
                                if(!is_null($row->customer_promotion_id)){
                                    $type = "Promotion";
                                }

                                return $type;
                            })
                            ->addColumn('action', function($row){
                                $route = ($row->quotation) ? route('orders.show',$row->quotation->id) : route('orders.approval.show', $row->id);
                                $btn = '<a href="' .$route. '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm mr-10">
                                            <i class="fa fa-eye"></i>
                                        </a>';
                                if(userrole() == 1){
                                    $btn .= '<a href="javascript:;" data-url="'. route('orders.notify-customer').'" data-order="'.$row->id.'" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm m-5 notifyCustomer" title="Notify Customer">
                                            <i class="fa fa-bell"></i>
                                        </a>';
                                }
                                return $btn;
                            })
                            // ->orderColumn('name', function ($query, $order) {
                            //     //$query->orderBy('card_name', $order);

                            //     $query->select('quotations.*')->join('customers', 'quotations.card_code', '=', 'customers.card_code')
                            //         ->orderBy('customers.card_name', $order);
                            // })
                            // ->orderColumn('doc_entry', function ($query, $order) {
                            //     $query->orderBy('doc_entry', $order);
                            // })
                            // // ->orderColumn('status', function ($query, $order) {
                            // //     $query->local_order->orderBy('doc_entry', $order);
                            // // })
                            // ->orderColumn('total', function ($query, $order) {
                            //     $query->orderBy('doc_total', $order);
                            // })
                            ->orderColumn('date', function ($query, $order) {
                                $query->orderBy('created_at', $order);
                            })
                            // ->orderColumn('due_date', function ($query, $order) {
                            //     $query->orderBy('doc_due_date', $order);
                            // })
                            // ->orderColumn('company', function ($query, $order) {
                            //     $query->join('sap_connections', 'quotations.sap_connection_id', '=', 'sap_connections.id')->orderBy('sap_connections.company_name', $order);
                            // })
                            ->rawColumns(['action', 'status', 'order_type','doc_entry'])
                            ->make(true);
    }

    public function cancelOrder(Request $request){

        $response = ['status' => false, 'message' => 'Record not found!'];
        if(in_array(userrole(), [1, 4, 10, 14])){
            $quotation = Quotation::where('id', $request->id);

            if(userrole() == 4){
                $quotation->whereHas('local_order', function($q){
                    $q->where('customer_id', Auth::id());
                });
            }elseif(userrole() == 14){
                $quotation->whereHas('local_order', function($q){
                    $q->where('sales_specialist_id', Auth::id());
                });
            }

            $quotation = $quotation->first();
            if(!empty($quotation)){
                
                $sap_connection = @$quotation->sap_connection;

                $sap_quotations = new SAPQuotations(@$sap_connection->db_name, @$sap_connection->user_name, @$sap_connection->password);
                $response = $sap_quotations->cancelSpecificQuotation($quotation->id, $quotation->doc_entry);

                $emails = [];
                $customer_mails = [];
                $link = route('orders.show', @$quotation->id);
                $user = Customer::where('card_code',@$quotation->card_code)->first();
                $group = CustomerGroup::where('code',@$user->group_code)->where('sap_connection_id',@$user->sap_connection_id)->first();
                $emails = explode("; ", @$group->emails);
                $customer_mails = explode("; ", @$user->email);

                if($user->sap_connection_id == 1){
                    $from_name = 'AP BLUE WHALE CORP';
                }else if($user->sap_connection_id == 2){
                    $from_name = 'NORTH TREND MARKETING CORP';
                }else if($user->sap_connection_id == 3){
                    $from_name = 'PHILCREST MARKETING CORP';
                }else if($user->sap_connection_id == 5){
                    $from_name = 'SOLID TREND TRADE SALES INC.';
                }

                // foreach($customer_mails as $email){
                //     Mail::send('emails.cancel_order', array('link'=>$link, 'customer'=>$user->card_name,'order_no'=>@$quotation->u_omsno), function($message) use($email,$from_name,$quotation) {
                //         $message->from('orders@northtrend.com', $from_name);
                //         $message->to($email, $email)
                //                 ->subject('Order #'.$quotation->u_omsno.' -Cancel Order');
                //     });
                // }

                // if(@$group->emails == null || @$group->emails == ""){
                //     Mail::send('emails.user_cancel_order', array('link'=>$link, 'customer'=>$user->card_name,'order_no'=>@$quotation->u_omsno), function($message) use($quotation,$from_name) {
                //         $message->from('orders@northtrend.com', $from_name);
                //         $message->to('orders@northtrend.com', 'orders@northtrend.com')
                //                 ->subject('Order #'.@$quotation->u_omsno.' -Cancel Order');
                //     });
                // }else{
                //     foreach($emails as $email){

                //         Mail::send('emails.user_cancel_order', array('link'=>$link, 'customer'=>$user->card_name,'order_no'=>@$quotation->u_omsno), function($message) use($email,$from_name,$quotation) {
                //             $message->from('orders@northtrend.com', $from_name);
                //             $message->to($email, $email)
                //                     ->subject('Order #'.@$quotation->u_omsno.' -Cancel Order');
                //         });
                //     }
                // }
                

                if(@$response['status']){
                    $response = ['status' => true, 'message' => 'Order canceled successfully!'];
                }else{
                    $response = ['status' => false, 'message' => 'Something went wrong !'];
                }
            }else{
                $response = ['status' => false, 'message' => 'You are not authorize to cancel this order!'];
            }
        }
        return $response;
    }

    public function completeOrder(Request $request){

        if(!@$request->is_accept){
            return $response = ['status' => false, 'message' => 'Please accept the checkbox of mark the order as completed.'];
        }

        $response = ['status' => false, 'message' => 'Record not found!'];

        $quotation = Quotation::where('id', $request->id);
        if(userrole() == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $quotation->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $quotation->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }elseif(userrole() == 14){
            $quotation->whereHas('customer', function($q){
                $cus = CustomersSalesSpecialist::where(['ss_id' => Auth::user()->id])->pluck('customer_id')->toArray();
                $q->whereIn('id', $cus);
            });
        }elseif(userrole() != 1){
            if (!is_null(@Auth::user()->created_by)) {
                $customers = @Auth::user()->created_by_user->get_multi_customer_details();
                $quotation->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                $quotation->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
            } else {
                return abort(404);
            }
        }

        $quotation = $quotation->first();
        if(!empty($quotation)){
            if(@$quotation->order->invoice){

                $quotation->order->invoice->completed_date = date('Y-m-d H:i:s');
                $quotation->order->invoice->completed_remarks = @$request->remarks;
                $quotation->order->invoice->save();

                $response = ['status' => true, 'message' => 'Order completed successfully!'];
            }
        }
        return $response;
    }
    // Notify Customer
    public function notifyCustomer(Request $request){
        $q_id = $request->order_id;
        $quotation = Quotation::with('customer')->find($q_id);

        if(!empty($quotation) && !empty(@$quotation->customer->user)){
            $link = route('orders.show', $q_id);
            $user = @$quotation->customer->user;
            // return view('emails.order_update',array('link'=>$link, 'order_no'=>$quotation->doc_entry));

            // Send Mail.
            Mail::send('emails.order_update', array('link'=>$link, 'order_no'=>$quotation->doc_entry, 'status'=>getOrderStatusByQuotation($quotation)), function($message) use($user) {
                $message->to($user->email, $user->name)
                        ->subject('Order Status Update');
            });

            // Create Local Notification
            $notification = new Notification();
            $notification->type = 'OU';
            $notification->title = 'Order Status Updated';
            $notification->module = 'customer';
            $notification->sap_connection_id = $quotation->sap_connection_id;
            $notification->message = 'Your order <a href="'.$link.'"><b>#'.$quotation->doc_entry.'</b></a> status has been updated to <b>[STATUS]</b>.';
            $notification->user_id = @Auth::user()->id;
            $notification->save();

            if($notification->id){
                $connection = new NotificationConnection();
                $connection->notification_id = $notification->id;
                $connection->user_id = $user->id;
                $connection->record_id = $user->customer_id;
                $connection->save();
            }

            // Send One Signal Notification.
            $fields['filters'] = array(array("field" => "tag", "key" => "customer", "relation"=> "=", "value"=> ".$user->customer_id."));
            $message = $notification->title;

            $push = OneSignal::sendPush($fields, $message);
        }

        $response = ['status' => true, 'message' => 'Status update Email and notification has been send successfully!'];
        return $response;
    }

    /* Pending Orders */
    public function pendingOrder(){
        return view('orders.pending_orders');
    }

    public function pendingOrderView($id){
        $total = 0;
        $data = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items.product'])->where('id', $id)->firstOrFail();
        return view('orders.pending_order_view', compact('data', 'total'));
    }

    public function getAllPendingOrder(Request $request){

        $data = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items']);

        if(userrole() == 4){
            $cust_id = explode(',', Auth::user()->multi_customer_id);
            $data->whereIn('customer_id', $cust_id);
        }
        if(userrole() == 14){
            $data->where('sales_specialist_id', Auth::user()->id);
        }
        
        $data->where('confirmation_status', 'ERR');

        if($request->filter_search != ""){
            $data->whereHas('customer', function($q) use ($request) {
                $q->where('card_name','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('customer_name', function($row) {
                            return $row->customer->card_name ?? '-';
                        })
                        ->addColumn('status', function($row) {
                            if($row->confirmation_status == 'P'){
                                return "Pending";
                            }
                            if($row->confirmation_status == 'C'){
                                return "Confirm";
                            }
                            if($row->confirmation_status == 'ERR'){
                                return $row->message;
                            }
                        })
                        ->addColumn('date', function($row) {
                            return date('M d, Y',strtotime($row->created_at));
                        })
                        ->addColumn('due_date', function($row) {
                            return date('M d, Y',strtotime($row->due_date));
                        })
                        ->addColumn('created_by', function($row) {
                            if(!empty($row->sales_specialist_id)){
                                return $row->sales_specialist->sales_specialist_name ?? '-';
                            } else {
                                return "Customer";
                            }
                        })
                        ->orderColumn('due_date', function ($query, $order) {
                            $query->orderBy('due_date', $order);
                        })
                        ->orderColumn('date', function ($query, $order) {
                            $query->orderBy('created_at', $order);
                        })
                        ->orderColumn('confirmation_status', function ($query, $order) {
                            $query->orderBy('confirmation_status', $order);
                        })
                        ->addColumn('action', function($row) {
                            $btn = '<a href="' . route('orders.panding-orders.view',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                <i class="fa fa-eye"></i>
                                </a>';
                            $btn .= '<a href="javascript:;" data-id="'.$row->id.'" class="btn btn-bg-light btn-light-info btn-sm m-2 pushOrder">
                                Push
                            </a>';

                            $btn .= '<a href="javascript:;" data-id="'.$row->id.'" class="btn btn-bg-light btn-light-danger btn-sm m-2 deleteOrder">
                                Delete
                            </a>';

                            return $btn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }
    /* End Pending Orders */

    /* Pending Promotions */
    public function pendingPromotion(){
        return view('orders.pending_promotion');
    }

    public function pendingPromotionView($id){

        $data = CustomerPromotion::where('id',$id);

        if(Auth::id() != 1){
            $data->where('user_id',Auth::id());
        }

        $data = $data->firstOrFail();

        $sap_pushed = CustomerPromotionProductDelivery::has('customer_promotion_product')
                                                ->where('is_sap_pushed', false)
                                                ->whereHas('customer_promotion_product', function($q) use($id){
                                                    $q->where('customer_promotion_id', $id);
                                                })
                                                ->count();
        if($sap_pushed == 0){
            abort(404);
        }

        return view('orders.pending_promotion_view',compact('data'));
    }

    public function getAllPendingPromotion(Request $request){

        $data = collect([]);
        $not_pushed_promotion = CustomerPromotionProductDelivery::has('customer_promotion_product')
                                            ->where('is_sap_pushed', false)
                                            ->with('customer_promotion_product')
                                            ->whereHas('customer_promotion_product.customer_promotion', function($q){
                                                $q->where('status', 'approved');
                                            })
                                            ->get();

        if(count($not_pushed_promotion)){

            $not_pushed_promotion = array_map( function ( $ar ) {
                       return $ar['customer_promotion_id'];
                    }, array_column( $not_pushed_promotion->toArray(), 'customer_promotion_product' ) );

            if(!empty($not_pushed_promotion)){
                $data =  CustomerPromotion::whereIn('id', $not_pushed_promotion);

                if(Auth::id() != 1){
                    $data->where('customer_promotions.user_id',Auth::id());
                }

                if($request->filter_status != ""){
                    $data->where('customer_promotions.status',$request->filter_status);
                }

                if($request->filter_search != ""){
                    $data->where(function($query) use ($request) {

                        $query->whereHas('promotion',function($q) use ($request) {
                            $q->where('title','LIKE',"%".$request->filter_search."%");
                        });

                    });
                }

                $data->when(!isset($request->order), function ($q) {
                    $q->orderBy('customer_promotions.id', 'desc');
                });
            }
        }else{
             return DataTables::of($data)->make(true);
        }
        
        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('promotion', function($row) {
                                return @$row->promotion->title ?? "-";
                            })
                            ->addColumn('user', function($row) {
                                return @$row->user->sales_specialist_name ?? "-";
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('orders.pending-promotion.view',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                $btn .= '<a href="javascript:;" class="btn btn-sm btn-light-info btn-inline m-2 push-in-sap" data-id="'.$row->id.'">
                                  Push
                                </a>';

                                return $btn;
                            })
                            ->addColumn('created_at', function($row) {
                                return date('M d, Y',strtotime($row->created_at));
                            })
                            ->addColumn('status', function($row) {

                                $btn = "";
                                if($row->status == "approved"){
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline ">Approved</a>';
                                }else if($row->status == "pending"){
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-info btn-inline">Pending</a>';
                                }else if($row->status == "canceled"){
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-danger btn-inline ">Canceled</a>';
                                }

                                return $btn;
                            })
                            ->orderColumn('promotion', function ($query, $order) {
                                $query->select('customer_promotions.*')->join('promotions', 'customer_promotions.promotion_id', '=', 'promotions.id')
                                    ->orderBy('promotions.title', $order);
                            })
                            ->orderColumn('user', function ($query, $order) {
                                $query->select('customer_promotions.*')->join('users', 'customer_promotions.user_id', '=', 'users.id')
                                    ->orderBy('users.sales_specialist_name', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('status', $order);
                            })
                            ->orderColumn('created_at', function ($query, $order) {
                                $query->orderBy('created_at', $order);
                            })
                            ->rawColumns(['action','status','created_at','user'])
                            ->make(true);
    }
    /* End Pending Promotion */

    // Push Orders to SAP
    public function pushSingleOrder(Request $request){
        $order_id = $request->id;

        $data = LocalOrder::with('customer')->find($order_id);
        if(!empty($data)){
            $sap_connection = SapConnection::find(@$data->customer->sap_connection_id);
            if(!is_null($sap_connection)){
                // SAPAllOrderPost::dispatch($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $sap_connection->id, $order_id);

                $sap = new SAPOrderPost($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $sap_connection->id);

                $sap->pushOrder($order_id);
            }

            return $response = ['status' => true, 'message' => 'Order Placed Successfully!'];
        } else {
            return $response = ['status' => false, 'message' => 'Something went wrong!'];
        }
    }

    public function deletePushSingleOrder(Request $request){
        $order_id = $request->id;

        try{
            $data = LocalOrder::where('id',$order_id)->delete();        
            return $response = ['status' => true, 'message' => 'Order deleted Successfully!'];
        }catch(Exception $e){
            return $response = ['status' => false, 'message' => 'Something went wrong!'];
        }
    }

    public function pushAllOrder(Request $request){
        $data = LocalOrder::with(['customer'])->where('confirmation_status', 'ERR')->get();

        if(!empty($data)){
            foreach($data as $order){
                $sap_connection = SapConnection::find(@$order->customer->sap_connection_id);

                if(!is_null($sap_connection)){
                    SAPAllOrderPost::dispatch($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $sap_connection->id, @$order->id);
                }
            }
            return $response = ['status' => true, 'message' => 'All Order Placed Successfully!'];
        } else {
            return $response = ['status' => false, 'message' => 'Something went wrong!'];
        }
    }

    public function pushAllPromotion(Request $request){
        $data = collect([]);
        $not_pushed_promotion = CustomerPromotionProductDelivery::has('customer_promotion_product')
                                            ->where('is_sap_pushed', false)
                                            ->with('customer_promotion_product')
                                            ->whereHas('customer_promotion_product.customer_promotion', function($q){
                                                $q->where('status', 'approved');
                                            })
                                            ->get();

        if(!empty($not_pushed_promotion)){

            $not_pushed_promotion = array_map( function ( $ar ) {
                       return $ar['customer_promotion_id'];
                    }, array_column( $not_pushed_promotion->toArray(), 'customer_promotion_product' ) );

            if(!empty($not_pushed_promotion)){
                $data = CustomerPromotion::whereIn('id', $not_pushed_promotion)->get();
            }
        }

        if(!empty($data)){
            foreach($data as $item){
                $sap_connection = SapConnection::find(@$item->sap_connection_id);

                if(!is_null($sap_connection)){

                    foreach (@$item->products as $p) {
                        foreach (@$p->deliveries as $d) {
                            SAPAllCustomerPromotionPost::dispatch($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, @$d->id);
                        }
                    }
                }
            }
            return $response = ['status' => true, 'message' => 'All Promotion Pushed Successfully!'];
        } else {
            return $response = ['status' => false, 'message' => 'Something went wrong!'];
        }
    }

    public function getCustomer(Request $request){
        return app(CustomerPromotionController::class)->getCustomer($request);
    }


    public function export(Request $request){
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 36000);

        $startTime = microtime(true); // Start timing

        $filter = collect();
        if(@$request->data){
          $request = json_decode(base64_decode($request->data));
        }


        $data = LocalOrder::query();

        $branchIds = Auth::user()->branch;
        if(isset($branchIds) && !empty($branchIds)){
            $branch_ids = Auth::user()->customerBranch->pluck('id');
            $data->whereHas('customer.group', function($q) use ($branch_ids) {
                $q->whereIn('id', $branch_ids);
            });
        }

        // if($request->engage_transaction != 0){
        //     $data->whereHas('quotation', function($q){
        //         $q->whereNotNull('u_omsno');
        //     });
        // }

        if(userrole() == 4){
            $data->whereHas('customer', function($q){
                $cus = explode(',', Auth::user()->multi_customer_id);
                $q->whereIn('id', $cus);
            });
        }elseif(userrole() == 14){ //sales personnel
            // $data->where('sales_person_code', @Auth::user()->sales_employee_code); //previous code

            // $territory = TerritorySalesSpecialist::where('user_id', userid())->with('territory:id,territory_id')->get();
            // $sapConnections = TerritorySalesSpecialist::where('user_id', userid())->groupBy('sap_connection_id')->pluck('sap_connection_id')->toArray();
            
            // $territoryIds= [];
            // foreach($territory as $id){
            //     $territoryIds[] = $id->territory->territory_id;
            // }

            // $territoryIds = (@$territoryIds)? $territoryIds : [-3];
            // $sapConnections = (@$sapConnections)? $sapConnections : [-3];

            $data->where('sales_specialist_id', userid());
            // $data->whereHas('customer', function($q) use($territoryIds, $sapConnections){
            //     // $cus = CustomersSalesSpecialist::where(['ss_id' => Auth::user()->id])->pluck('customer_id')->toArray();
            //     $q->whereIn('real_sap_connection_id', $sapConnections);
            //     $q->whereIn('territory', $territoryIds);
            // });

        }elseif(!in_array(userrole(), [1,10,11] )){
            if (!is_null(@Auth::user()->created_by)) {
                $customers = @Auth::user()->created_by_user->get_multi_customer_details();
                $data->whereHas('customer', function($q) use ($customers){
                    $q->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                });
                $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
            
                // $data->where('card_code', @Auth::user()->created_by_user->customer->card_code);
            } else {
                return DataTables::of(collect())->make(true);
            }
        }

        if($request->filter_brand != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where(function($que) use ($request) {
                        $que->whereHas('product_groups', function($q2) use ($request){
                            $q2->where('product_group_id', $request->filter_brand);
                        });
                    });
                });
            });
        }

        if(@$request->filter_class != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('u_classification', $request->filter_class);
                });
            });
        }

        if($request->filter_sales_specialist != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where(function($que) use ($request) {
                        $que->whereHas('sales_specialist', function($q2) use ($request){
                            $q2->where('id', $request->filter_sales_specialist);
                        });
                    });
                });
            });
        }

        if($request->filter_market_sector != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('u_sector', $request->filter_market_sector);
                });
            });
        }

        if($request->filter_group != ""){
            $data->whereHas('customer.group', function($q) use ($request){
                $q->where('code',$request->filter_group);
            });
        }
        
        $territory = @$request->filter_territory;
        if(!empty($territory) && $territory[0] !== null){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer.territories', function($q) use ($request) {
                    $q->whereIn('id', $request->filter_territory);
                });
            });
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('doc_num','LIKE',"%".$request->filter_search."%");
                $q->orwhere('doc_entry','LIKE',"%".$request->filter_search."%");
                $q->orWhereHas('customer', function($c) use ($request){
                    $c->where('card_name','LIKE',"%".$request->filter_search."%");
                });
                $q->orWhereHas('sales_specialist', function($c) use ($request){
                    $c->where('sales_specialist_name','LIKE',"%".$request->filter_search."%");
                });
            });
        }

        if(@$request->filter_customer != ""){
            $data->whereHas('customer', function($q) use ($request) {
                $q->where('card_code',$request->filter_customer);
            });
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }


        if($request->filter_approval != ""){
            $data->where('approval',$request->filter_approval);
            if($request->filter_approval == 'Pending'){
                $data->whereDoesntHave('quotation')
                     ->whereDate('created_at', '>=', '2025-02-26');
            }
        }
        
        $status = @$request->filter_status;
        if(!empty($status) && $status[0] !== null){
            $data->whereHas('quotation', function($query) use ($status){
                $selected_status = [];

                if(in_array("PN",$status)){
                    array_push($selected_status, 'Pending');
                }
                if(in_array("OP",$status)){
                    array_push($selected_status, 'On Process');
                }
                if(in_array("CL", $status)){
                    array_push($selected_status, 'Cancelled');
                }

                if(in_array("CM", $status)){
                    array_push($selected_status, 'Completed');
                }

                if(in_array("PS", $status)){
                    array_push($selected_status, 'Partially Served');
                }

                $query->whereIn('status', $selected_status);

                if(in_array("DL", $status)){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', 'DL');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }

                if(in_array("IN", $status)){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', 'IN');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }
                if(in_array("CF", $status)){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', 'CF');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }
                if(in_array("FD", $status)){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', '!=','CM')->where('u_sostat', '!=','DL');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }
                
            });
        }
        
        if($request->filter_order_type != ""){
            $data->whereHas('quotation', function($query) use ($request){   
                if($request->filter_order_type == "Standard"){
                    $query->whereNull('customer_promotion_id');
                }elseif($request->filter_order_type == "Promotion"){
                    $query->whereNotNull('customer_promotion_id');
                }
            });
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('created_at', '>=' , $start);
            $data->whereDate('created_at', '<=' , $end);
        }

        $data->when(!isset($request->order), function ($q) {

            $q->orderBy('created_at', 'desc');
            // $q->whereHas('quotation', function($query){
            //     $query->orderBy('doc_date', 'desc');
            //     $query->orderBy('doc_time', 'desc');
            // });
        });
        
        $data = $data->with([
                            'customer.sap_connection',
                            'customer.group',
                            'customer',
                            'quotation',
                            'items',
                            'address',
                            'approver'
        ]);
                        
        $records = [];
        $key_counter = 1;
        $data->chunk(1000, function ($orders) use (&$records, &$key_counter) {

            $key_counter =+ $key_counter;
            foreach ($orders as $key => $value) {
                
                $formattedDuration  = $date = $time = '-';
                if($value->approved_at){
                    $approvedAt = Carbon::parse($value->approved_at);
                    $currentDateTime = Carbon::parse($value->created_at);
                    $days = $currentDateTime->diffInDays($approvedAt, false);
                    $noun = ($days > 1) ? 'days' : 'day';
                    $duration = $currentDateTime->diff($approvedAt);

                    $formattedDuration = $duration->format('%d '.$noun.' %H:%I:%S');

                    $date = $approvedAt->format('M d, Y');
                    $time = $approvedAt->format('H:i A');
                }
        
                $records[] = [
                    'no' => $key_counter,
                    'business_unit' => $value->customer->sap_connection->company_name ?? "-",
                    'branch' => $value->customer->group->name ?? "-",
                    'customer_code' => $value->customer->card_code ?? "-",
                    'customer_name' => $value->customer->card_name ?? "-",
                    'order_amount' => number_format_value($value->quotation->doc_total ?? $value->items->sum('total')),
                    'delivery_address' => $value->address->street ?? "-",
                    'approval_status' => $value->quotation ? 'Approved' : $value->approval,
                    'approved_by' => $value->approver->sales_specialist_name ?? '-',
                    'approval_date' => $date,
                    'approval_time' => $time,
                    'approval_duration' => $formattedDuration,
                    'reason' => $value->disapproval_remarks ?? "-",
                ];

                $key_counter++;
            }
        });
        
        
        if(count($records)){
            $title = 'Order Report '.date('dmY').'.xlsx';
            
            $endTime = microtime(true); // End timing
            // Log::info('Export query execution time: ' . round($endTime - $startTime, 2) . ' seconds');

            return Excel::download(new OrderExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));

        $endTime = microtime(true); // End timing in case of failure
        // Log::info('Export query execution time (no data): ' . round($endTime - $startTime, 2) . ' seconds');
        
        return redirect()->back();
    }

    public function itemStatus(Request $request){
        $orderd_quantity = $request->quantity;
        $data = Quotation::where('card_code', $request->card_code)->where('u_omsno', @$request->details)->first();
        $product = $request->product;
        if(!empty($data)){
            $invoice = @$data->order->invoice1;
            $invoiceIds = [];
            $doc_num = [];
            if(!empty($invoice)){
                foreach($invoice as $val){
                    $invoiceIds[] = @$val->id;
                    $doc_num[] = $val->doc_num;
                }
            }

            $num = InvoiceItem::with('invoice')->whereIn('invoice_id',$invoiceIds)->where('item_code',@$request->id)->pluck('invoice_id');
            $invoice_num = Invoice::whereIn('id',$num)->get();
            $sum_of_quan = 0;            

            $invoice_item_details = InvoiceItem::with('invoice')->whereIn('invoice_id',$invoiceIds)->where('item_code',@$request->id)->get();

            foreach ($invoice_item_details as $key => $value) {
                $sum_of_quan = @$value->quantity + $sum_of_quan;
            }

            $status = getOrderStatusByQuotation(@$data, true);
            $date_array = @$status['date_array'];
            $status = @$status['status'];
            // $data->status_details = view('customer-promotion.ajax.delivery-status-modal',compact('invoice_num'))->render();
            if(count($invoice_num) > 0){
                $data->status_details = view('customer-promotion.ajax.delivery-status-modal', compact('invoice_num','status','date_array','invoice_item_details','doc_num','product','sum_of_quan','orderd_quantity'))->render();
            }else{
                $data->status_details = view('customer-promotion.ajax.delivery-status-default-modal', compact('status','date_array','product','orderd_quantity'))->render();
            }
            $response = ['status' => true, 'message' => 'Order completed successfully!','data'=>$data];
            return $response;
        }
    }

    public function statusSync($less, $great){
        $quotations = Quotation::where('id', '>=', $less)->where('id', '<=', $great)->get();

        foreach($quotations as $quot){
            if($quot->cancelled === "Yes"){
                Quotation::where('id', $quot->id)->update(['status' =>'Cancelled']);
                // $quot->update(['status' => 'Cancelled']);
                // dd($quot->sap_connection_id." ".$quot->doc_entry." -".$quot->cancelled);
            }

            $check_order = $quot->order ?? '-';
            $check_inv = $quot->order->invoice1 ?? '-';

            if($check_inv === '-' && $check_order !== '-'){
                
                $order_stat = ($quot->order->cancelled === "Yes")? 'Cancelled' : 'On Process';
                Quotation::where('id', $quot->id)->update(['status' =>$order_stat]);
                // $quot->update(['status' =>$order_stat]);
                // dd($quot->sap_connection_id." ".$quot->doc_entry." -".$quot->cancelled);
            }

            if($check_inv !== '-'){
                foreach($quot->order->invoice1 as $inv){
                    // echo $quot->sap_connection_id." ".$quot->doc_entry." ".$inv->items->sum('quantity') .'-'.$quot->items->sum('quantity')."<br>";
                   if($inv->cancelled === "No" && $quot->order->cancelled === "No"){
                        $order_stat = ($inv->items->sum('quantity') === $quot->items->sum('quantity')) ? 'Completed' : 'Partially Served';
                        Quotation::where('id', $quot->id)->update(['status' =>$order_stat]);
                   }
                }
            }
        }
    }


    public function allOrders()
    {
        $company = collect();
        if(in_array(userrole(), [1, 10, 11])){
            $company = SapConnection::all();
        }
        $approvalStatus = LocalOrder::getApproval();
        return view('orders.orders-all', compact('company', 'approvalStatus'));
    }




}
