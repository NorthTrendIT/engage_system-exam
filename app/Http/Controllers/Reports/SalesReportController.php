<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SapConnection;
use Auth;
use DB;
use DataTables;
use App\Models\Role;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

class SalesReportController extends Controller
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
        return view('report.sales-report.index', compact('company','managers'));
    }

    public function getAll(Request $request){
        
        $data = $this->getReportResultData($request);

        $dataArr = $data->get()->toArray();
        
        $grand_total_of_total_quantity = array_sum(array_column($dataArr, 'total_quantity'));
        $grand_total_of_total_price = '₱ '. number_format_value(array_sum(array_column($dataArr, 'total_price')));
        $grand_total_of_total_price_after_vat = '₱ '. number_format_value(array_sum(array_column($dataArr, 'total_price_after_vat')));
        
        $table = DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('item_name', function($row) {
                                return @$row->item_name ?? "-";
                            })
                            ->addColumn('item_code', function($row) {
                                return @$row->item_code ?? "-";
                            })
                            ->addColumn('brand', function($row) {
                                return @$row->brand ?? "-";
                            })
                            ->addColumn('company', function($row) {
                                return @$row->company ?? "-";
                            })
                            ->addColumn('total_quantity', function($row) {
                                return @$row->total_quantity ?? "-";
                            })
                            ->addColumn('total_price', function($row) {

                                $html = '₱ '. "0.00";
                                if(@$row->total_price){
                                    $html = '₱ '.number_format_value(@$row->total_price, 2);
                                }
                                return $html;
                            })
                            ->addColumn('total_price_after_vat', function($row) {

                                $html = '₱ '. "0.00";
                                if(@$row->total_price_after_vat){
                                    $html = '₱ '.number_format_value(@$row->total_price_after_vat, 2);
                                }
                                return $html;
                            })
                            ->rawColumns(['status','action','total_price','total_price_after_vat'])
                            ->make(true);

        $data = compact(
                        'table',
                        'grand_total_of_total_quantity',
                        'grand_total_of_total_price',
                        'grand_total_of_total_price_after_vat',
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
                            'item_name' => $value->item_name ?? "-",
                            'item_code' => $value->item_code ?? "-",
                            'brand' => @$value->brand ?? "-",
                            'company' => @$value->company ?? "-",
                            'total_quantity' => @$value->total_quantity ?? "-",
                            'total_price' => @$value->total_price ?? "-",
                            'total_price_after_vat' => @$value->total_price_after_vat ?? "-",
                          );
        }
        if(count($records)){
            $title = 'Sales Report '.date('dmY').'.xlsx';
            return Excel::download(new SalesReportExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }

    public function getReportResultData($request){
        $data = InvoiceItem::join('invoices', 'invoices.id','=','invoice_items.invoice_id')
                            ->join("products",function($join){
                                $join->on('products.item_code','=','invoice_items.item_code')->on('products.sap_connection_id','=', 'invoices.real_sap_connection_id');
                            })
                            ->join("product_groups",function($join){
                                $join->on('product_groups.number','=','products.items_group_code')->on('product_groups.sap_connection_id','=', 'products.sap_connection_id');
                            })
                            ->join('sap_connections','sap_connections.id','=', 'invoices.sap_connection_id')
                            ->where('invoices.document_status', 'bost_Open')
                            ->where('invoices.cancelled', 'No')
                            // ->whereIn('invoices.u_sostat', ['CM','IN'])
                            ->whereIn('invoices.u_sostat', ['CM'])
                            ->select(
                                DB::raw("count(invoice_items.id) as total_id"),
                                DB::raw("sum(invoice_items.quantity) as total_quantity"),
                                DB::raw("sum(invoice_items.price) as total_price"),
                                DB::raw("sum(invoice_items.price_after_vat) as total_price_after_vat"),
                                'products.*',
                                'product_groups.group_name as brand',
                                'sap_connections.company_name as company',
                                // 'invoice_items.*',
                            )
                            ->orderBy('invoices.id','DESC');

        if(@$request->filter_company != ""){
            $data->where('sap_connections.id',$request->filter_company);
        }

        if(@$request->filter_brand != ""){
            $data->where('products.items_group_code',$request->filter_brand);
        }

        if(@$request->filter_product_category != ""){
            $data->where('products.u_tires',$request->filter_product_category);
        }

        if(@$request->filter_product_line != ""){
            $data->where('products.u_item_line',$request->filter_product_line);
        }

        if(@$request->filter_product_class != ""){
            $data->where('products.item_class',$request->filter_product_class);
        }

        if(@$request->filter_product_type != ""){
            $data->where('products.u_item_type',$request->filter_product_type);
        }

        if(@$request->filter_product_application != ""){
            $data->where('products.u_item_application',$request->filter_product_application);
        }

        if(@$request->filter_product_pattern != ""){
            $data->where('products.u_pattern2',$request->filter_product_pattern);
        }

        if(@$request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('products.item_code','LIKE',"%".$request->filter_search."%");
                $q->orwhere('products.item_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('products.u_pattern2','LIKE',"%".$request->filter_search."%");
                $q->orwhere('products.u_item_application','LIKE',"%".$request->filter_search."%");
                $q->orwhere('products.u_item_type','LIKE',"%".$request->filter_search."%");
                $q->orwhere('products.u_tires','LIKE',"%".$request->filter_search."%");
                $q->orwhere('product_groups.group_name','LIKE',"%".$request->filter_search."%");
            });
        }

        if(@$request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('invoices.doc_date', '>=' , $start);
            $data->whereDate('invoices.doc_date', '<=' , $end);
        }

        if(@$request->filter_customer_class != "" || $request->filter_market_sector != "" || $request->filter_market_sub_sector != "" || $request->filter_sales_specialist != ""){
            $data->join("customers",function($join){
                $join->on('customers.card_code','=','invoices.card_code')->on('customers.sap_connection_id','=', 'invoices.sap_connection_id');
            });
        }

        if(@$request->filter_customer_class != ""){
            $data->where('customers.u_classification',$request->filter_customer_class);
        }

        if(@$request->filter_market_sector != ""){
            $data->where('customers.u_sector',$request->filter_market_sector);
        }

        if(@$request->filter_market_sub_sector != ""){
            $data->where('customers.u_subsector',$request->filter_market_sub_sector);
        }

        if(@$request->filter_sales_specialist != ""){
            $data->join('customers_sales_specialists','customers_sales_specialists.customer_id','=', 'customers.id');
            $data->where('customers_sales_specialists.ss_id',$request->filter_sales_specialist);
        }

        $data->groupBy('invoice_items.item_code', 'invoice_items.sap_connection_id');

        return $data;
    }
}
