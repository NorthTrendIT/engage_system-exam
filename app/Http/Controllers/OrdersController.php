<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPOrders;
use App\Support\SAPInvoices;
use App\Support\SAPQuotations;
use App\Jobs\SyncOrders;
use App\Jobs\SyncInvoices;
use App\Jobs\SyncQuotations;
use App\Jobs\SAPAllOrderPost;
use App\Jobs\SAPCustomerPromotionPost;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\LocalOrder;
use App\Models\CustomerPromotion;
use App\Models\SapConnection;
use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationConnection;
use App\Support\SAPOrderPost;
use Mail;
use DataTables;
use Auth;
use OneSignal;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;

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
        $data = Quotation::with(['items.product', 'customer'])->where('id', $id);
        if(userrole() == 4){
            $data->where('card_code', @Auth::user()->customer->card_code);
        }elseif(userrole() == 2){
            $data->where('sales_person_code', @Auth::user()->sales_employee_code);
        }elseif(userrole() != 1){
            return abort(404);
        }

        $data = $data->firstOrFail();

        return view('orders.order_view', compact('data'));
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

            $sap_connections = SapConnection::all();
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

                SyncQuotations::dispatch($value->db_name, $value->user_name , $value->password, $quotation_log_id);
                SyncOrders::dispatch($value->db_name, $value->user_name , $value->password, $order_log_id);
                SyncInvoices::dispatch($value->db_name, $value->user_name , $value->password, $invoice_log_id);
            }

            $response = ['status' => true, 'message' => 'Sync Orders successfully !'];
        } catch (\Exception $e) {
            // dd($e);
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    public function getAll(Request $request){
        $data = Quotation::query();

        if(userrole() == 4){
            $data->where('card_code', @Auth::user()->customer->card_code);
        }elseif(userrole() == 2){
            $data->where('sales_person_code', @Auth::user()->sales_employee_code);
        }elseif(userrole() != 1){
            if (!is_null(@Auth::user()->created_by)) {
                $data->where('card_code', @Auth::user()->created_by_user->customer->card_code);
            } else {
                return DataTables::of(collect())->make(true);;
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
                    $q->where('u_class', $request->filter_class);
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
                    $q->where('u_msec', $request->filter_market_sector);
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
                // $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
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
                                return getOrderStatus($row);
                            })
                            ->addColumn('doc_entry', function($row) {
                                return $row->doc_entry;
                            })
                            ->addColumn('total', function($row) {
                                return '₱ '. number_format($row->doc_total);
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
                            ->addColumn('action', function($row){
                                $btn = '<a href="' . route('orders.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-color-primary">
                                            <i class="fa fa-eye"></i>
                                        </a>';
                                if(userrole() == 1){
                                    $btn .= '<a href="javascript:;" data-url="'. route('orders.notify-customer').'" data-order="'.$row->id.'" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-color-primary m-5 notifyCustomer" title="Notify Customer">
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
                            ->rawColumns(['action'])
                            ->make(true);
    }

    // Notify Customer
    public function notifyCustomer(Request $request){
        $q_id = $request->order_id;
        $quotation = Quotation::with('customer')->find($q_id);

        if(!empty($quotation)){
            $link = route('orders.show', $q_id);
            $user = User::where('customer_id' ,'=', $quotation->customer->id)->firstOrFail();
            // return view('emails.order_update',array('link'=>$link, 'order_no'=>$quotation->doc_entry));

            // Send Mail.
            Mail::send('emails.order_update', array('link'=>$link, 'order_no'=>$quotation->doc_entry), function($message) use($user) {
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
        // dd($data);

        $data->where('confirmation_status', 'ERR');

        // dd($data->get());

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

        return view('orders.pending_promotion_view',compact('data'));
    }

    public function getAllPendingPromotion(Request $request){

        $data = CustomerPromotion::where(['is_sap_pushed' => 0, 'status' => 'approved']);

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
                SAPAllOrderPost::dispatch($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $sap_connection->id, $order_id);

                // $sap = new SAPOrderPost($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $sap_connection->id);

                // $sap->pushOrder($order_id);

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
                    SAPAllOrderPost::dispatch($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, @$order->id);
                }
            }
            return $response = ['status' => true, 'message' => 'All Order Placed Successfully!'];
        } else {
            return $response = ['status' => false, 'message' => 'Something went wrong!'];
        }
    }

    public function pushAllPromotion(Request $request){
        $data = CustomerPromotion::where(['is_sap_pushed' => 0, 'status' => 'approved'])->get();
        // dd($data);
        if(!empty($data)){
            foreach($data as $item){
                $sap_connection = SapConnection::find(@$item->sap_connection_id);

                if(!is_null($sap_connection)){
                    SAPCustomerPromotionPost::dispatch($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, @$item->id);
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
            $data->where('card_code', @Auth::user()->customer->card_code);
        }elseif(userrole() == 2){
            $data->where('sales_person_code', @Auth::user()->sales_employee_code);
        }elseif(userrole() != 1){
            if (!is_null(@Auth::user()->created_by)) {
                $data->where('card_code', @Auth::user()->created_by_user->customer->card_code);
            } else {
                return redirect()->back();
            }
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

        if(@$filter->filter_date_range != ""){
            $date = explode(" - ", $filter->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('doc_date', '>=' , $start);
            $data->whereDate('doc_date', '<=' , $end);
        }

        $data = $data->get();

        $records = array();
        foreach($data as $key => $value){
            $records[] = array(
                            'no' => $key + 1,
                            'company' => @$value->sap_connection->company_name,
                            'doc_entry' => $value->doc_entry,
                            'customer' => @$value->customer->card_name ?? @$value->card_name ?? "-",
                            'doc_total' => number_format($value->doc_total),
                            'created_at' => date('M d, Y',strtotime($value->doc_date)),
                            'status' => getOrderStatus($value),
                          );
        }
        if(count($records)){
            return Excel::download(new OrderExport($records), 'Order Report.xlsx');
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }

}
