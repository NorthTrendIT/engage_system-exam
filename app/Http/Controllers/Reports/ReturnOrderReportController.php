<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\SapConnection;
use App\Models\Customer;

use DB;
use DataTables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReturnOrderReportExport;

use Auth;
use App\Models\User;
use App\Models\Role;

class ReturnOrderReportController extends Controller
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
        return view('report.return-order-report.index', compact('company','managers'));
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
        $grand_total_of_quantity = $data->sum('quantity');
        $grand_total_of_amount = '₱ '. number_format_value($data->sum('price_after_vat'));

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
                            // ->addColumn('doc_entry', function($row) {
                            //     $html = @$row->doc_entry ?? "-";

                            //     if(!empty($row->invoice)){
                            //         $html = "<a href='".route('invoices.show',@$row->invoice->id)."' target='_blank' title='View Invoice'>".@$row->doc_entry."</a>";
                            //     }
                            //     return $html;
                            // })
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
                        'grand_total_of_quantity',
                        'grand_total_of_amount',
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
                            'return_date' => @$value->credit_note->doc_date ?? "-",
                            'return_no' => @$value->credit_note->doc_num ?? "-",
                            'sales_specialist' => @$value->credit_note->sales_specialist->sales_specialist_name ?? "-",
                            'return_amount' => @$value->credit_note->doc_total ?? "0.00",
                            'item_code' => $value->item_code ?? "-",
                            'item_description' => $value->item_description ?? "-",
                            'item_price' => $value->price_after_vat ?? "-",
                            'qty_returned' => $value->quantity ?? "-",
                            'remarks' => @$value->credit_note->comments ?? "-",
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
        $data = CreditNoteItem::orderBy('credit_note_id', 'DESC');
        $data->whereHas('credit_note', function($q){
            $q->where('doc_type', 'dDocument_Items')->where('u_class', 'RETURNS');
        });

        if(Auth::user()->role_id == 4){
            $customers = Auth::user()->get_multi_customer_details();
            $data->where(function($query) use ($customers) {
                $query->whereHas('credit_note', function($q) use ($customers) {
                        $q->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                        $q->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
                });
            });
        }else{
            if(@$request->filter_customer != ""){
                $customers = Customer::where('id',$request->filter_customer)->first();
                $data->where(function($query) use ($customers) {
                    $query->whereHas('credit_note', function($q) use ($customers) {
                        $q->where('card_code', $customers->card_code);

                        // $q->orwhereHas('customer',function($q1) use ($request){
                        //     $q1->where('card_name','LIKE',"%".$request->filter_customer."%");
                        // });
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

        if(@$request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        /*if($request->engage_transaction != 0){
            $data->whereNotNull('u_omsno');
        }*/

        return $data;
    }

    
}
