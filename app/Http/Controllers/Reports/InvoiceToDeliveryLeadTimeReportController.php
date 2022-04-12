<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\SapConnection;

use DB;
use DataTables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoiceToDeliveryLeadTimeReportExport;

class InvoiceToDeliveryLeadTimeReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('report.invoice-to-delivery-lead-time-report.index', compact('company'));
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
                            ->addColumn('u_delivery', function($row) {
                                return date('M d, Y',strtotime(@$row->u_delivery));
                            })
                            ->addColumn('u_sostat', function($row) {
                                return @$row->u_sostat ?? "-";
                            })
                            ->addColumn('num_at_card', function($row) {
                                return @$row->num_at_card ?? "-";
                            })
                            ->addColumn('sales_specialist', function($row) {
                                return @$row->sales_specialist->sales_specialist_name ?? "-";
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->addColumn('lead_time', function($row) {
                                $startDate = date("Y-m-d", strtotime($row->created_at));
                                $endDate = $row->u_delivery;

                                $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
                                return $days ." Day(s)";
                            })
                            ->rawColumns(['action','status','name','lead_time'])
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

            $startDate = date("Y-m-d", strtotime($value->created_at));
            $endDate = $value->u_delivery;

            $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);

            $records[] = array(
                            'no' => $key + 1,
                            'customer_name' => @$value->customer->card_name ?? @$value->card_name ?? "-",
                            'business_unit' => @$value->sap_connection->company_name ?? "-",
                            'invoice_date' => @$value->doc_date ?? "-",
                            'invoice_no' => @$value->doc_num ?? "-",
                            'sales_specialist' => @$value->sales_specialist->sales_specialist_name ?? "-",
                            'u_delivery' => @$value->u_delivery ?? "-",
                            'u_sostat' => @$value->u_sostat ?? "-",
                            'num_at_card' => @$value->num_at_card ?? "-",
                            'lead_time' => $days ." Day(s)",
                          );
        }
        if(count($records)){
            $title = 'Invoice To Delivery Lead Time Report '.date('dmY').'.xlsx';
            return Excel::download(new InvoiceToDeliveryLeadTimeReportExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }

    public function getReportResultData($request){

        $data = Invoice::has('order')->whereNotNull('u_delivery')->orderby('doc_date', 'desc');

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

        if(@$request->filter_brand != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($que) use ($request) {
                    $que->whereHas('product_groups', function($q2) use ($request){
                        $q2->where('id', $request->filter_brand);
                    });
                });
            });
        }

        if(@$request->filter_sales_specialist != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('sales_specialist', function($q2) use ($request){
                    $q2->where('id', $request->filter_sales_specialist);
                });
            });
        }

        if(@$request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        return $data;
    }

    
}
