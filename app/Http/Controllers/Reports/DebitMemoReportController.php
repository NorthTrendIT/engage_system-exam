<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CreditNoteItem;
use App\Models\SapConnection;

use DB;
use DataTables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DebitMemoReportExport;

use Auth;
use App\Models\User;
use App\Models\Role;

class DebitMemoReportController extends Controller
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
        return view('report.debit-memo-report.index', compact('company','managers'));
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

        $doc_totals = array_value_recursive('doc_total', $data->get()->toArray());
        $doc_totals = (array)$doc_totals;
        if($doc_totals != null){
            $grand_total_of_amount = '₱ '. number_format_value(
                                            array_sum(
                                                @$doc_totals
                                            )
                                        );
        }else{
            $grand_total_of_amount = '₱ 0.00';
        }
        $grand_total_of_price_after_vat = '₱ '. number_format_value($data->sum('price_after_vat'));
        $grand_total_of_gross_total = '₱ '. number_format_value($data->sum('gross_total'));

        $table = DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('card_name', function($row) {
                                return @$row->credit_note->customer->card_name ?? @$row->card_name ?? "-";
                            })
                            ->addColumn('card_code', function($row) {
                                return @$row->credit_note->customer->card_code ?? @$row->card_code ?? "-";
                            })
                            ->addColumn('date', function($row) {
                                return date('M d, Y',strtotime(@$row->credit_note->doc_date));
                            })
                            ->addColumn('doc_num', function($row) {
                                return @$row->credit_note->doc_num ?? "-";
                            })
                            ->addColumn('sales_specialist', function($row) {
                                return @$row->credit_note->sales_specialist->sales_specialist_name ?? "-";
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->addColumn('doc_total', function($row) {
                                $amount = @$row->credit_note->doc_total ?? "0.00";
                                return '₱ '. number_format_value($amount);
                            })
                            ->addColumn('price_after_vat', function($row) {
                                $amount = @$row->price_after_vat ?? "0.00";
                                return '₱ '. number_format_value($amount);
                            })
                            ->addColumn('gross_total', function($row) {
                                $amount = @$row->gross_total ?? "0.00";
                                return '₱ '. number_format_value($amount);
                            })
                            ->addColumn('quantity', function($row) {
                                return @$row->quantity ?? "0.00";
                            })
                            ->addColumn('comments', function($row) {
                                return @$row->credit_note->comments ?? "-";
                            })
                            ->rawColumns(['action','status','name','doc_entry'])
                            ->make(true);


        $data = compact(
                        'table',
                        'grand_total_of_amount',
                        'grand_total_of_price_after_vat',
                        'grand_total_of_gross_total',
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
                            'customer_name' => @$value->credit_note->customer->card_name ?? @$value->card_name ?? "-",
                            'business_unit' => @$value->sap_connection->company_name ?? "-",
                            'date' => @$value->credit_note->doc_date ?? "-",
                            'document_no' => @$value->credit_note->doc_num ?? "-",
                            'sales_specialist' => @$value->credit_note->sales_specialist->sales_specialist_name ?? "-",
                            'amount' => @$value->credit_note->doc_total ?? "0.00",
                            'item_description' => $value->item_description ?? "-",
                            'item_price' => $value->price_after_vat ?? "-",
                            'gross_total' => $value->gross_total ?? "-",
                            'remarks' => @$value->credit_note->comments ?? "-",
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

        $data = CreditNoteItem::with('credit_note')->orderBy('credit_note_id', 'DESC');

        $data->whereHas('credit_note', function($q){
            $q->where('doc_type', 'dDocument_Service')->where('document_status', 'bost_Open')->where('doc_total', '<', 0);
        });

        if(Auth::user()->role_id == 4){
            $data->where(function($query) use ($request) {
                $query->whereHas('credit_note', function($q) use ($request) {
                    $q->orwhereHas('customer', function($q1) use ($request){
                        $q1->where('id', Auth::id());
                    });
                });
            });

        }else{
            if(@$request->filter_customer != ""){
                $data->where(function($query) use ($request) {
                    $query->whereHas('credit_note', function($q) use ($request) {
                        $q->orwhereHas('customer', function($q1) use ($request){
                            $q1->where('id', $request->filter_customer);
                        });

                        $q->orwhere(function($q1) use ($request){
                            $q1->where('card_name','LIKE',"%".$request->filter_customer."%");
                        });
                    });
                });
            }
        }

        

        if(@$request->filter_brand != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('product1', function($que) use ($request) {
                    $que->whereHas('group', function($q2) use ($request){
                        $q2->where('id', $request->filter_brand);
                    });
                });
            });
        }

        if(Auth::user()->role_id == 2){
            $data->where(function($query) use ($request) {
                $query->whereHas('credit_note', function($q) use ($request) {
                    $q->whereHas('sales_specialist', function($q2) use ($request){
                        $q2->where('id', Auth::id());
                    });
                });
            });
        }else{
            if(@$request->filter_sales_specialist != ""){
                $data->where(function($query) use ($request) {
                    $query->whereHas('credit_note', function($q) use ($request) {
                        $q->whereHas('sales_specialist', function($q2) use ($request){
                            $q2->where('id', $request->filter_sales_specialist);
                        });
                    });
                });
            }
        }

        if(@$request->filter_manager != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('credit_note', function($q) use ($request) {
                    $q->whereHas('sales_specialist', function($q2) use ($request){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $q2->whereIn('id', $salesAgent);
                    });
                });
            });
        }

        if(Auth::user()->role_id == 6){
            $data->where(function($query) use ($request) {
                $query->whereHas('credit_note', function($q) use ($request) {
                    $q->whereHas('sales_specialist', function($q2) use ($request){
                        $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                        $q2->whereIn('id', $salesAgent);
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
