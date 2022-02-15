<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPCustomer;
use App\Jobs\SyncCustomers;
use App\Models\Customer;
use App\Models\Classes;
use App\Models\CustomerGroup;
use App\Models\SapConnection;
use App\Models\CustomerBpAddress;
use App\Models\Territory;
use DataTables;
use Auth;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerExport;

class CustomerController extends Controller
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
        return view('customer.index');
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
        $data = Customer::where('id',$id);

        if(userrole() != 4){
            if(in_array(userrole(),[2])){
                $data->whereHas('sales_specialist', function($q) {
                    return $q->where('ss_id',Auth::id());
                });
            }
        }

        $data = $data->firstOrFail();

        return view('customer.view',compact('data'));
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

    public function syncCustomers(){
        try {

            // Add Sync Customer data log.
            // add_log(15, null);

            $sap_connections = SapConnection::all();

            foreach ($sap_connections as $value) {

                $log_id = add_sap_log([
                                'ip_address' => userip(),
                                'activity_id' => 15,
                                'user_id' => userid(),
                                'data' => null,
                                'type' => "S",
                                'status' => "in progress",
                                'sap_connection_id' => $value->id,
                            ]);


                // Save Data of customer in database
                SyncCustomers::dispatch($value->db_name, $value->user_name , $value->password, $log_id);
            }
            // // Save Data of customer in database
            // SyncCustomers::dispatch('TEST-APBW', 'manager', 'test');

            $response = ['status' => true, 'message' => 'Sync Customer successfully !'];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Customer::whereHas('group',function($q){
                            $q->where('name','!=','EMPLOYEE');
                        });

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_territory != ""){
            $data->where('territory',$request->filter_territory);
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_market_sector != ""){
            $data->where('u_msec',$request->filter_market_sector);
        }

        if($request->filter_market_sub_sector != ""){
            $data->where('u_tsec',$request->filter_market_sub_sector);
        }

        if($request->filter_region != ""){
            $data->where('u_rgn',$request->filter_region);
        }

        if($request->filter_province != ""){
            $data->where('u_province',$request->filter_province);
        }

        if($request->filter_city != ""){
            $data->where('city',$request->filter_city);
        }

        if($request->filter_branch != ""){
            $data->where('group_code',$request->filter_branch);
        }

        if($request->filter_sales_specialist != ""){
            $data->whereHas('sales_specialist', function($q) use($request){
                $q->where('id', $request->filter_sales_specialist);
            });
        }

        if($request->filter_customer_class != ""){
            $data->where('customer_class',$request->filter_customer_class);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('card_code','LIKE',"%".$request->filter_search."%");
                $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('email','LIKE',"%".$request->filter_search."%");

                if(userrole() == 1){
                    $q->orwhere('credit_limit','LIKE',"%".$request->filter_search."%");
                }
            });
        }

        // Not a customer
        if(userrole() != 4){
            // Sales specialist can see only assigned customer
            if(in_array(userrole(),[2])){
                $data->whereHas('sales_specialist', function($q) {
                    return $q->where('ss_id',Auth::id());
                });
            }
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('created_date', '>=' , $start);
            $data->whereDate('created_date', '<=' , $end);
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('created_date', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {
                                $btn = ' <a href="' . route('customer.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                return $btn;
                            })
                            ->addColumn('name', function($row) {
                                $html = "";

                                $html .= '<div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="'.asset('assets/assets/media/default_user.png').'" alt="">
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a href="' . route('customer.show',$row->id). '" class="text-dark fw-bolder text-hover-primary fs-6">';

                                $html .= @$row->card_name ?? " ";

                                $html .= '</a>
                                                <span class="text-muted fw-bold text-muted d-block fs-7">';

                                $html .= "Code: ".$row->card_code;

                                if(@$row->user->email){
                                    $html .= " | Email: ".$row->user->email;
                                }

                                $html .= '</span>
                                            </div>
                                        </div>';

                                return $html;
                            })
                            ->addColumn('status', function($row) {
                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                                }else{
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-danger btn-inline status">Inctive</a>';
                                }

                                return $btn;
                            })
                            ->addColumn('class', function($row) {
                                return @$row->u_class ?? "-";
                            })
                            ->addColumn('credit_limit', function($row) {
                                if(userrole() == 1){
                                    return @$row->credit_limit ?? "-";
                                }else{
                                    return "-";
                                }
                            })
                            ->addColumn('group', function($row) {
                                return @$row->group->name ?? "-";
                            })
                            ->addColumn('u_card_code', function($row) {
                                return @$row->u_card_code ?? "-";
                            })
                            ->addColumn('territory', function($row) {
                                return @$row->territories->description ?? "-";
                            })
                            ->addColumn('created_at', function($row) {
                                return date('M d, Y',strtotime($row->created_date));
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->orderColumn('name', function ($query, $order) {
                                $query->orderBy('card_name', $order);
                            })
                            ->orderColumn('created_at', function ($query, $order) {
                                $query->orderBy('created_at', $order);
                            })
                            ->orderColumn('u_card_code', function ($query, $order) {
                                $query->orderBy('u_card_code', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->orderColumn('credit_limit', function ($query, $order) {
                                $query->orderBy('credit_limit', $order);
                            })
                            ->orderColumn('class', function ($query, $order) {
                                $query->orderBy('u_class', $order);
                            })
                            ->orderColumn('group', function ($query, $order) {
                                $query->join('customer_groups', 'customers.group_code', '=', 'customer_groups.code')->orderBy('customer_groups.name', $order);
                            })
                            ->orderColumn('territory', function ($query, $order) {
                                $query->join('territories', 'customers.territory', '=', 'territories.id')->orderBy('territories.description', $order);
                            })
                            ->orderColumn('company', function ($query, $order) {
                                $query->join('sap_connections', 'customers.sap_connection_id', '=', 'sap_connections.id')->orderBy('sap_connections.company_name', $order);
                            })
                            ->rawColumns(['name', 'role','status','action','credit_limit','group','class'])
                            ->make(true);
    }


    public function getAllBpAddress(Request $request){

        $data = CustomerBpAddress::where('customer_id', $request->customer_id)->orderBy('order', 'ASC')->get();

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('address_type', function($row) {
                                if($row->address_type == "bo_BillTo"){
                                    return "Billing";
                                }elseif($row->address_type == "bo_ShipTo"){
                                    return "Shipping";
                                }else{
                                    return "-";
                                }
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }


    public function getTerritory(Request $request){

        $response = array();
        $search = $request->search;

        $data = Territory::orderby('description','asc')->select('territory_id','description')->limit(50);

        if($search != ''){
            $data->where('description', 'like', '%' .$search . '%');
        }

        $data = $data->get();

        foreach($data as $value){
            $response[] = array(
                "id" => $value->territory_id,
                "text" => $value->description
            );
        }

        return response()->json($response);
    }


    public function export(Request $request){
        $filter = collect();
        if(@$request->data){
            $filter = json_decode(base64_decode($request->data));
        }

        $data = Customer::whereHas('group',function($q){
                            $q->where('name','!=','EMPLOYEE');
                        })->orderBy('created_date', 'desc');

        if(@$filter->filter_status != ""){
            $data->where('is_active',$filter->filter_status);
        }

        if(@$filter->filter_customer_group != ""){
            $data->where('group_code',$filter->filter_customer_group);
        }

        if(@$filter->filter_territory != ""){
            $data->where('territory',$filter->filter_territory);
        }

        if(@$filter->filter_class != ""){
            $data->where('u_class',$filter->filter_class);
        }

        if(@$filter->filter_company != ""){
            $data->where('sap_connection_id',$filter->filter_company);
        }

        if(@$filter->filter_search != ""){
            $data->where(function($q) use ($filter) {
                $q->orwhere('card_code','LIKE',"%".$filter->filter_search."%");
                $q->orwhere('card_name','LIKE',"%".$filter->filter_search."%");
                $q->orwhere('email','LIKE',"%".$filter->filter_search."%");

                if(userrole() == 1){
                    $q->orwhere('credit_limit','LIKE',"%".$filter->filter_search."%");
                }
            });
        }

        // Not a customer
        if(userrole() != 4){
            // Sales specialist can see only assigned customer
            if(in_array(userrole(),[2])){
                $data->whereHas('sales_specialist', function($q) {
                    return $q->where('ss_id',Auth::id());
                });
            }
        }

        if(@$filter->filter_date_range != ""){
            $date = explode(" - ", $filter->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('created_date', '>=' , $start);
            $data->whereDate('created_date', '<=' , $end);
        }

        $data = $data->get();

        $records = array();
        foreach($data as $key => $value){
            $records[] = array(
                                'no' => $key + 1,
                                'company' => @$value->sap_connection->company_name,
                                'card_code' => $value->card_code,
                                'card_name' => $value->card_name,
                                'email' => @$value->user->email,
                                'u_card_code' => $value->u_card_code,
                                'credit_limit' => $value->credit_limit,
                                'group_name' => @$value->group->name,
                                'territory' => @$value->territories->description,
                                'class' => @$value->u_class,
                                'created_at' => date('M d, Y',strtotime($value->created_date)),
                                // 'status' => $value->is_active ? "Active" : "Inctive",
                            );
        }
        if(count($records)){
            return Excel::download(new CustomerExport($records), 'Customer Report.xlsx');
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }
}
