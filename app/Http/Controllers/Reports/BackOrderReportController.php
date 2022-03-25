<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SapConnection;

use DB;
use DataTables;

class BackOrderReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $company = SapConnection::all();
        return view('report.back-order-report.index', compact('company'));
    }

    public function getAll(Request $request){
        $data = OrderItem::join('orders','orders.id','=', 'order_items.order_id')
                            ->join("products",function($join){
                                $join->on('products.item_code','=','order_items.item_code')->on('products.sap_connection_id','=', 'orders.sap_connection_id');
                            })
                            ->join("product_groups",function($join){
                                $join->on('product_groups.number','=','products.items_group_code')->on('product_groups.sap_connection_id','=', 'products.sap_connection_id');
                            })
                            ->join('sap_connections','sap_connections.id','=', 'orders.sap_connection_id')
                            ->where('orders.document_status', 'bost_Open')
                            ->where('orders.cancelled', 'No')
                            ->whereIn('orders.u_sostat', ['OP'])
                            ->select(
                                DB::raw("count(order_items.id) as total_id"),
                                DB::raw("sum(order_items.quantity) as total_quantity"),
                                DB::raw("sum(order_items.price) as total_price"),
                                DB::raw("sum(order_items.price_after_vat) as total_price_after_vat"),
                                'products.*',
                                'product_groups.group_name as brand',
                                'sap_connections.company_name as company',
                                // 'order_items.*',
                            )
                            ->orderBy('orders.id','DESC');


        if($request->filter_company != ""){
            $data->where('products.sap_connection_id',$request->filter_company);
        }

        if($request->filter_brand != ""){
            $data->where('products.items_group_code',$request->filter_brand);
        }

        if($request->filter_search != ""){
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

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('orders.doc_date', '>=' , $start);
            $data->whereDate('orders.doc_date', '<=' , $end);
        }


        if($request->filter_customer != "" || $request->filter_sales_specialist != ""){
            $data->join("customers",function($join){
                $join->on('customers.card_code','=','orders.card_code')->on('customers.sap_connection_id','=', 'orders.sap_connection_id');
            });
        }

        if($request->filter_customer != ""){
            $data->where('customers.id', $request->filter_customer);
        }

        if($request->filter_sales_specialist != ""){
            $data->join('customers_sales_specialists','customers_sales_specialists.customer_id','=', 'customers.id');
            $data->where('customers_sales_specialists.ss_id', $request->filter_sales_specialist);
        }

        $data->groupBy('order_items.item_code', 'products.sap_connection_id');

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
                            ->rawColumns(['status','action','total_price','total_price_after_vat'])
                            ->make(true);
    }

}
