<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CreditNote;
use App\Models\SapConnection;

use DB;
use DataTables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReturnOrderReportExport;

class ReturnOrderReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('report.return-order-report.index', compact('company'));
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
                                return @$row->card_name ?? "-";
                            })
                            ->addColumn('card_code', function($row) {
                                return @$row->card_code ?? "-";
                            })
                            ->addColumn('date', function($row) {
                                return date('M d, Y',strtotime($row->doc_date));
                            })
                            ->addColumn('doc_entry', function($row) {
                                $html = @$row->doc_entry ?? "-";

                                if(!empty($row->invoice)){
                                    $html = "<a href='".route('invoices.show',@$row->invoice->id)."' target='_blank' title='View Invoice'>".@$row->doc_entry."</a>";
                                }
                                return $html;
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->addColumn('total_quantity', function($row) {
                                return $row->items()->sum('quantity');
                            })
                            ->addColumn('total_amount', function($row) {
                                $amount = $row->items()->sum('gross_total');
                                return '₱ '. number_format_value($amount);
                            })
                            // ->addColumn('total_price', function($row) {
                            //     $amount = $row->items()->sum('price');
                            //     return '₱ '. number_format_value($amount);
                            // })
                            // ->addColumn('total_price_after_vat', function($row) {
                            //     $amount = $row->items()->sum('price_after_vat');
                            //     return '₱ '. number_format_value($amount);
                            // })
                            ->rawColumns(['action','status','name','doc_entry'])
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

            $total_quantity = $value->items()->sum('quantity');
            $total_amount = $value->items()->sum('gross_total');

            $records[] = array(
                            'no' => $key + 1,
                            'doc_entry' => $value->doc_entry ?? "-",
                            'card_code' => $value->card_code ?? "-",
                            'card_name' => $value->card_name ?? "-",
                            'company' => @$value->sap_connection->company_name ?? "-",
                            'total_quantity' => $total_quantity,
                            'total_amount' => $total_amount,
                            'date' => $value->doc_date,
                          );
        }
        if(count($records)){
            $title = 'Return Order Report '.date('dmY').'.xlsx';
            return Excel::download(new ReturnOrderReportExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }

    public function getReportResultData($request){

        $data = CreditNote::where('doc_type', 'dDocument_Items')->orderBy('doc_date', 'DESC');

        if(@$request->filter_customer != ""){
            $data->where(function($query) use ($request) {
                $query->orwhereHas('customer', function($q1) use ($request){
                    $q1->where('id', $request->filter_customer);
                });

                $query->orwhere(function($q1) use ($request){
                    $q1->where('card_name','LIKE',"%".$request->filter_customer."%");
                });
            });
        }

        if(@$request->filter_brand != ""){
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

        if(@$request->filter_sales_specialist != ""){
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

        if(@$request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        return $data;
    }

    
}
