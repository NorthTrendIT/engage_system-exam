<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SapConnection;
use App\Models\Invoice;
use App\Models\InvoiceItem;

use DB;
use DataTables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BackOrderReportExport;

use Auth;
use App\Models\User;
use App\Models\Role;

class BackOrderReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
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
        return view('report.back-order-report.index', compact('company','managers'));
    }

    public function getAll(Request $request){
        
        $data = $this->getReportResultData($request);

        $grand_total_of_quantity_ordered = number_format_value($data->sum('quantity'));
        $grand_total_of_remaining_open_quantity = number_format_value($data->sum('remaining_open_quantity'));
        $grand_total_of_open_amount = '₱ '. number_format_value($data->sum('open_amount'));

        $table = DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('item_name', function($row) {
                                return @$row->product1->item_name ?? @$row->item_description ?? "-";
                            })
                            ->addColumn('item_code', function($row) {
                                return @$row->product1->item_code ?? @$row->item_code ?? "-";
                            })
                            ->addColumn('order_no', function($row) {
                                return @$row->order->u_omsno ?? "-";
                            })

                            ->addColumn('customer', function($row) {
                                return @$row->order->customer->card_name ?? @$row->order->card_name ?? "-";
                            })
                            ->addColumn('sales_specialist', function($row) {
                                return @$row->order->sales_specialist->sales_specialist_name ?? "-";
                            })
                            ->addColumn('brand', function($row) {
                                return @$row->product1->group->group_name ?? "-";
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->addColumn('doc_entry', function($row) {
                                return @$row->order->doc_entry ?? "-";
                            })
                            ->addColumn('doc_date', function($row) {
                                return date('M d, Y',strtotime(@$row->order->doc_date));
                            })
                            ->addColumn('quantity', function($row) {
                                return @$row->quantity ?? "-";
                            })
                            ->addColumn('price', function($row) {
                                $html = '₱ '. "0.00";
                                if(@$row->price){
                                    $price = @$row->price;
                                    /*$price = @$row->price * @$row->quantity;*/
                                    $html = '₱ '.number_format_value(@$price, 2);
                                }
                                return $html;
                            })
                            ->addColumn('price_after_vat', function($row) {
                                $html = '₱ '. "0.00";
                                if(@$row->price_after_vat){
                                    $price = @$row->price_after_vat;
                                   // $price = @$row->price_after_vat * @$row->quantity;
                                    $html = '₱ '.number_format_value(@$price, 2);
                                }
                                return $html;
                            })
                            ->addColumn('remaining_open_quantity', function($row) {
                                return @$row->remaining_open_quantity ?? "0.00";
                            })
                            ->addColumn('open_amount', function($row) {
                                $html = '₱ '. "0.00";
                                if(@$row->open_amount){
                                    $html = '₱ '.number_format_value(@$row->open_amount, 2);
                                }
                                return $html;
                            })
                            ->addColumn('day_passed', function($row) {
                                $startDate = date("Y-m-d", strtotime(@$row->created_at));
                                $endDate = date("Y-m-d");

                                $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
                                return $days ." Day(s)";
                            })
                            ->rawColumns(['status','action','price','price_after_vat','day_passed'])
                            ->make(true);

        $data = compact(
                        'table',
                        'grand_total_of_quantity_ordered',
                        'grand_total_of_remaining_open_quantity',
                        'grand_total_of_open_amount',
                    );

        return $response = [ 'status' => true , 'message' => 'Report details fetched successfully !' , 'data' => $data ];
    }


    public function export(Request $request){
        $filter = collect();
        if(@$request->data){
          $filter = json_decode(base64_decode($request->data));
        }

        $data = $this->getReportResultData($filter);

        $data = $data->get();

        $records = array();
        foreach($data as $key => $value){

            $records[] = array(
                            'no' => $key + 1,
                            'so_no' =>@$value->order->doc_entry,
                            'so_date' => @$value->order->doc_date,
                            'business_unit' => @$value->sap_connection->company_name ?? "-",
                            'customer_name' => @$value->order->customer->card_name ?? @$value->order->card_name ?? "-",
                            'sales_person' => @$value->order->sales_specialist->sales_specialist_name ?? "-",
                            'brand' => @$value->product1->group->group_name ?? "-",
                            'product_code' => @$value->product1->item_code ?? @$value->item_code ?? "-",
                            'product_name' => @$value->product1->item_name ?? @$value->item_description ?? "-",
                            'quantity_ordered' => @$value->quantity,
                            'remaining_open_quantity' => @$value->remaining_open_quantity ?? "0.00",
                            'price' => @$value->price * @$value->quantity,
                            'price_after_vat' => @$value->price_after_vat * @$value->quantity,
                            'open_amount' => @$value->open_amount,
                          );
        }
        if(count($records)){
            $title = 'Back Order Report '.date('dmY').'.xlsx';
            return Excel::download(new BackOrderReportExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }

    public function getChartData(){
        $company = SapConnection::all();

        $total_quantity = [];
        $total_open_amount = [];
        $total_remaining_open_quantity = [];
        $category = [];


        $data = OrderItem::where('remaining_open_quantity', '>', 0)->orderBy('id','DESC');

        $data->whereHas('order', function($q){
            $q->where('document_status', 'bost_Open');
            $q->where('cancelled', 'No');
            $q->whereIn('u_sostat', ['OP']);
        });

        $data->select(
            'sap_connection_id',
            DB::raw("count(id) as total_id"),
            DB::raw("sum(quantity) as total_quantity"),
            DB::raw("sum(open_amount) as total_open_amount"),
            DB::raw("sum(remaining_open_quantity) as total_remaining_open_quantity"),
        );

        $data = $data->groupby('sap_connection_id')->get();

        foreach($company as $key => $value){
            $companyName = $value->company_name;

            array_push($category, $companyName);

            $obj = $data->firstWhere('sap_connection_id',$value->id);

            array_push($total_quantity, round(@$obj->total_quantity ?? 0, 2));
            array_push($total_open_amount, round(@$obj->total_open_amount ?? 0.00, 2));
            array_push($total_remaining_open_quantity, round(@$obj->total_remaining_open_quantity ?? 0, 2));

        }

        $data = [];
        array_push($data, array('name' => 'Total Quantity', 'data' => $total_quantity));
        array_push($data, array('name' => 'Remaining Open Quantity', 'data' => $total_remaining_open_quantity));
        array_push($data, array('name' => 'Total Open Amount', 'data' => $total_open_amount));

        return ['status' => true, 'data' => $data, 'category' => $category];
    }


    public function getReportResultData($request){
        $data = OrderItem::with('order')->where('remaining_open_quantity', '>', 0)->orderBy('id','DESC');

        $data->whereHas('order', function($q) use ($request){
            $q->where('document_status', 'bost_Open');
            $q->where('cancelled', 'No');
            $q->whereIn('u_sostat', ['OP']);

            if($request->engage_transaction != 0){
                $q->whereNotNull('u_omsno');
            }
           
        });


        if(@$request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        if(@$request->filter_brand != ""){
            $data->whereHas('product.group', function($q) use ($request) {
                $q->where('items_group_code', $request->filter_brand);
            });
        }

        if(@$request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereHas('order', function($q) use ($start, $end){
                $q->whereDate('doc_date', '>=' , $start);
                $q->whereDate('doc_date', '<=' , $end);
            });
        }

        if(Auth::user()->role_id == 4){
            $customers = Auth::user()->get_multi_customer_details();            
            $data->whereHas('order', function($q) use ($customers) {
                $q->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                $q->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
            });
        }else{
            if(@$request->filter_customer != ""){
                $data->whereHas('order.customer', function($q) use ($request) {
                    $q->where('id', $request->filter_customer);
                });
            }
        }

        if(@$request->filter_manager != ""){
            $data->whereHas('order.sales_specialist', function($q) use ($request) {
                $salesAgent = User::where('parent_id',@$request->filter_manager)->pluck('id')->toArray();
                $q->whereIn('id', @$salesAgent);
            });
        }

        if(Auth::user()->role_id == 6){
            $data->whereHas('order.sales_specialist', function($q) use ($request) {
                $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                $q->whereIn('id', $salesAgent);
            });
        }

        if(Auth::user()->role_id == 2){
            $data->whereHas('order.sales_specialist', function($q) use ($request) {
                $q->where('id', Auth::id());
            });
        }else{
            if(@$request->filter_sales_specialist != ""){
                $data->whereHas('order.sales_specialist', function($q) use ($request) {
                    $q->where('id', $request->filter_sales_specialist);
                });
            }
        }
        

        return $data;
    }

    public function getProductData(Request $request){
        $customers = Auth::user()->get_multi_customer_details();
        if($request->order == 'order'){
            $items = [];
            $data = Order::whereIn('card_code', array_column($customers->toArray(), 'card_code'))
                ->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))->pluck('id')->toArray();
            if($request->type == 'Quantity'){
                $items = OrderItem::whereIn('order_id',$data)->orderBy('quantity','DESC')->take(5)->get();
            }else if($request->type == 'Liters'){
                $items = OrderItem::with(['product' => function ($q) {
                            $q->orderBy('sales_unit_weight','DESC');
                        }])
                        ->whereIn('order_id',$data)->take(5)->get();
            }else if($request->type == 'Amount'){
                $items = OrderItem::whereIn('order_id',$data)->orderBy('gross_total','DESC')->take(5)->get();
            }
        }else if($request->order == 'invoice'){
            $items = [];
            $data = Invoice::whereIn('card_code', array_column($customers->toArray(), 'card_code'))
                ->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))->pluck('id')->toArray();
            if($request->type == 'Quantity'){
                $items = InvoiceItem::whereIn('invoice_id',$data)->orderBy('quantity','DESC')->take(5)->get();
            }else if($request->type == 'Liters'){
                $items = InvoiceItem::with(['product' => function ($q2) {
                        $q2->orderBy('sales_unit_weight','DESC');
                    }])
                    ->whereIn('invoice_id',$data)->take(5)->get();
            }else if($request->type == 'Amount'){
                $items = InvoiceItem::whereIn('invoice_id',$data)->orderBy('gross_total','DESC')->take(5)->get();
            }
        }else if($request->order == 'back_order'){
            $items = [];
            $data = OrderItem::with(['order','product' => function ($q1) {
                                $q1->orderBy('sales_unit_weight','DESC');
                            }])
                            ->where('remaining_open_quantity', '>', 0)
                            ->whereHas('order', function($q) use ($customers) {
                                $q->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                                $q->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
                            });                
            if($request->type == 'Quantity'){
                $items = $data->orderBy('quantity','DESC')->take(5)->get();
            }else if($request->type == 'Liters'){
                $items = $data->take(5)->get();
            }else if($request->type == 'Amount'){
                $items = $data->orderBy('gross_total','DESC')->take(5)->get();
            }            
        }
        $data = [];
        foreach($items as $key=>$val){
            if($request->type == 'Quantity'){
                $data[$key]['name'] = $val->item_code;
                $data[$key]['key'] = floor($val->quantity);
            }else if($request->type == 'Amount'){
                $data[$key]['name'] = $val->item_code;
                $data[$key]['key'] = floor($val->gross_total);
            }else if($request->type == 'Liters'){
                $data[$key]['name'] = $val->product->item_name;
                $data[$key]['key'] = floor($val->product->sales_unit_weight);
            }
        }
        $response = ['status' => true, 'data'=>$items,'data1'=>$data];
        return $response;
    }
}
