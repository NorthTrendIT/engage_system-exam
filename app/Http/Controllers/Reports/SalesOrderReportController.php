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

    public function getAll1(Request $request)
    {       
        // For Pending
        $pending_total_sales_orders = $pending_total_sales_quantity = $pending_total_sales_revenue = 0;

        $pending_quotation_item = Quotation::doesntHave('order')->where('document_status','bost_Open')->where('cancelled', "No");

        if($request->engage_transaction != 0){
            $pending_quotation_item->whereNotNull('u_omsno');
        }

        if($request->filter_company != ""){
            $pending_quotation_item->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $pending_quotation_item->whereDate('doc_date', '>=' , $start);
            $pending_quotation_item->whereDate('doc_date', '<=' , $end);
        }

        if(Auth::user()->role_id == 4){
            $pending_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', Auth::id());
                    });
                });
        }else{
            if($request->filter_customer != ""){
                $pending_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });
                });
            }
        }

        if($request->filter_manager != ""){
            $pending_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }

        if(Auth::user()->role_id == 6){
            $pending_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }
                                                    
        if(Auth::user()->role_id == 2){
            $pending_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', Auth::id());
                    });
                });
        }else{
            if($request->filter_sales_specialist != ""){
                $pending_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        $pending_quotation_item = $pending_quotation_item->get()->toArray();

        $pending_total_sales_orders = count($pending_quotation_item);

        $pending_quotation_item_quan = QuotationItem::whereHas('quotation', function($q) use ($request) {
                                                    $q->doesntHave('order')->where('document_status','bost_Open')->where('cancelled', "No");

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
                                                        $q->where(function($query) use ($request) {
                                                                $query->whereHas('customer', function($q1) use ($request){
                                                                    $q1->where('id', Auth::id());
                                                                });
                                                            });
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
            $pending_quotation_item->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }

        if($request->filter_brand != ""){
            $pending_quotation_item_quan->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }

        $pending_quotation_item_quan = $pending_quotation_item_quan->get()->toArray();

        //$pending_total_sales_orders = count($pending_quotation_item);

        if(!empty($pending_quotation_item_quan)){
            $pending_total_sales_quantity = array_sum(array_column($pending_quotation_item_quan, 'quantity'));
            $pending_total_sales_revenue = round(array_sum(array_column($pending_quotation_item_quan, 'price_after_vat')), 2);
        }


        // For Approved
        $approved_total_sales_orders = $approved_total_sales_quantity = $approved_total_sales_revenue = 0;

        $approved_order_item = Order::doesntHave('invoice')
                                    ->where('document_status','bost_Open')
                                    ->where('u_sostat', "OP")
                                    ->where('cancelled','No');

        if($request->engage_transaction != 0){
            $approved_order_item->whereNotNull('u_omsno');
        }

        if($request->filter_company != ""){
            $approved_order_item->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $approved_order_item->whereDate('doc_date', '>=' , $start);
            $approved_order_item->whereDate('doc_date', '<=' , $end);
        }

        if(Auth::user()->role_id == 4){
            $approved_order_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', Auth::id());
                    });
                });
        }else{
            if($request->filter_customer != ""){
                $approved_order_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });
                });
            }
        }

        if($request->filter_manager != ""){
                $approved_order_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $q1->where('ss_id', $salesAgent);
                    });
                });
        }

        if(Auth::user()->role_id == 6){
            $qapproved_order_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }

        if(Auth::user()->role_id == 2){
            $approved_order_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', Auth::id());
                    });
                });
        }else{
            if($request->filter_sales_specialist != ""){
                $approved_order_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        $approved_order_item_quan = OrderItem::whereHas('order', function($q) use ($request) {
                                                    $q->doesntHave('invoice')->where('document_status','bost_Open')->where('u_sostat', "OP");
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
                                                        $q->where(function($query) use ($request) {
                                                                $query->whereHas('customer', function($q1) use ($request){
                                                                    $q1->where('id', Auth::id());
                                                                });
                                                            });
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
            $approved_order_item->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }

        if($request->filter_brand != ""){
            $approved_order_item_quan->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }

        $approved_order_item = $approved_order_item->get()->toArray();
        $approved_total_sales_orders = count($approved_order_item);

        $approved_order_item_quan = $approved_order_item_quan->get()->toArray();        

        if(!empty($approved_order_item_quan)){
            $approved_total_sales_quantity = array_sum(array_column($approved_order_item_quan, 'quantity'));
            $approved_total_sales_revenue = round(array_sum(array_column($approved_order_item_quan, 'price_after_vat')), 2);
        }


        // For Disapproved 
        // quotation table
        $disapproved_total_sales_orders = $disapproved_total_sales_quantity = $disapproved_total_sales_revenue = 0;

        $disapproved_quotation_item = Quotation::doesntHave('order');

        $disapproved_quotation_item->where(function($query){
            $query->orwhere(function($q1){
               $q1->where('document_status','Cancelled');
            });

            $query->orwhere(function($q1){
                $q1->where('cancelled', "Yes");
            });
        });

        if($request->engage_transaction != 0){
            $disapproved_quotation_item->whereNotNull('u_omsno');
        }

        if($request->filter_company != ""){
            $disapproved_quotation_item->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $disapproved_quotation_item->whereDate('doc_date', '>=' , $start);
            $disapproved_quotation_item->whereDate('doc_date', '<=' , $end);
        }

        if(Auth::user()->role_id == 4){
            $disapproved_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', Auth::id());
                    });
                });
        }else{
            if($request->filter_customer != ""){
                $disapproved_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });
                });
            }
        }

        if($request->filter_manager != ""){
                $disapproved_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $q1->where('ss_id', $salesAgent);
                    });
                });
        }

        if(Auth::user()->role_id == 6){
            $disapproved_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }
                                                
        if(Auth::user()->role_id == 2){
            $disapproved_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', Auth::id());
                    });
                });
        }else{
            if($request->filter_sales_specialist != ""){
                $disapproved_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        if($request->filter_brand != ""){
            $disapproved_quotation_item->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }

        $disapproved_quotation_item_quan = QuotationItem::whereHas('quotation', function($q) use ($request){
                                                    $q->doesntHave('order');

                                                    $q->where(function($query){
                                                        $query->orwhere(function($q1){
                                                           $q1->where('document_status','Cancelled');
                                                        });

                                                        $query->orwhere(function($q1){
                                                            $q1->where('cancelled', "Yes");
                                                        });
                                                    });

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
                                                        $q->where(function($query) use ($request) {
                                                                $query->whereHas('customer', function($q1) use ($request){
                                                                    $q1->where('id', Auth::id());
                                                                });
                                                            });
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
            $disapproved_quotation_item_quan->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }

        $disapproved_quotation_item = $disapproved_quotation_item->get()->toArray();
        $disapproved_total_sales_orders = count($disapproved_quotation_item);

        $disapproved_quotation_item_quan = $disapproved_quotation_item_quan->get()->toArray();

        if(!empty($disapproved_quotation_item_quan)){
            $disapproved_total_sales_quantity += array_sum(array_column($disapproved_quotation_item_quan, 'quantity'));
            $disapproved_total_sales_revenue += round(array_sum(array_column($disapproved_quotation_item_quan, 'price_after_vat')), 2);
        }

        // order table

        $disapproved_order_item = Order::doesntHave('invoice');

        $disapproved_order_item->where(function($query){
            $query->orwhere(function($q1){
               $q1->where('document_status','Cancelled');
            });

            $query->orwhere(function($q1){
                $q1->where('cancelled', "Yes");
            });
        });

        if($request->engage_transaction != 0){
            $disapproved_order_item->whereNotNull('u_omsno');
        }

        if($request->filter_company != ""){
            $disapproved_order_item->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $disapproved_order_item->whereDate('doc_date', '>=' , $start);
            $disapproved_order_item->whereDate('doc_date', '<=' , $end);
        }

        if(Auth::user()->role_id == 4){
            $disapproved_order_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', Auth::id());
                    });
                });
        }else{
            if($request->filter_customer != ""){
                $disapproved_order_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });
                });
            }
        }

        if($request->filter_manager != ""){
                $disapproved_order_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $q1->where('ss_id', $salesAgent);
                    });
                });
        }

        if(Auth::user()->role_id == 6){
            $disapproved_order_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }

        if(Auth::user()->role_id == 2){
            $disapproved_order_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', Auth::id());
                    });
                });
        }else{
            if($request->filter_sales_specialist != ""){
                $disapproved_order_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        if($request->filter_brand != ""){
            $disapproved_order_item->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }


        $disapproved_order_item_quan = OrderItem::whereHas('order', function($q) use ($request){
                                                    $q->has('quotation')->doesntHave('invoice');

                                                    $q->where(function($query){
                                                        $query->orwhere(function($q1){
                                                           $q1->where('document_status','Cancelled');
                                                        });

                                                        $query->orwhere(function($q1){
                                                            $q1->where('cancelled', "Yes");
                                                        });
                                                    });

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
                                                        $q->where(function($query) use ($request) {
                                                                $query->whereHas('customer', function($q1) use ($request){
                                                                    $q1->where('id', Auth::id());
                                                                });
                                                            });
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
            $disapproved_order_item_quan->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }

        $disapproved_order_item = $disapproved_order_item->get()->toArray();
        $disapproved_total_sales_orders += count($disapproved_order_item);

        $disapproved_order_item_quan = $disapproved_order_item_quan->get()->toArray();

        if(!empty($disapproved_order_item_quan)){
            $disapproved_total_sales_quantity += array_sum(array_column($disapproved_order_item_quan, 'quantity'));
            $disapproved_total_sales_revenue += round(array_sum(array_column($disapproved_order_item_quan, 'price_after_vat')), 2);
        }

        $data = compact(
                            'pending_total_sales_quantity',
                            'pending_total_sales_revenue', 
                            'pending_total_sales_orders',

                            'approved_total_sales_quantity',
                            'approved_total_sales_revenue', 
                            'approved_total_sales_orders',

                            'disapproved_total_sales_quantity',
                            'disapproved_total_sales_revenue', 
                            'disapproved_total_sales_orders',
                        );


        $response = [ 'status' => true, 'data' => $data, 'message' => 'Report details fetched successfully!'];
        return $response;
    }

    public function getAll(Request $request){
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 3600);
        // For Pending
        $pending_total_sales_orders = $pending_total_sales_quantity = $pending_total_sales_revenue = 0;
        $pending_quotation_item = $this->getDataPendingQuaotation($request);        
        $pending_quotation_item = $pending_quotation_item->get()->toArray();
        $pending_total_sales_orders = count($pending_quotation_item);
        $pending_quotation_item_quan = $this->getPendingQuationQuantity($request);
        $pending_quotation_item_quan = $pending_quotation_item_quan->get()->toArray();
        if(!empty($pending_quotation_item_quan)){
            $pending_total_sales_quantity = array_sum(array_column($pending_quotation_item_quan, 'quantity'));
            $pending_total_sales_revenue = round(array_sum(array_column($pending_quotation_item_quan, 'price_after_vat')), 2);
        }

        // On Process
        $on_process_total_sales_orders = $on_process_total_sales_quantity = $on_process_total_sales_revenue = 0;
        $on_process_quotation_item = $this->getDataOnProcessQuaotation($request);        
        $on_process_quotation_item = $on_process_quotation_item->get()->toArray();
        $on_process_total_sales_orders = count($on_process_quotation_item);
        $on_process_quotation_item_quan = $this->getOnProcessQuationQuantity($request);
        $on_process_quotation_item_quan = $on_process_quotation_item_quan->get()->toArray();
        if(!empty($on_process_quotation_item_quan)){
            $on_process_total_sales_quantity = array_sum(array_column($on_process_quotation_item_quan, 'quantity'));
            $on_process_total_sales_revenue = round(array_sum(array_column($on_process_quotation_item_quan, 'price_after_vat')), 2);
        }

        // For Deleivery
        $for_delivery_total_sales_orders = $for_delivery_total_sales_quantity = $for_delivery_total_sales_revenue = 0;
        $for_delivery_quotation_item = $this->getDataForDeliveryQuotation($request);        
        $for_delivery_quotation_item = $for_delivery_quotation_item->get()->toArray();
        $for_delivery_total_sales_orders = count($for_delivery_quotation_item);
        $for_delivery_quotation_item_quan = $this->getForDeliveryQuationQuantity($request);
        $for_delivery_quotation_item_quan = $for_delivery_quotation_item_quan->get()->toArray();
        if(!empty($for_delivery_quotation_item_quan)){
            $for_delivery_total_sales_quantity = array_sum(array_column($for_delivery_quotation_item_quan, 'quantity'));
            $for_delivery_total_sales_revenue = round(array_sum(array_column($for_delivery_quotation_item_quan, 'price_after_vat')), 2);
        }

        // Delivered
        $delivered_total_sales_orders = $delivered_total_sales_quantity = $delivered_total_sales_revenue = 0;
        $delivered_quotation_item = $this->getDataDeliveredQuotation($request);        
        $delivered_quotation_item = $delivered_quotation_item->get()->toArray();
        $delivered_total_sales_orders = count($delivered_quotation_item);
        $deleivered_quotation_item_quan = $this->getDeliveredQuationQuantity($request);
        $deleivered_quotation_item_quan = $deleivered_quotation_item_quan->get()->toArray();
        if(!empty($deleivered_quotation_item_quan)){
            $deleivered_total_sales_quantity = array_sum(array_column($deleivered_quotation_item_quan, 'quantity'));
            $deleivered_total_sales_revenue = round(array_sum(array_column($deleivered_quotation_item_quan, 'price_after_vat')), 2);
        }

        // Completed
        $completed_total_sales_orders = $completed_total_sales_quantity = $completed_total_sales_revenue = 0;
        $completed_quotation_item = $this->getDataCompletedQuotation($request);        
        $completed_quotation_item = $completed_quotation_item->get()->toArray();
        $completed_total_sales_orders = count($completed_quotation_item);
        $completed_quotation_item_quan = $this->getCompletedQuationQuantity($request);
        $completed_quotation_item_quan = $completed_quotation_item_quan->get()->toArray();
        if(!empty($completed_quotation_item_quan)){
            $completed_total_sales_quantity = array_sum(array_column($completed_quotation_item_quan, 'quantity'));
            $completed_total_sales_revenue = round(array_sum(array_column($completed_quotation_item_quan, 'price_after_vat')), 2);
        }

        // Cancelled
        $cancelled_total_sales_orders = $cancelled_total_sales_quantity = $cancelled_total_sales_revenue = 0;
        $cancelled_quotation_item = $this->getDataCancelledQuotation($request);
        $cancelled_quotation_item = $cancelled_quotation_item->get()->toArray();
        $cancelled_total_sales_orders = count($cancelled_quotation_item);
        $cancelled_quotation_item_quan = $this->getCancelledQuationQuantity($request);
        $cancelled_quotation_item_quan = $cancelled_quotation_item_quan->get()->toArray();
        if(!empty($cancelled_quotation_item_quan)){
            $cancelled_total_sales_quantity = array_sum(array_column($cancelled_quotation_item_quan, 'quantity'));
            $cancelled_total_sales_revenue = round(array_sum(array_column($completed_quotation_item_quan, 'price_after_vat')), 2);
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

    public function getDataPendingQuaotation($request){
        $pending_quotation_item = Quotation::doesntHave('order')
                                    ->where('document_status','bost_Open')
                                    ->where('cancelled', "No");

        if($request->engage_transaction != 0){
           $pending_quotation_item->whereNotNull('u_omsno');
        }

        if($request->filter_company != ""){
            $pending_quotation_item->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $pending_quotation_item->whereDate('doc_date', '>=' , $start);
            $pending_quotation_item->whereDate('doc_date', '<=' , $end);
        }

        if(Auth::user()->role_id == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $pending_quotation_item->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $pending_quotation_item->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }else{
            if($request->filter_customer != ""){
                $pending_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });
                });
            }
        }

        if($request->filter_manager != ""){
            $pending_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }

        if(Auth::user()->role_id == 6){
            $pending_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }
                                                    
        if(Auth::user()->role_id == 2){
            $pending_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', Auth::id());
                    });
                });
        }else{
            if($request->filter_sales_specialist != ""){
                $pending_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        return $pending_quotation_item;
    }

    public function getDataOnProcessQuaotation($request){
        $on_process_quotation_item = Quotation::whereNotNull('u_omsno')->where('cancelled','No')
                                    ->whereHas('order',function($q){
                                        $q->where('document_status', 'bost_Open')->doesntHave('invoice');
                                    });
        if($request->engage_transaction != 0){
            $on_process_quotation_item->whereNotNull('u_omsno');
        }

        if($request->filter_company != ""){
            $on_process_quotation_item->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $on_process_quotation_item->whereDate('doc_date', '>=' , $start);
            $on_process_quotation_item->whereDate('doc_date', '<=' , $end);
        }

        if(Auth::user()->role_id == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $on_process_quotation_item->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $on_process_quotation_item->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }else{
            if($request->filter_customer != ""){
                $on_process_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });
                });
            }
        }

        if($request->filter_manager != ""){
            $on_process_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }

        if(Auth::user()->role_id == 6){
            $on_process_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }
                                                    
        if(Auth::user()->role_id == 2){
            $on_process_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', Auth::id());
                    });
                });
        }else{
            if($request->filter_sales_specialist != ""){
                $on_process_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        return $on_process_quotation_item;
    }

    public function getDataForDeliveryQuotation($request){
        $for_delivery_quotation_item = Quotation::whereNotNull('u_omsno')->where('cancelled','No')                            ->whereHas('order.invoice',function($q){
                                            $q->where('cancelled', 'No')->where('document_status', 'bost_Open')->where('u_sostat','!=', 'DL')->where('u_sostat','!=', 'CM');
                                        });
        if($request->engage_transaction != 0){
            $for_delivery_quotation_item->whereNotNull('u_omsno');
        }

        if($request->filter_company != ""){
            $for_delivery_quotation_item->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $for_delivery_quotation_item->whereDate('doc_date', '>=' , $start);
            $for_delivery_quotation_item->whereDate('doc_date', '<=' , $end);
        }

        if(Auth::user()->role_id == 4){
           
            $customers = Auth::user()->get_multi_customer_details();
            $for_delivery_quotation_item->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $for_delivery_quotation_item->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }else{
            if($request->filter_customer != ""){
                $for_delivery_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });
                });
            }
        }

        if($request->filter_manager != ""){
            $for_delivery_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }

        if(Auth::user()->role_id == 6){
            $for_delivery_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }
                                                    
        if(Auth::user()->role_id == 2){
            $for_delivery_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', Auth::id());
                    });
                });
        }else{
            if($request->filter_sales_specialist != ""){
                $for_delivery_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        return $for_delivery_quotation_item;
    }

    public function getDataDeliveredQuotation($request){
        $delivered_quotation_item = Quotation::whereNotNull('u_omsno')->where('cancelled','No')                            ->whereHas('order.invoice',function($q){
                                            $q->where('cancelled', 'No')->where('document_status', 'bost_Open')->where('u_sostat','DL');
                                        });
        if($request->engage_transaction != 0){
            $delivered_quotation_item->whereNotNull('u_omsno');
        }

        if($request->filter_company != ""){
            $delivered_quotation_item->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $delivered_quotation_item->whereDate('doc_date', '>=' , $start);
            $delivered_quotation_item->whereDate('doc_date', '<=' , $end);
        }

        if(Auth::user()->role_id == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $delivered_quotation_item->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $delivered_quotation_item->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }else{
            if($request->filter_customer != ""){
                $delivered_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });
                });
            }
        }

        if($request->filter_manager != ""){
            $delivered_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }

        if(Auth::user()->role_id == 6){
            $delivered_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }
                                                    
        if(Auth::user()->role_id == 2){
            $delivered_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', Auth::id());
                    });
                });
        }else{
            if($request->filter_sales_specialist != ""){
                $delivered_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        return $delivered_quotation_item;
    }

    public function getDataCompletedQuotation($request){

        $completed_quotation_item = Quotation::whereNotNull('u_omsno')->where('cancelled','No')                            ->whereHas('order.invoice',function($q){
                                            $q->where('cancelled', 'No')->where('document_status', 'bost_Open')->where('u_sostat','CM');
                                        });
        if($request->engage_transaction != 0){
            $completed_quotation_item->whereNotNull('u_omsno');
        }

        if($request->filter_company != ""){
            $completed_quotation_item->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $completed_quotation_item->whereDate('doc_date', '>=' , $start);
            $completed_quotation_item->whereDate('doc_date', '<=' , $end);
        }

        if(Auth::user()->role_id == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $completed_quotation_item->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $completed_quotation_item->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }else{
            if($request->filter_customer != ""){
                $completed_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });
                });
            }
        }

        if($request->filter_manager != ""){
            $completed_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }

        if(Auth::user()->role_id == 6){
            $completed_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }
                                                    
        if(Auth::user()->role_id == 2){
            $completed_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', Auth::id());
                    });
                });
        }else{
            if($request->filter_sales_specialist != ""){
                $completed_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        return $completed_quotation_item;
    }

    public function getDataCancelledQuotation($request){

        $cancelled_quotation_item = Quotation::whereNotNull('u_omsno')->where('cancelled','No')
                                                ->where(function($query){
                                                    $query->orwhere(function($q){
                                                        $q->where('cancelled', 'Yes');
                                                    });

                                                    $query->orwhere(function($q){
                                                        $q->whereHas('order',function($p){
                                                            $p->where('cancelled', 'Yes');
                                                        });
                                                    });

                                                    $query->orwhere(function($q1){
                                                        $q1->whereHas('order.invoice',function($p1){
                                                            $p1->where('cancelled', 'Yes');
                                                        });
                                                    });
                                                });
        if($request->engage_transaction != 0){
            $cancelled_quotation_item->whereNotNull('u_omsno');
        }

        if($request->filter_company != ""){
            $cancelled_quotation_item->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $cancelled_quotation_item->whereDate('doc_date', '>=' , $start);
            $cancelled_quotation_item->whereDate('doc_date', '<=' , $end);
        }

        if(Auth::user()->role_id == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $cancelled_quotation_item->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $cancelled_quotation_item->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }else{
            if($request->filter_customer != ""){
                $cancelled_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });
                });
            }
        }

        if($request->filter_manager != ""){
            $cancelled_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }

        if(Auth::user()->role_id == 6){
            $cancelled_quotation_item->where(function($query) use ($request) {
                $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q1->where('ss_id', $salesAgent);
                });
            });
        }
                                                    
        if(Auth::user()->role_id == 2){
            $cancelled_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', Auth::id());
                    });
                });
        }else{
            if($request->filter_sales_specialist != ""){
                $cancelled_quotation_item->where(function($query) use ($request) {
                    $query->whereHas('customer.sales_specialist', function($q1) use ($request){
                        $q1->where('ss_id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        return $cancelled_quotation_item;
    }

    public function getPendingQuationQuantity($request){
        $pending_quotation_item_quan = QuotationItem::whereHas('quotation', function($q) use ($request) {
                                                    $q->doesntHave('order')->where('document_status','bost_Open')->where('cancelled', "No");

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
            $pending_quotation_item->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }

        return $pending_quotation_item_quan;
    }

    public function getOnProcessQuationQuantity($request){

        $on_process_quotation_item_quan = QuotationItem::whereHas('quotation', function($q) use ($request) {
                                                    $q->where('cancelled', "No");

                                                    $q->where(function($query) use ($request) {
                                                        $query->whereHas('order', function($q1) use ($request){
                                                            $q1->where('document_status', 'bost_Open')->doesntHave('invoice');
                                                        });
                                                    });

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
            $on_process_quotation_item->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }
        return $on_process_quotation_item_quan;
    }    

    public function getForDeliveryQuationQuantity($request){

        $for_delivery_quotation_item_quan = QuotationItem::whereHas('quotation', function($q) use ($request) {
                                                    $q->where('cancelled', "No");

                                                    $q->where(function($query) use ($request) {
                                                        $query->whereHas('order.invoice', function($q1) use ($request){
                                                            $q1->where('cancelled', 'No')
                                                                ->where('document_status', 'bost_Open')
                                                                ->where('u_sostat','!=', 'DL')
                                                                ->where('u_sostat','!=', 'CM');
                                                        });
                                                    });

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
            $for_delivery_quotation_item->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }
        return $for_delivery_quotation_item_quan;
    }

    public function getDeliveredQuationQuantity($request){

        $delivered_quotation_item_quan = QuotationItem::whereHas('quotation', function($q) use ($request) {
                                                    $q->where('cancelled', "No");

                                                    $q->where(function($query) use ($request) {
                                                        $query->whereHas('order.invoice', function($q1) use ($request){
                                                            $q1->where('cancelled', 'No')
                                                                ->where('document_status', 'bost_Open')
                                                                ->where('u_sostat','DL');
                                                        });
                                                    });

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
            $delivered_quotation_item->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }
        return $delivered_quotation_item_quan;
    }

    public function getCompletedQuationQuantity($request){

        $completed_quotation_item_quan = QuotationItem::whereHas('quotation', function($q) use ($request) {
                                                    $q->where('cancelled', "No");

                                                    $q->where(function($query) use ($request) {
                                                        $query->whereHas('order.invoice', function($q1) use ($request){
                                                            $q1->where('cancelled', 'No')
                                                                ->where('document_status', 'bost_Open')
                                                                ->where('u_sostat','CM');
                                                        });
                                                    });

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
            $completed_quotation_item->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }
        return $completed_quotation_item_quan;
    }

    public function getCancelledQuationQuantity($request){

        $cancelled_quotation_item_quan = QuotationItem::whereHas('quotation', function($q) use ($request) {
                                                    $q->where('cancelled', "No");

                                                    $q->where(function($query) use ($request) {
                                                        $query->orwhere(function($q1){
                                                            $q1->where('cancelled', 'Yes');
                                                        });

                                                        $query->orwhere(function($q2){
                                                            $q2->whereHas('order',function($p){
                                                                $p->where('cancelled', 'Yes');
                                                            });
                                                        });

                                                        $query->orwhere(function($q3){
                                                            $q3->whereHas('order.invoice',function($p1){
                                                                $p1->where('cancelled', 'Yes');
                                                            });
                                                        });
                                                    });

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
            $cancelled_quotation_item->where(function($query) use ($request) {
                $query->whereHas('product1.group', function($q1) use ($request){
                    $q1->where('id', $request->filter_brand);
                });
            });
        }
        return $cancelled_quotation_item_quan;
    }

}
