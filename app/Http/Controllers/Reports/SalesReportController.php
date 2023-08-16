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
use App\Support\SAPInvoices;
use App\Models\Customer;
use Log;

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
        $title = 'Sales Report';
        return view('report.sales-report.index', compact('company','managers', 'title'));
    }

    public function getAll(Request $request){
        
        // $data = $this->getReportResultData($request);
        $data = $this->getInvoiceDataFromSap($request);

        // $dataArr = $data->get()->toArray();
        
        $grand_total_of_total_quantity = $data['grand_total_qty'];
        $grand_total_of_total_price = '₱ '. number_format($data['grand_total_price'], 2);
        $grand_total_of_total_price_after_vat = '₱ '. number_format($data['grand_total_price_after_vat'], 2);
        
        // $grand_total_of_total_quantity = array_sum(array_column($dataArr, 'total_quantity'));
        // $grand_total_of_total_price = '₱ '. number_format_value(array_sum(array_column($dataArr, 'total_price')));
        // $grand_total_of_total_price_after_vat = '₱ '. number_format_value(array_sum(array_column($dataArr, 'total_price_after_vat')));
        

        $table = DataTables::of($data['invoice_data'])
                            ->addIndexColumn()
                            ->addColumn('invoice_no', function($row) {
                                return $row['DocNum'] ?? '-';
                            })
                            ->addColumn('invoice_date', function($row) {
                                return $row['DocDate'] ?? '-';
                            })
                            ->addColumn('item_code', function($row) {
                                return $row['ItemCode'] ?? "-";
                            })
                            ->addColumn('item_name', function($row) {
                                return $row['ItemDescription'] ?? "-";
                            })
                            ->addColumn('brand', function($row) {
                                return $row['Brand'] ?? "-";
                            })
                            ->addColumn('company', function($row) use ($data) {
                                return $data['db_name'] ?? "-";
                            })
                            ->addColumn('total_quantity', function($row) {
                                return $row['Quantity'] ?? "-";
                            })
                            // ->addColumn('total_price', function($row) {

                            //     $html = '₱ '. "0.00";
                            //     // if(@$row->total_price){
                            //     //     $html = '₱ '.number_format_value(@$row->total_price, 2);
                            //     // }
                            //     return $html;
                            // })
                            // ->addColumn('total_price_after_vat', function($row) {

                            //     $html = '₱ '. "0.00";
                            //     // if(@$row->total_price_after_vat){
                            //     //     $html = '₱ '.number_format_value(@$row->total_price_after_vat, 2);
                            //     // }
                            //     return $html;
                            // })
                            // ->addColumn('total_amount', function($row) {
                            //     $html = '₱ '. "0.00";
                            //     // if(@$row->total_price_after_vat){
                            //     //     $price = @$row->quantity * $row->total_price_after_vat;
                            //     //     $html = '₱ '.number_format_value(@$price, 2);
                            //     // }
                            //     return $html;
                            // })
                            ->addColumn('uom', function($row) {
                                return $row['UoM'] ?? '-';
                            })
                            ->addColumn('item_price', function($row) {
                                return number_format($row['Price'], 2);
                            })
                            ->addColumn('net_amount', function($row) {
                                return number_format($row['GrossTotal'], 2);
                            })
                            ->addColumn('status', function($row) {
                                return $row['Status'] ?? '-';
                            })
                            ->rawColumns(['status'])
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

        // $data = $this->getReportResultData($filter);
        // $data = $data->get();

        $data = $this->getInvoiceDataFromSap($filter);

        $records = array();
        foreach($data['invoice_data'] as $key => $value){

            $records[] = array(
                            'no' => $key + 1,
                            'invoice_num' => $value['DocNum'] ?? "-",
                            'date' => $value['DocDate'] ?? "-",
                            'product_code' => @$value['ItemCode'] ?? "-",
                            'product_name' => @$value['ItemDescription'] ?? "-",
                            'brand' => @$value['Brand'] ?? "-",
                            'business_unit' => @$data['db_name'] ?? "-",
                            'total_qty' => @$value['Quantity'] ?? "-",
                            'uom' => @$value['UoM'] ?? "-",
                            'unit_price' => @$value['Price'] ?? "-",
                            'net_amount' => @$value['GrossTotal'] ?? "-",
                            'status' => @$value['Status'] ?? "-",
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
                            //->whereNotNull('invoices.u_omsno')
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

        if($request->engage_transaction != 0){
            $data->whereNotNull('invoices.u_omsno');
        }

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

        if(@$request->filter_customer_class != "" || @$request->filter_market_sector != "" || @$request->filter_market_sub_sector != "" || @$request->filter_sales_specialist != ""){
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

    public function getInvoiceDataFromSap($request){

        $url = '/b1s/v1/$crossjoin(Invoices, Invoices/DocumentLines)?$expand=Invoices($select=DocEntry, DocNum, DocDate, DocumentStatus, Cancelled), Invoices/DocumentLines($select=ItemCode, ItemDescription, CostingCode2, Quantity, MeasureUnit, Price, GrossTotal, PriceAfterVAT)';
        $limit = '&$top=100&$orderby=DocDate desc';
        $filter = '&$filter=Invoices/DocEntry eq Invoices/DocumentLines/DocEntry and';
        $filter_length = strlen($filter);
        $sap_connection = (object)[];

        if($request->filter_company != '' && $request->filter_customer == ''){
            $sap_connection = SapConnection::find($request->filter_company);
        }else{
            $customer = Customer::find($request->filter_customer);
            $sap_connection = $customer->sap_connection;

            $and = (substr($filter, $filter_length) === '') ? '' : ' and';
            $filter .= $and.' Invoices/CardCode eq \''.$customer->card_code.'\'';
        }

        if(@$request->filter_search != ""){
            $and = (substr($filter, $filter_length) === '') ? '' : ' and';
            $filter .= $and.' Invoices/DocNum eq '.$request->filter_search;
        }

        if(@$request->filter_brand != ""){
            $and = (substr($filter, $filter_length) === '') ? '' : ' and';
            $filter .= $and.' Invoices/DocumentLines/CostingCode2 eq \''.$request->filter_brand.'\'';
        }

        if(@$request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));
            
            $and = (substr($filter, $filter_length) === '') ? '' : ' and';
            $filter .= $and.' Invoices/DocDate ge \''.$start.'\' and Invoices/DocDate le \''.$end.'\'';
        }

        if($request->overdue === 'Yes'){
            $today = date("Y-m-d");
            $status = 'O';
            $cancelled = 'N';
            $and = (substr($filter, $filter_length) === '') ? '' : ' and';
            $filter .= $and.' Invoices/DocDueDate lt \''.$today.'\' and DocumentStatus eq \''.$status.'\' and Cancelled eq \''.$cancelled.'\'';
        }

        $filter = (substr($filter, $filter_length) === '') ? '' : $filter ;
        $filter_limit  = ($filter === '' || ($request->filter_customer == "" && $request->filter_brand != "")) ? $filter.$limit : str_replace('&$top=100','',$filter);
        $url = $url.$filter_limit;
        
        $sap_invoices = new SAPInvoices($sap_connection->db_name, $sap_connection->user_name, $sap_connection->password);
        $sap_invoices->fetchInvoiceDataForReportingV2($url);
        
        return ['invoice_data' => $sap_invoices->invoice_data,
                'db_name'      => $sap_connection->db_name, 
                'grand_total_qty' => number_format($sap_invoices->grand_total_qty), 
                'grand_total_price' => $sap_invoices->grand_total_price,
                'grand_total_price_after_vat' => $sap_invoices->grand_total_price_after_vat ];
    }
}
