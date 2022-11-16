<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\SapConnection;

use DB;
use DataTables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OverdueSalesInvoiceReportExport;

use Auth;
use App\Models\User;
use App\Models\Role;

class OverdueSalesInvoiceReportController extends Controller
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
        return view('report.overdue-sales-invoice-report.index', compact('company','managers'));
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
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 1800);
        $data = $this->getReportResultData($request);

        $number_of_overdue_invoices = $data->count();
        $total_amount_of_overdue_invoices = $data->sum('doc_total');

        $table = DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('name', function($row) {                                
                                return  @$row->customer->card_name ?? @$row->card_name ?? "-";
                            })
                            ->addColumn('doc_entry', function($row) {
                                return $row->doc_entry;
                            })
                            ->addColumn('total', function($row) {
                                return '₱ '. number_format_value($row->doc_total);
                            })
                            ->addColumn('date', function($row) {
                                return date('M d, Y',strtotime($row->doc_date));
                            })
                            ->addColumn('due_date', function($row) {
                                return date('M d, Y',strtotime($row->doc_due_date));
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);


        $data = compact(
                        'table',
                        'number_of_overdue_invoices',
                        'total_amount_of_overdue_invoices',
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
                            'company' => @$value->sap_connection->company_name ?? "-",
                            'doc_entry' => $value->doc_entry ?? "-",
                            'customer' => @$value->customer->card_name ?? @$value->card_name ?? "-",
                            'doc_total' => $value->doc_total,
                            'created_at' => date('M d, Y',strtotime($value->doc_date)),
                          );
        }
        if(count($records)){
            $title = 'Overdue Sales Invoice Report '.date('dmY').'.xlsx';
            return Excel::download(new OverdueSalesInvoiceReportExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }

    public function getReportResultData($request){
        $date = date('Y-m-d', strtotime('-2 months'));

        $data = Invoice::orderBy('created_at', 'DESC');

        $data->where(function($query){
            $query->orwhere(function($q){
                $q->where('cancelled', '!=','No')->where('document_status', '!=', 'Cancelled');
            });

            $query->orwhere(function($q){
                $q->where('u_sostat', '!=','CM')->where('document_status', 'bost_Open');
            });
        });

        $data->whereDate('doc_date', '<=', $date);

        if(Auth::user()->role_id == 4){
            $data->where(function($query) use ($request) {
                $query->orwhereHas('customer', function($q1) use ($request){
                    $q1->where('id', Auth::id());
                });
            });
        }else{
            if($request->filter_customer != ""){
                $data->where(function($query) use ($request) {
                    $query->orwhereHas('customer', function($q1) use ($request){
                        $q1->where('id', $request->filter_customer);
                    });

                    $query->orwhere(function($q1) use ($request){
                        $q1->where('card_name','LIKE',"%".$request->filter_customer."%");
                    });

                });
            }
        }
        

        if($request->filter_brand != ""){
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

        if(@$request->filter_manager != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where(function($que) use ($request) {
                        $que->whereHas('sales_specialist', function($q2) use ($request){
                            $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                            $q2->whereIn('id', $salesAgent);
                        });
                    });
                });
            });
        }


        if(Auth::user()->role_id == 6){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where(function($que) use ($request) {
                        $que->whereHas('sales_specialist', function($q2) use ($request){
                            $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                            $q2->whereIn('id', $salesAgent);
                        });
                    });
                });
            });
        }

        if(Auth::user()->role_id == 2){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where(function($que) use ($request) {
                        $que->whereHas('sales_specialist', function($q2) use ($request){
                            $q2->where('id', Auth::id());
                        });
                    });
                });
            });
        }else{
            if($request->filter_sales_specialist != ""){
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
        }
        

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('doc_date', '>=' , $start);
            $data->whereDate('doc_date', '<=' , $end);
        }

        return $data;
    }
}
