<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SapConnection;
use App\Models\Product;
use App\Models\CustomerPromotion;
use App\Models\InvoiceItem;
use DB;
use DataTables;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductSalesReportExport;

class ProductSalesReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('report.product-sales-report.index', compact('company'));
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
        //
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

    public function getAll(Request $request){
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
                    ->whereIn('invoices.u_sostat', ['CM'])
                    ->select(
                        DB::raw("count(invoice_items.id) as total_id"),
                        DB::raw("sum(invoice_items.quantity) as total_quantity"),
                        DB::raw("sum(invoice_items.price) as total_price"),
                        DB::raw("sum(invoice_items.price_after_vat) as total_price_after_vat"),
                        'products.*',
                        'product_groups.group_name as brand',
                        'sap_connections.company_name as company',
                        'invoice_items.*',
                    )
                    ->where('invoice_items.ship_date', '>=', Carbon::now()->subDays(60)->toDateTimeString())
                    ->orderBy('invoice_items.ship_date','DESC');


        if($request->filter_company != ""){
            $data->where('sap_connections.id',$request->filter_company);
        }

        if($request->filter_brand != ""){
            $data->where('products.items_group_code',$request->filter_brand);
        }

        if($request->filter_product_category != ""){
            $data->where('products.u_tires',$request->filter_product_category);
        }

        if($request->filter_product_line != ""){
            $data->where('products.u_item_line',$request->filter_product_line);
        }

        if($request->filter_product_class != ""){
            $data->where('products.item_class',$request->filter_product_class);
        }

        if($request->filter_product_type != ""){
            $data->where('products.u_item_type',$request->filter_product_type);
        }

        if($request->filter_product_application != ""){
            $data->where('products.u_item_application',$request->filter_product_application);
        }

        if($request->filter_product_pattern != ""){
            $data->where('products.u_pattern2',$request->filter_product_pattern);
        }

        $data->groupBy('invoice_items.item_code', 'invoice_items.sap_connection_id');

        $data = $data->get();

        return DataTables::of($data)
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
                            ->addColumn('quantity', function($row) {
                                return @$row->quantity ?? "-";
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
                            ->rawColumns(['total_price','total_price_after_vat'])
                            ->make(true);
    }

    public function export(Request $request){
        $filter = collect();

        if(@$request->data){
          $filter = json_decode(base64_decode($request->data));
        }

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
                        'invoice_items.*',
                    )
                    ->where('invoice_items.ship_date', '>=', Carbon::now()->subDays(60)->toDateTimeString())
                    ->orderBy('invoice_items.ship_date','DESC');


        if($request->filter_company != ""){
            $data->where('sap_connections.id',$request->filter_company);
        }

        if($request->filter_brand != ""){
            $data->where('products.items_group_code',$request->filter_brand);
        }

        if($request->filter_product_category != ""){
            $data->where('products.u_tires',$request->filter_product_category);
        }

        if($request->filter_product_line != ""){
            $data->where('products.u_item_line',$request->filter_product_line);
        }

        if($request->filter_product_class != ""){
            $data->where('products.item_class',$request->filter_product_class);
        }

        if($request->filter_product_type != ""){
            $data->where('products.u_item_type',$request->filter_product_type);
        }

        if($request->filter_product_application != ""){
            $data->where('products.u_item_application',$request->filter_product_application);
        }

        if($request->filter_product_pattern != ""){
            $data->where('products.u_pattern2',$request->filter_product_pattern);
        }

        $data->groupBy('invoice_items.item_code', 'invoice_items.sap_connection_id');

        $data = $data->get();

        $headers = array(
            'No',
            'Business Unit',
            'Product Code',
            'Product Name',
            'Brand',
            'Total Quantity',
            'Total Price',
            'Total Price After VAT',
        );

        $records = array();

        foreach($data as $key => $value){
            $temp = array(
                    'no' => $key + 1,
                    'company' => @$value->company ?? "-",
                    'item_code' => @$value->item_code ?? "-",
                    'item_name' => @$value->item_name ?? "-",
                    'brand' => @$value->brand ?? "",
                    'total_quantity' => @$value->quantity ?? "-",
                    'total_price' => @$value->total_price ?? "-",
                    'total_price_after_vat' => @$value->total_price_after_vat ?? "-",
                );

            $records[] = $temp;
        }


        if(count($records)){
            $title = 'Product Sales Report '.date('dmY').'.xlsx';
            return Excel::download(new ProductSalesReportExport($records, $headers), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }
}
