<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SapConnection;

use DB;
use DataTables;

use Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use Carbon\Carbon;

class SalesOrderReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = [];
        $managers = [];

        if(Auth::user()->role_id == 1){
            $company = SapConnection::all();
            $role = Role::where('name','Manager')->first();
            $managers = User::where('role_id',@$role->id)->get();
        }
        if(Auth::user()->role_id == 6){
            $company = SapConnection::all();          
        }
        return view('report.sales-order-report.index', compact('company','managers'));
    }

    public function getAll(Request $request){
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 36000);
        // For Pending
        $pending_total_sales_orders = $pending_total_sales_quantity = $pending_total_sales_revenue = 0;
        $pending_quotation_item = $this->getResultData($request,'PN');        
        $pending_quotation_item = $pending_quotation_item->get()->toArray();
        $pending_total_sales_orders = count($pending_quotation_item);        
        $pending_quotation_item_quan = $this->getResultDataQuantity($request,'PN');
        $pending_quotation_item_quan = $pending_quotation_item_quan->get()->toArray();
        if(!empty($pending_quotation_item_quan)){
            $pending_total_sales_quantity = array_sum(array_column($pending_quotation_item_quan, 'quantity'));
            $pending_total_sales_revenue = round(array_sum(array_column($pending_quotation_item_quan, 'gross_total')), 2);
        }

        // On Process
        $on_process_total_sales_orders = $on_process_total_sales_quantity = $on_process_total_sales_revenue = 0;
        $on_process_quotation_item = $this->getResultData($request,'OP');        
        $on_process_quotation_item = $on_process_quotation_item->get()->toArray();
        $on_process_total_sales_orders = count($on_process_quotation_item);
        $on_process_quotation_item_quan = $this->getResultDataQuantity($request,'OP');
        $on_process_quotation_item_quan = $on_process_quotation_item_quan->get()->toArray();
        if(!empty($on_process_quotation_item_quan)){
            $on_process_total_sales_quantity = array_sum(array_column($on_process_quotation_item_quan, 'quantity'));
            $on_process_total_sales_revenue = round(array_sum(array_column($on_process_quotation_item_quan, 'gross_total')), 2);
        }

        // For Deleivery
        $for_delivery_total_sales_orders = $for_delivery_total_sales_quantity = $for_delivery_total_sales_revenue = 0;
        $for_delivery_quotation_item = $this->getResultData($request,'FD');        
        $for_delivery_quotation_item = $for_delivery_quotation_item->get()->toArray();
        $for_delivery_total_sales_orders = count($for_delivery_quotation_item);
        $for_delivery_quotation_item_quan = $this->getResultDataQuantity($request,'FD');
        $for_delivery_quotation_item_quan = $for_delivery_quotation_item_quan->get()->toArray();
        if(!empty($for_delivery_quotation_item_quan)){
            $for_delivery_total_sales_quantity = array_sum(array_column($for_delivery_quotation_item_quan, 'quantity'));
            $for_delivery_total_sales_revenue = round(array_sum(array_column($for_delivery_quotation_item_quan, 'gross_total')), 2);
        }

        // Delivered
        $delivered_total_sales_orders = $delivered_total_sales_quantity = $delivered_total_sales_revenue = 0;
        $delivered_quotation_item = $this->getResultData($request,'DL');        
        $delivered_quotation_item = $delivered_quotation_item->get()->toArray();
        $delivered_total_sales_orders = count($delivered_quotation_item);
        $deleivered_quotation_item_quan = $this->getResultDataQuantity($request,'DL');
        $deleivered_quotation_item_quan = $deleivered_quotation_item_quan->get()->toArray();
        if(!empty($deleivered_quotation_item_quan)){
            $deleivered_total_sales_quantity = array_sum(array_column($deleivered_quotation_item_quan, 'quantity'));
            $deleivered_total_sales_revenue = round(array_sum(array_column($deleivered_quotation_item_quan, 'gross_total')), 2);
        }

        // Completed
        $completed_total_sales_orders = $completed_total_sales_quantity = $completed_total_sales_revenue = 0;   
        $completed_quotation_item = $this->getResultData($request,'CM');     
        $completed_quotation_item = $completed_quotation_item->get()->toArray();
        $completed_total_sales_orders = count($completed_quotation_item);
        $completed_quotation_item_quan = $this->getResultDataQuantity($request,'CM');
        $completed_quotation_item_quan = $completed_quotation_item_quan->get()->toArray();
        if(!empty($completed_quotation_item_quan)){
            $completed_total_sales_quantity = array_sum(array_column($completed_quotation_item_quan, 'quantity'));
            $completed_total_sales_revenue = round(array_sum(array_column($completed_quotation_item_quan, 'gross_total')), 2);
        }

        // Cancelled
        $cancelled_total_sales_orders = $cancelled_total_sales_quantity = $cancelled_total_sales_revenue = 0;
        $cancelled_quotation_item = $this->getResultData($request,'CL');
        $cancelled_quotation_item = $cancelled_quotation_item->get()->toArray();
        $cancelled_total_sales_orders = count($cancelled_quotation_item);
        $cancelled_quotation_item_quan = $this->getResultDataQuantity($request,'CL');
        $cancelled_quotation_item_quan = $cancelled_quotation_item_quan->get()->toArray();
        if(!empty($cancelled_quotation_item_quan)){
            $cancelled_total_sales_quantity = array_sum(array_column($cancelled_quotation_item_quan, 'quantity'));
            $cancelled_total_sales_revenue = round(array_sum(array_column($cancelled_quotation_item_quan, 'gross_total')), 2);
        }


        $data = compact(
            'pending_total_sales_quantity',
            'pending_total_sales_revenue', 
            'pending_total_sales_orders',

            'on_process_total_sales_orders',
            'on_process_total_sales_quantity',
            'on_process_total_sales_revenue', 

            'for_delivery_total_sales_orders',
            'for_delivery_total_sales_quantity',
            'for_delivery_total_sales_revenue', 

            'delivered_total_sales_orders',
            'delivered_total_sales_quantity',
            'delivered_total_sales_revenue', 

            'completed_total_sales_orders',
            'completed_total_sales_quantity',
            'completed_total_sales_revenue', 

            'cancelled_total_sales_orders',
            'cancelled_total_sales_quantity',
            'cancelled_total_sales_revenue', 
        );

        $response = [ 'status' => true, 'data' => $data, 'message' => 'Report details fetched successfully!'];
        return $response;
    } 

    public function getResultDataQuantity($request,$status){
        $data = QuotationItem::whereHas('quotation', function($q) use ($request,$status) {

                                                    if($status == 'PN'){
                                                        $q->doesntHave('order')->where('cancelled', "No")->whereNotNull('u_omsno');
                                                    }else if($status == 'OP'){
                                                        $q->whereHas('order',function($q1){
                                                            $q1->where('document_status', 'bost_Open')->doesntHave('invoice')->whereNotNull('u_omsno')->where('cancelled','No');
                                                        })->where('cancelled','No');
                                                    }else if($status == 'FD'){
                                                        $q->whereHas('order.invoice',function($q1) use ($status){
                                                            $q1->where('u_sostat', '!=','DL')->where('u_sostat', '!=','CM')->whereNotNull('u_omsno')->where('cancelled','No');
                                                        });

                                                    }else if($status == 'CL'){
                                                        $q->where(function($query){
                                                            $query->orwhere(function($q1){
                                                                $q1->where('cancelled', 'Yes');
                                                            });
                                                        });
                                                    }else{
                                                        $q->whereHas('order.invoice',function($q1) use ($status){
                                                            $q1->where('u_sostat', $status)->whereNotNull('u_omsno')->where('cancelled','No');
                                                        });
                                                    }
                                                    // $q->doesntHave('order')->where('document_status','bost_Open')->where('cancelled', "No");

                                                    if($request->engage_transaction != 0){
                                                        $q->whereNotNull('u_omsno');
                                                    }

                                                    if($request->filter_company != ""){
                                                        $q->where('sap_connection_id',$request->filter_company);
                                                    }

                                                    if($request->filter_date_range != ""){
                                                        $date = explode(" - ", $request->filter_date_range);
                                                        $start = date("Y-m-d", strtotime($date[0]));
                                                        $end = date("Y-m-d", strtotime($date[1]));

                                                        $q->whereDate('doc_date', '>=' , $start);
                                                        $q->whereDate('doc_date', '<=' , $end);
                                                    }

                                                    if(Auth::user()->role_id == 4){
                                                        $customers = Auth::user()->get_multi_customer_details();
                                                        $q->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                                                        $q->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));                                                        
                                                    }else{
                                                        if($request->filter_customer != ""){
                                                            $q->where(function($query) use ($request) {
                                                                $query->whereHas('customer', function($q1) use ($request){
                                                                    $q1->where('id', $request->filter_customer);
                                                                });
                                                            });
                                                        }
                                                    }

                                                    if($request->filter_manager != ""){
                                                            $q->where(function($query) use ($request) {
                                                                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                                                                    $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                                                                    $q1->where('ss_id', $salesAgent);
                                                                });
                                                            });
                                                    }

                                                    if(Auth::user()->role_id == 6){
                                                        $q->where(function($query) use ($request) {
                                                            $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                                                                $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                                                                $q1->where('ss_id', $salesAgent);
                                                            });
                                                        });
                                                    }
                                                    
                                                    if(Auth::user()->role_id == 2){
                                                        $q->where(function($query) use ($request) {
                                                                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                                                                    $q1->where('ss_id', Auth::id());
                                                                });
                                                            });
                                                    }else{
                                                        if($request->filter_sales_specialist != ""){
                                                            $q->where(function($query) use ($request) {
                                                                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                                                                    $q1->where('ss_id', $request->filter_sales_specialist);
                                                                });
                                                            });
                                                        }
                                                    }
                                                });

        

        if($request->filter_brand != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }

        return $data;
    }

    public function getResultData(Request $request,$status){
        $data = Quotation::whereNotNull('u_omsno');

        if(userrole() == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }elseif(userrole() == 2){
            $data->where('sales_person_code', @Auth::user()->sales_employee_code);
        }elseif(userrole() != 1 && userrole()!= 10){
            if (!is_null(@Auth::user()->created_by)) {
                $customers = @Auth::user()->created_by_user->get_multi_customer_details();
                $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
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

        if($request->filter_territory != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('territory', $request->filter_territory);
                });
            });
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('doc_type','LIKE',"%".$request->filter_search."%");
                $q->orwhere('doc_entry','LIKE',"%".$request->filter_search."%");
            });
        }

        if($request->filter_customer != ""){
           // $data->where('card_code',$request->filter_customer);

                $data->whereHas('customer', function($q1) use ($request){
                    $q1->where('id', $request->filter_customer);
                });
            
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        if($status != ""){
            if($status == "CL"){ //Cancel
                $data->where(function($query){
                    $query->orwhere(function($q){
                        $q->where('cancelled', 'Yes');
                    });
                    $query->orwhere(function($q){
                        $q->whereHas('order',function($p){
                            $p->where('cancelled', 'Yes');
                        });
                    });
                    $query->orwhere(function($q1){
                        $q1->whereHas('order.invoice1',function($p1){
                            $p1->where('cancelled', 'Yes');
                        });
                    });
                });
            }elseif($status == "PN"){ 
                $data->has('order', '<', 1)->where('cancelled','No');

            }elseif($status == "OP"){ //On Process
                $data->whereHas('order',function($q){
                    $q->where('document_status', 'bost_Open')->doesntHave('invoice')->whereNotNull('u_omsno')->where('cancelled','No');
                })->where('cancelled','No');

            }elseif($status == "FD"){
                $data->whereHas('order.invoice',function($q) use ($status){
                    $q->where('u_sostat', '!=','DL')->where('u_sostat', '!=','CM')->whereNotNull('u_omsno')->where('cancelled','No');
                })->where('cancelled','No');
            }else{
                $data->whereHas('order.invoice',function($q) use ($status){
                    $q->where('u_sostat', $status)->whereNotNull('u_omsno')->where('cancelled','No');
                })->where('cancelled','No');
            }
        }


        if($request->filter_order_type != ""){
            if($request->filter_order_type == "Standard"){
                $data->whereNull('customer_promotion_id');
            }elseif($request->filter_order_type == "Promotion"){
                $data->whereNotNull('customer_promotion_id');
            }
        }


        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('doc_date', '>=' , $start);
            $data->whereDate('doc_date', '<=' , $end);
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });
        return $data;
    }

    public function getChartData(Request $request){

        $inactiveCustomers = $activeCustomers = $customerWithOrder = 0;

        $active_customer_data = $this->getCustomerActiveData();
        $activeCustomers = count($active_customer_data->get()->toArray());

        $inactive_customer_data = $this->getCustomerInActiveData();
        $inactiveCustomers = count($inactive_customer_data->get()->toArray());

        $customer_with_data = $this->getCustomerWithData();
        $customerWithOrder = count($customer_with_data->get()->toArray());

        $data = compact('inactiveCustomers','activeCustomers','customerWithOrder');

        $response = [ 'status' => true, 'data' => $data];
        return $response;
    }

    public function getStatusChartData(Request $request){
        $customers = Auth::user()->get_multi_customer_details();

        $total_pending_order = Quotation::whereNotNull('u_omsno')->where('cancelled','No')->doesntHave('order')->whereIn('card_code', array_column($customers->toArray(), 'card_code'))->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))->count();

        $total_on_process_order = Order::whereNotNull('u_omsno')->where('cancelled','No')->doesntHave('invoice')->whereIn('card_code', array_column($customers->toArray(), 'card_code'))->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))->where('document_status', 'bost_Open')->count();

        $total_for_delivery_order = Quotation::whereNotNull('u_omsno')
        ->where('cancelled','No')
        ->whereIn('card_code', array_column($customers->toArray(), 'card_code'))
        ->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))
        ->whereHas('order',function($q){
             $q->where('cancelled', 'No');
        })
        ->whereHas('order.invoice',function($q){
             $q->where('cancelled', 'No')->where('u_sostat','!=', 'DL')->where('u_sostat','!=', 'CM');
        })                                    
        ->count();

        $total_delivered_order = Quotation::whereNotNull('u_omsno')
        ->where('cancelled','No')
        ->whereIn('card_code', array_column($customers->toArray(), 'card_code'))
        ->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))
        ->whereHas('order.invoice',function($q){
             $q->where('cancelled', 'No')->where('document_status', 'bost_Open')->where('u_sostat', 'DL');
        })->count();

        $total_completed_order = Quotation::whereNotNull('u_omsno')
        ->where('cancelled','No')
        ->whereIn('card_code', array_column($customers->toArray(), 'card_code'))
        ->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))
        ->whereHas('order.invoice',function($q){
             $q->where('cancelled', 'No')->where('document_status', 'bost_Open')->where('u_sostat', 'CM');
        })->count();

        $status = [];
        array_push($status, $total_pending_order);
        array_push($status, $total_on_process_order);
        array_push($status, $total_for_delivery_order);
        array_push($status, $total_delivered_order);
        array_push($status, $total_completed_order);

        $data = [];
        array_push($data, array('name' => 'Status', 'data' => $status));

        $category = [
                        'Pending',
                        'On Process',
                        'For Deleivery',
                        'Delivered',
                        'Completed',
                    ];

        return ['status' => true, 'data' => $data, 'category' => $category];

    }

    public function getCustomerWithData(){
        $customer = Customer::where('is_active','1')
                            ->where('frozen','0')
                            ->whereHas('customerOrder');
        return $customer;
    }

    public function getCustomerActiveData(){
        $today = Carbon::today()->toDateString();
        $customer = Customer::where('is_active','1')
                            ->where('frozen','0');
        return $customer;
    }

    public function getCustomerInActiveData(){
        $customer = Customer::where('frozen','1')->where('is_active','0')->whereNull('frozen_from')->whereNull('frozen_to');
        return $customer;
    }

}
