<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPInvoices;
use App\Support\SAPOrders;
use App\Support\SAPQuotations;
use App\Jobs\SyncInvoices;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SapConnection;
use DataTables;
use Auth;

class InvoicesController extends Controller
{
    public function __construct(){

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = collect();
        if(userrole() == 1){
            $company = SapConnection::all();
        }
        return view('invoices.index', compact('company'));
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
        $total = 0;
        $data = Invoice::where('id', $id);
        if(userrole() == 4){
            $data->where('card_code', @Auth::user()->customer->card_code);
        }elseif(userrole() == 2){
            $data->where('sales_person_code', @Auth::user()->sales_employee_code);
        }elseif(userrole() != 1){
            return abort(404);
        }

        $data = $data->firstOrFail();

        return view('invoices.view', compact('data'));
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

    public function syncInvoices(){
        try {

            $sap_connections = SapConnection::all();
            foreach ($sap_connections as $value) {
                $invoice_log_id = add_sap_log([
                                        'ip_address' => userip(),
                                        'activity_id' => 36,
                                        'user_id' => userid(),
                                        'data' => null,
                                        'type' => "S",
                                        'status' => "in progress",
                                        'sap_connection_id' => $value->id,
                                    ]);

                SyncInvoices::dispatch($value->db_name, $value->user_name , $value->password, $invoice_log_id);
            }

            $response = ['status' => true, 'message' => 'Sync invoices details successfully !'];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    public function getAll(Request $request){
        $data = Invoice::query();

        if(userrole() == 4){
            $data->where('card_code', @Auth::user()->customer->card_code);
        }elseif(userrole() == 2){
            $data->where('sales_person_code', @Auth::user()->sales_employee_code);
        }elseif(userrole() != 1){
            if (!is_null(@Auth::user()->created_by)) {
                $data->where('card_code', @Auth::user()->created_by_user->customer->card_code);
            } else {
                return DataTables::of(collect())->make(true);;
            }
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                // $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('doc_type','LIKE',"%".$request->filter_search."%");
                $q->orwhere('doc_entry','LIKE',"%".$request->filter_search."%");
            });
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

        if($request->filter_class != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('u_class', $request->filter_class);
                });
            });
        }

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

        if($request->filter_market_sector != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('u_sector', $request->filter_market_sector);
                });
            });
        }

        if($request->filter_territory != ""){
            $data->where(function($query) use ($request) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('territory', $request->filter_territory);
                });
            });
        }

        if($request->filter_customer != ""){
            $data->where('card_code',$request->filter_customer);
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_status != ""){
            $status = $request->filter_status;

            if($status == "CL"){ //Cancel
                $data->where('cancelled', 'Yes');

            }elseif($status == "PN"){ //Pending

                // $data->where('cancelled', 'No');

                $data->where(function($query){
                    $query->orwhere(function($q){
                        $q->whereNull('u_sostat');
                    });

                    $query->orwhere(function($q1){
                        $q1->where('cancelled', 'No')->whereNotIn('u_sostat', array_keys(getOrderStatusArray()));
                    });
                });

            }else{
                $data->where('document_status', 'bost_Open')->where('u_sostat', $status);
            }
        }


        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('doc_date', '>=' , $start);
            $data->whereDate('doc_date', '<=' , $end);
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        // dd($data->get());
        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('name', function($row) {
                                return  @$row->customer->card_name ?? @$row->card_name ?? "-";
                            })
                            ->addColumn('status', function($row) {
                                return getOrderStatusByInvoice($row);
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
                            ->addColumn('action', function($row){
                                $btn = '<a href="' . route('invoices.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>';
                                return $btn;
                            })
                            ->orderColumn('name', function ($query, $order) {
                                //$query->orderBy('card_name', $order);

                                $query->select('invoices.*')->join('customers', 'invoices.card_code', '=', 'customers.card_code')
                                    ->orderBy('customers.card_name', $order);
                            })
                            ->orderColumn('doc_entry', function ($query, $order) {
                                $query->orderBy('doc_entry', $order);
                            })
                            ->orderColumn('total', function ($query, $order) {
                                $query->orderBy('doc_total', $order);
                            })
                            ->orderColumn('date', function ($query, $order) {
                                $query->orderBy('doc_date', $order);
                            })
                            ->orderColumn('due_date', function ($query, $order) {
                                $query->orderBy('doc_due_date', $order);
                            })
                            ->orderColumn('company', function ($query, $order) {
                                $query->join('sap_connections', 'invoices.sap_connection_id', '=', 'sap_connections.id')->orderBy('sap_connections.company_name', $order);
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }

    public function getCustomer(Request $request){
        return app(CustomerPromotionController::class)->getCustomer($request);
    }

    public function syncSpecificInvoice(Request $request){

        $response = ['status' => false, 'message' => 'Something went wrong !'];
        if(@$request->id){

            $invoice = Invoice::find($request->id);
            if(!is_null($invoice) && !is_null(@$invoice->sap_connection)){
                try {

                    $sap_connection = @$invoice->sap_connection;

                    // Sync Invoice Data
                    $sap_invoices = new SAPInvoices($sap_connection->db_name, $sap_connection->user_name, $sap_connection->password);
                    $sap_invoices->addSpecificInvoicesDataInDatabase(@$invoice->doc_entry);

                    // Sync Order Data
                    if(@$invoice->order->doc_entry){
                        $sap_orders = new SAPOrders($sap_connection->db_name, $sap_connection->user_name, $sap_connection->password);
                        $sap_orders->addSpecificOrdersDataInDatabase(@$invoice->order->doc_entry);
                    }

                    // Sync Quotation Data
                    if(@$invoice->order->base_entry){
                        $sap_quotations = new SAPQuotations($sap_connection->db_name, $sap_connection->user_name, $sap_connection->password);
                        $sap_quotations->addSpecificQuotationsDataInDatabase(@$invoice->order->base_entry);
                    }

                    $response = ['status' => true, 'message' => 'Sync invoice details successfully !'];
                } catch (\Exception $e) {
                    $response = ['status' => false, 'message' => 'Something went wrong !'];
                }
            }

        }
        return $response;
    }
}
