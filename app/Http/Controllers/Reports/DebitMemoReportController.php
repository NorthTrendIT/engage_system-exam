<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\SapConnection;

use DB;
use DataTables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DebitMemoReportExport;

class DebitMemoReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('report.debit-memo-report.index', compact('company'));
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
                            ->addColumn('name', function($row) {
                                $name = "<a href='".route('customer.show',@$row->id)."' target='_blank'>".@$row->card_name."</a>";
                                return  $name;
                            })
                            ->addColumn('card_code', function($row) {
                                return @$row->card_code ?? "-";
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->addColumn('open_amount', function($row) {
                                $amount = $row->debit_memo_reports()->where('u_class', '!=', 'Rebate')->sum('doc_total');
                                return '₱ '. number_format_value($amount);
                            })
                            ->addColumn('used_amount', function($row) {
                                $amount = $row->debit_memo_reports()->where('u_class', 'Rebate')->sum('doc_total');
                                return '₱ '. number_format_value($amount);
                            })
                            ->rawColumns(['action','status','name'])
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

            $open_amount = $value->debit_memo_reports()->where('u_class', '!=', 'Rebate')->sum('doc_total');
            $used_amount = $value->debit_memo_reports()->where('u_class', 'Rebate')->sum('doc_total');

            $records[] = array(
                            'no' => $key + 1,
                            'card_code' => $value->card_code ?? "-",
                            'card_name' => $value->card_name ?? "-",
                            'company' => @$value->sap_connection->company_name ?? "-",
                            'open_amount' => $open_amount,
                            'used_amount' => $used_amount,
                          );
        }
        if(count($records)){
            $title = 'Debit Memo Report '.date('dmY').'.xlsx';
            return Excel::download(new DebitMemoReportExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }

    public function getReportResultData($request){

        $data = Customer::has('debit_memo_reports')->with('debit_memo_reports')->orderBy('created_at', 'DESC');

        if(@$request->filter_customer != ""){
            $data->where(function($query) use ($request) {
                $query->orwhere(function($q1) use ($request){
                    $q1->where('id', $request->filter_customer);
                });

                $query->orwhere(function($q1) use ($request){
                    $q1->where('card_name','LIKE',"%".$request->filter_customer."%");
                });

            });
        }

        if(@$request->filter_brand != ""){
            $data->where(function($query) use ($request) {
                $que->whereHas('product_groups', function($q2) use ($request){
                    $q2->where('product_group_id', $request->filter_brand);
                });
            });
        }

        if(@$request->filter_sales_specialist != ""){
            $data->where(function($query) use ($request) {
                $que->whereHas('sales_specialist', function($q2) use ($request){
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
