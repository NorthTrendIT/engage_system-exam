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

use Mail;
use DataTables;
use Auth;
use OneSignal;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;

use App\Models\Product;

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
        if(userrole() == 1){
            $company = SapConnection::all();
        }
        return view('orders.index', compact('company'));
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
        if(userrole() == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }elseif(userrole() == 2){
            $data->where('sales_person_code', @Auth::user()->sales_employee_code);
        }elseif(!is_null(Auth::user()->created_by)){
            $customers = Auth::user()->created_by_user->get_multi_customer_details();
            $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }elseif(userrole() != 1){
            return abort(404);
        }

        
        $data = $data->firstOrFail();
        $products = Product::where('id',@$data->doc_num)->first();
        
        return view('orders.order_view', compact('data','products'));
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

    public function syncOrders(){
        try {

            $sap_connections = SapConnection::where('id', '!=', 5)->get();
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
                    if(@$quotation->order->doc_entry){
                        $sap_orders = new SAPOrders($sap_connection->db_name, $sap_connection->user_name, $sap_connection->password);
                        $sap_orders->addSpecificOrdersDataInDatabase(@$quotation->order->doc_entry);
                    }

                    // Sync Invoice Data
                    if(@$quotation->order->invoice->doc_entry){
                        $sap_invoices = new SAPInvoices($sap_connection->db_name, $sap_connection->user_name, $sap_connection->password);
                        $sap_invoices->addSpecificInvoicesDataInDatabase(@$quotation->order->invoice->doc_entry);
                    }

                    $response = ['status' => true, 'message' => 'Sync order details successfully !'];
                } catch (\Exception $e) {
                    $response = ['status' => false, 'message' => 'Something went wrong !'];
                }
            }
        }
        return $response;
    }

    public function getAll(Request $request){
        $data = Quotation::query();

        if(userrole() == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }elseif(userrole() == 2){
            $data->where('sales_person_code', @Auth::user()->sales_employee_code);
        }elseif(userrole() != 1){
            if (!is_null(@Auth::user()->created_by)) {
                $customers = @Auth::user()->created_by_user->get_multi_customer_details();
                $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
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
            $data->where('card_code',$request->filter_customer);
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_status != ""){
            $status = $request->filter_status;

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
                        $q1->whereHas('order.invoice',function($p1){
                            $p1->where('cancelled', 'Yes');
                        });
                    });
                });

            }elseif($status == "PN"){ //Pending

                $data->has('order', '<', 1);

            }elseif($status == "OP"){ //On Process

                $data->whereHas('order',function($q){
                    $q->where('document_status', 'bost_Open')->doesntHave('invoice');
                });

            }else{
                $data->whereHas('order.invoice',function($q) use ($status){
                    $q->where('cancelled', 'No')->where('document_status', 'bost_Open')->where('u_sostat', $status);
                });
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

        // dd($data->get());
        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('name', function($row) {
                                return  @$row->customer->card_name ?? @$row->card_name ?? "-";
                            })
                            ->addColumn('status', function($row) {
                                return getOrderStatusBtnHtml(getOrderStatusByQuotation($row));
                            })
                            ->addColumn('doc_entry', function($row) {
                                return '<a href="' . route('orders.show',$row->id). '" title="View details">'.$row->doc_entry.'</a>';
                            })
                            ->addColumn('total', function($row) {
                                return '₱ '. number_format_value($row->doc_total);
                            })
                            ->addColumn('date', function($row) {
                                return date('M d, Y',strtotime($row->doc_date));
                            })
                            ->addColumn('due_date', function($row) {
                                return date('M d, Y',strtotime($row->doc_due_date));
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
                                $btn = '<a href="' . route('orders.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm mr-10">
                                            <i class="fa fa-eye"></i>
                                        </a>';
                                if(userrole() == 1){
                                    $btn .= '<a href="javascript:;" data-url="'. route('orders.notify-customer').'" data-order="'.$row->id.'" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm m-5 notifyCustomer" title="Notify Customer">
                                            <i class="fa fa-bell"></i>
                                        </a>';
                                }
                                return $btn;
                            })
                            ->orderColumn('name', function ($query, $order) {
                                //$query->orderBy('card_name', $order);

                                $query->select('quotations.*')->join('customers', 'quotations.card_code', '=', 'customers.card_code')
                                    ->orderBy('customers.card_name', $order);
                            })
                            ->orderColumn('doc_entry', function ($query, $order) {
                                $query->orderBy('doc_entry', $order);
                            })
                            ->orderColumn('total', function ($query, $order) {
                                $query->orderBy('doc_total', $order);
                            })
                            ->orderColumn('date', function ($query, $order) {
                                $query->orderBy('doc_date', $order);
                            })
                            ->orderColumn('due_date', function ($query, $order) {
                                $query->orderBy('doc_due_date', $order);
                            })
                            ->orderColumn('company', function ($query, $order) {
                                $query->join('sap_connections', 'quotations.sap_connection_id', '=', 'sap_connections.id')->orderBy('sap_connections.company_name', $order);
                            })
                            ->rawColumns(['action', 'status', 'order_type','doc_entry'])
                            ->make(true);
    }

    public function cancelOrder(Request $request){

        $response = ['status' => false, 'message' => 'Record not found!'];

        $quotation = Quotation::where('id', $request->id);
        // if(userrole() == 4){
        //     $quotation->where('card_code', @Auth::user()->customer->card_code);
        // }elseif(userrole() == 2){
        //     $quotation->where('sales_person_code', @Auth::user()->sales_employee_code);
        // }elseif(userrole() != 1){
        //     return abort(404);
        // }

        if(userrole() == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $quotation->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $quotation->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }elseif(userrole() == 2){
            $quotation->where('sales_person_code', @Auth::user()->sales_employee_code);
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
            
            $sap_connection = @$quotation->sap_connection;

            $sap_quotations = new SAPQuotations(@$sap_connection->db_name, @$sap_connection->user_name, @$sap_connection->password);
            $response = $sap_quotations->cancelSpecificQuotation($quotation->id, $quotation->doc_entry);

            if(@$response['status']){
                $response = ['status' => true, 'message' => 'Order canceled successfully!'];
            }else{
                $response = ['status' => false, 'message' => 'Something went wrong !'];
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
        }elseif(userrole() == 2){
            $quotation->where('sales_person_code', @Auth::user()->sales_employee_code);
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
        $filter = collect();
        if(@$request->data){
          $filter = json_decode(base64_decode($request->data));
        }

        $data = Quotation::orderBy('id', 'desc');

        if(userrole() == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }elseif(userrole() == 2){
            $data->where('sales_person_code', @Auth::user()->sales_employee_code);
        }elseif(userrole() != 1){
            if (!is_null(@Auth::user()->created_by)) {
                $customers = @Auth::user()->created_by_user->get_multi_customer_details();
                $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
            } else {
                return redirect()->back();
            }
        }

        if(@$filter->filter_brand != ""){
            $data->where(function($query) use ($filter) {
                $query->whereHas('customer', function($q) use ($filter) {
                    $q->where(function($que) use ($filter) {
                        $que->whereHas('product_groups', function($q2) use ($filter){
                            $q2->where('product_group_id', $filter->filter_brand);
                        });
                    });
                });
            });
        }

        if(@$filter->filter_class != ""){
            $data->where(function($query) use ($filter) {
                $query->whereHas('customer', function($q) use ($filter) {
                    $q->where('u_classification', $filter->filter_class);
                });
            });
        }

        if(@$filter->filter_sales_specialist != ""){
            $data->where(function($query) use ($filter) {
                $query->whereHas('customer', function($q) use ($filter) {
                    $q->where(function($que) use ($filter) {
                        $que->whereHas('sales_specialist', function($q2) use ($filter){
                            $q2->where('id', $filter->filter_sales_specialist);
                        });
                    });
                });
            });
        }

        if(@$filter->filter_market_sector != ""){
            $data->where(function($query) use ($filter) {
                $query->whereHas('customer', function($q) use ($filter) {
                    $q->where('u_sector', $filter->filter_market_sector);
                });
            });
        }

        if(@$filter->filter_territory != ""){
            $data->where(function($query) use ($filter) {
                $query->whereHas('customer', function($q) use ($filter) {
                    $q->where('territory', $filter->filter_territory);
                });
            });
        }

        if(@$filter->filter_search != ""){
            $data->where(function($q) use ($filter) {
                $q->orwhere('doc_type','LIKE',"%".$filter->filter_search."%");
                $q->orwhere('doc_entry','LIKE',"%".$filter->filter_search."%");
            });
        }

        if(@$filter->filter_customer != ""){
            $data->where('card_code',$filter->filter_customer);
        }

        if(@$filter->filter_company != ""){
            $data->where('sap_connection_id',$filter->filter_company);
        }

        if(@$filter->filter_status != ""){
            $status = $filter->filter_status;

            if($status == "CL"){ //Cancel

                $data->where(function($query){
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

            }elseif($status == "PN"){ //Pending

                $data->has('order', '<', 1);

            }elseif($status == "OP"){ //On Process

                $data->whereHas('order',function($q){
                    $q->where('document_status', 'bost_Open')->doesntHave('invoice');
                });

            }else{
                $data->whereHas('order.invoice',function($q) use ($status){
                    $q->where('cancelled', 'No')->where('document_status', 'bost_Open')->where('u_sostat', $status);
                });
            }
        }


        if(@$filter->filter_date_range != ""){
            $date = explode(" - ", $filter->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('doc_date', '>=' , $start);
            $data->whereDate('doc_date', '<=' , $end);
        }

        if(@$filter->filter_order_type != ""){
            if(@$filter->filter_order_type == "Standard"){
                $data->whereNull('customer_promotion_id');
            }elseif(@$filter->filter_order_type == "Promotion"){
                $data->whereNotNull('customer_promotion_id');
            }
        }
        
        $data = $data->get();

        $records = array();
        foreach($data as $key => $value){

            $type = "Standard";
            if(!is_null($value->customer_promotion_id)){
                $type = "Promotion";
            }
                                
            $records[] = array(
                            'no' => $key + 1,
                            'company' => @$value->sap_connection->company_name ?? "-",
                            'doc_entry' => $value->doc_entry ?? "-",
                            'type' => $type,
                            'customer' => @$value->customer->card_name ?? @$value->card_name ?? "-",
                            'doc_total' => number_format_value($value->doc_total),
                            'created_at' => date('M d, Y',strtotime($value->doc_date)),
                            'status' => getOrderStatusByQuotation($value),
                          );
        }
        if(count($records)){
            $title = 'Order Report '.date('dmY').'.xlsx';
            return Excel::download(new OrderExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }

}
