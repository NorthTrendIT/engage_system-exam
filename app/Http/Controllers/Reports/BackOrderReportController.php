<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SapConnection;

use DB;
use DataTables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BackOrderReportExport;

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
        $data = OrderItem::where('remaining_open_quantity', '>', 0)->orderBy('id','DESC');

        $data->whereHas('order', function($q){
            $q->where('document_status', 'bost_Open');
            $q->where('cancelled', 'No');
            $q->whereIn('u_sostat', ['OP']);
        });


        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_brand != ""){
            $data->whereHas('product.group', function($q) use ($request) {
                $q->where('items_group_code', $request->filter_brand);
            });
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereHas('order', function($q) use ($start, $end){
                $q->whereDate('doc_date', '>=' , $start);
                $q->whereDate('doc_date', '<=' , $end);
            });
        }

        if($request->filter_customer != ""){
            $data->whereHas('order.customer', function($q) use ($request) {
                $q->where('id', $request->filter_customer);
            });
        }

        if($request->filter_sales_specialist != ""){
            $data->whereHas('order.sales_specialist', function($q) use ($request) {
                $q->where('id', $request->filter_sales_specialist);
            });
        }

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('item_name', function($row) {
                                return @$row->product1->item_name ?? @$row->item_description ?? "-";
                            })
                            ->addColumn('item_code', function($row) {
                                return @$row->product1->item_code ?? @$row->item_code ?? "-";
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
                                    $price = @$row->price * @$row->quantity;
                                    $html = '₱ '.number_format_value(@$price, 2);
                                }
                                return $html;
                            })
                            ->addColumn('price_after_vat', function($row) {
                                $html = '₱ '. "0.00";
                                if(@$row->price_after_vat){
                                    $price = @$row->price_after_vat * @$row->quantity;
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
                            ->rawColumns(['status','action','price','price_after_vat'])
                            ->make(true);
    }


    public function export(Request $request){
        $filter = collect();
        if(@$request->data){
          $filter = json_decode(base64_decode($request->data));
        }

        $data = OrderItem::where('remaining_open_quantity', '>', 0)->orderBy('id','DESC');

        $data->whereHas('order', function($q){
            $q->where('document_status', 'bost_Open');
            $q->where('cancelled', 'No');
            $q->whereIn('u_sostat', ['OP']);
        });


        if(@$filter->filter_company != ""){
            $data->where('sap_connection_id',$filter->filter_company);
        }

        if(@$filter->filter_brand != ""){
            $data->whereHas('product.group', function($q) use ($filter) {
                $q->where('items_group_code', $filter->filter_brand);
            });
        }

        if(@$filter->filter_date_range != ""){
            $date = explode(" - ", $filter->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereHas('order', function($q) use ($start, $end){
                $q->whereDate('doc_date', '>=' , $start);
                $q->whereDate('doc_date', '<=' , $end);
            });
        }

        if(@$filter->filter_customer != ""){
            $data->whereHas('order.customer', function($q) use ($filter) {
                $q->where('id', $filter->filter_customer);
            });
        }

        if(@$filter->filter_sales_specialist != ""){
            $data->whereHas('order.sales_specialist', function($q) use ($filter) {
                $q->where('id', $filter->filter_sales_specialist);
            });
        }

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
}
