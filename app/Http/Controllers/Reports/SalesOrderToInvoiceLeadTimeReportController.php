<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\SapConnection;

use DB;
use DataTables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesOrderToInvoiceLeadTimeReportExport;

use Auth;
use App\Models\User;
use App\Models\Role;

class SalesOrderToInvoiceLeadTimeReportController extends Controller
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
        return view('report.sales-order-to-invoice-lead-time-report.index', compact('company','managers'));
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

    public function getAll(Request $request){
        
        $data = $this->getReportResultData($request);

        $table = DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('card_name', function($row) {
                                return @$row->customer->card_name ?? @$row->card_name ?? "-";
                            })
                            ->addColumn('card_code', function($row) {
                                return @$row->customer->card_code ?? @$row->card_code ?? "-";
                            })
                            ->addColumn('invoice_date', function($row) {
                                return date('M d, Y',strtotime(@$row->doc_date));
                            })
                            ->addColumn('invoice_doc_num', function($row) {
                                return @$row->doc_num ?? "-";
                            })
                            ->addColumn('order_date', function($row) {
                                return date('M d, Y',strtotime(@$row->order->doc_date));
                            })
                            ->addColumn('order_doc_num', function($row) {
                                return @$row->order->doc_num ?? "-";
                            })
                            ->addColumn('sales_specialist', function($row) {
                                return @$row->sales_specialist->sales_specialist_name ?? "-";
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->addColumn('lead_time', function($row) {
                                $endDate = $row->created_at;
                                $startDate = $row->order->created_at;

                                 $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
                                return $days ." Day(s)";
                            })
                            ->rawColumns(['lead_time'])
                            ->make(true);


        $data = compact(
                        'table',
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

            $endDate = $value->created_at;
            $startDate = $value->order->created_at;

            $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);

            $records[] = array(
                            'no' => $key + 1,
                            'customer_name' => @$value->customer->card_name ?? @$value->card_name ?? "-",
                            'business_unit' => @$value->sap_connection->company_name ?? "-",
                            'order_date' => @$value->order->doc_date ?? "-",
                            'order_no' => @$value->order->doc_num ?? "-",
                            'sales_specialist' => @$value->sales_specialist->sales_specialist_name ?? "-",
                            'invoice_date' => @$value->doc_date ?? "-",
                            'invoice_no' => @$value->doc_num ?? "-",
                            'lead_time' => $days ." Day(s)",
                          );
        }
        if(count($records)){
            $title = 'Sales Order To Invoice Lead Time Report '.date('dmY').'.xlsx';
            return Excel::download(new SalesOrderToInvoiceLeadTimeReportExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }

    public function getReportResultData($request){

        $data = Invoice::has('order')->orderby('doc_date', 'desc');

        if($request->engage_transaction != 0){
            $data->whereNotNull('u_omsno');
        }

        if(Auth::user()->role_id == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        }else{
            if(@$request->filter_customer != ""){
                $data->where(function($q) use ($request) {
                    $q->orwhereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });

                    $q->orwhere(function($q1) use ($request){
                        $q1->where('card_name','LIKE',"%".$request->filter_customer."%");
                    });
                });
            }
        }

        if(@$request->filter_brand != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($que) use ($request) {
                    $que->whereHas('product_groups', function($q2) use ($request){
                        $q2->where('id', $request->filter_brand);
                    });
                });
            });
        }

        if(Auth::user()->role_id == 2){
            $data->where(function($query) use ($request) {
                $query->whereHas('sales_specialist', function($q2) use ($request){
                    $q2->where('id', Auth::id());
                });
            });
        }else{
            if(@$request->filter_sales_specialist != ""){
                $data->where(function($query) use ($request) {
                    $query->whereHas('sales_specialist', function($q2) use ($request){
                        $q2->where('id', $request->filter_sales_specialist);
                    });
                });
            }
        }

        if(@$request->filter_manager != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('sales_specialist', function($q2) use ($request){
                    $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                    $q2->whereIn('id', $salesAgent);
                });
            });
        }

        if(Auth::user()->role_id == 6){
            $data->where(function($query) use ($request) {
                $query->whereHas('sales_specialist', function($q2) use ($request){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $q2->whereIn('id', $salesAgent);
                });
            });
        }

        // Date Range Filter
        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));
            $totalPending->whereDate('doc_date', '>=' , $start);
            $totalPending->whereDate('doc_date', '<=' , $end);
        }

        if(@$request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        return $data;
    }

    
}
