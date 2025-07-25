<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPCustomer;
use App\Jobs\SyncCustomers;
use App\Models\User;
use App\Models\Customer;
use App\Models\Classes;
use App\Models\CustomerGroup;
use App\Models\SapConnection;
use App\Models\CustomerBpAddress;
use App\Models\CustomerProductGroup;
use App\Models\Territory;
use DataTables;
use Auth;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerExport;
use App\Exports\CustomerTaggingExport;
use App\Models\CustomerTarget;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $sap_connections = SapConnection::all();
        return view('customer.index', compact('sap_connections'));
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

        $totalOverdueAmount = Invoice::where(['card_code'=>$data->card_code,'document_status'=>'bost_Open'])->sum('doc_entry');

        $sap_connection_id = explode(',', @$data->user->multi_sap_connection_id);
        $sap_connections = SapConnection::whereIn('id', $sap_connection_id)->where('id','!=', $data->sap_connection_id)->pluck('company_name')->toArray();
        $sap_connections = implode(", ", $sap_connections);
        return view('customer.view',compact('data', 'sap_connections','totalOverdueAmount'));
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

    public function syncCustomers(Request $request){
        try {            
            // Add Sync Customer data log.
            // add_log(15, null);

            if($request->filter_company != ""){
                $sap_connections = SapConnection::where('id', $request->filter_company)->first();
                $log_id = add_sap_log([
                                    'ip_address' => userip(),
                                    'activity_id' => 15,
                                    'user_id' => userid(),
                                    'data' => null,
                                    'type' => "S",
                                    'status' => "in progress",
                                    'sap_connection_id' => $sap_connections->id,
                                ]);

                    // Save Data of customer in database
                    SyncCustomers::dispatch($sap_connections->db_name, $sap_connections->user_name , $sap_connections->password, $log_id, $request->filter_search);
            }else{
                $sap_connections = SapConnection::where('id', '!=', 5)->get();

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
                    SyncCustomers::dispatch($value->db_name, $value->user_name , $value->password, $log_id, $request->filter_search);
                }
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

        $data = Customer::whereHas('sap_connection',function($q){
                    $q->WhereNull('deleted_at');
                }); //query();

        // if($request->filter_status != ""){
        //     $data->where('is_active',$request->filter_status);
        // }
        $sap_connections = SapConnection::where('id', $request->filter_company)->first();
        $sap_priceLists = new SAPCustomer($sap_connections->db_name, $sap_connections->user_name , $sap_connections->password, false, '');
        $priceRecord = $sap_priceLists->fetchPriceLists();
        $priceLists = [];

        if($priceRecord){
            foreach($priceRecord['value'] as $price){
                $priceLists[$price['PriceListNo']] = $price['PriceListName'];
            }
        }

        if($request->filter_territory != ""){
            $data->whereHas('territories', function($q) use ($request){
                $q->where('id', $request->filter_territory);
            });
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_market_sector != ""){
            $data->where('u_sector',$request->filter_market_sector);
        }

        if($request->filter_market_sub_sector != ""){
            $data->where('u_subsector',$request->filter_market_sub_sector);
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
                $q->where('ss_id', $request->filter_sales_specialist);
            });
        }

        if($request->filter_brand != ""){
            $data->whereHas('product_groups', function($q) use($request){
                $q->where('product_group_id', $request->filter_brand);
            });
        }

        if($request->filter_customer_class != ""){
            $data->where('u_classification',$request->filter_customer_class);
        }

        if($request->filter_status != ""){
            $today = Carbon::today()->toDateString();
            if($request->filter_status == 1){
                $data->where('is_active',1);                     
                    // ->orWhere(function($query) use ($today){
                    //     $query->where('frozen',1);
                    //     $query->where('frozen_from', '>' ,$today);
                    //     $query->where('frozen_to', '<' ,$today);
                    // });                    
            }else if($request->filter_status == 0){
                $data->where('is_active',0);
                // $data->where('frozen',0)->orWhere('is_active',0);
            }
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('card_code','LIKE',"%".$request->filter_search."%");
                $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('email','LIKE',"%".$request->filter_search."%");
                $q->orwhere('u_card_code','LIKE',"%".$request->filter_search."%");

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
                            ->addColumn('customer_code', function($row) {
                                return @$row->card_code ?? "-";
                            })
                            ->addColumn('card_name', function($row) {
                                return @$row->card_name ?? "-";
                            })
                            ->addColumn('name', function($row) {
                                $html = "";

                                $html .= '<div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <div class="symbol-label fs-3" style="color:'.convert_hex_to_rgba(@$row->user->default_profile_color).';background-color:'.convert_hex_to_rgba(@$row->user->default_profile_color,0.5).';"><b>'.get_sort_char(@$row->card_name).'</b></div>
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
                                return @$row->u_classification_sap_value->value ?? @$row->u_classification ?? "-";
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
                            ->addColumn('vat', function ($row) {
                                return @$row->vat_group ?? "-";
                            })
                            ->addColumn('u_card_code', function($row) {
                                return @$row->u_card_code ?? "-";
                            })
                            ->addColumn('assignment', function($row) {
                                $aName = '';
                                $count = 0;
                                // foreach(@$row->sales_specialist as $ss){
                                //     $comma = ($count > 0) ? ', ' : '';
                                //     if(strpos($aName, $ss->assignment->assignment_name) === false){
                                //         $aName .=  $comma.$ss->assignment->assignment_name;
                                //     }
                                //     $count ++;
                                // }
                                
                                $assgn = @$row->territory_sales_specialist->first();
                                $aName = ($assgn) ? $assgn->salesAssignment->assignment_name : '-';

                                return $aName;
                            })
                            ->addColumn('territory', function($row) {
                                return @$row->territoriesv2->description ?? "-";
                            })
                            ->addColumn('city', function($row) {
                                return @$row->city ?? "-";
                            })
                            ->addColumn('u_cust_segment', function($row) {
                                return @$row->u_cust_segment_sap_value->value ?? @$row->u_cust_segment ?? "-";
                            })
                            ->addColumn('u_sector', function($row) {
                                return @$row->u_sector_sap_value->value ?? @$row->u_sector ?? "-";
                            })
                            ->addColumn('u_subsector', function($row) {
                                return @$row->u_subsector_sap_value->value ?? @$row->u_subsector ?? "-";
                            })
                            ->addColumn('u_rgn', function($row) {
                                return @$row->u_rgn ?? "-";
                            })
                            ->addColumn('u_province', function($row) {
                                return @$row->u_province_sap_value->value ?? @$row->u_province ?? "-";
                            })
                            ->addColumn('created_at', function($row) {
                                return date('M d, Y',strtotime($row->created_date));
                            })
                            ->addColumn('company', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->addColumn('customer_price_list', function($row) use ($priceLists) {
                                return ($priceLists) ? $priceLists[$row->price_list_num] : '';
                            })
                            ->orderColumn('customer_code', function ($query, $order) {
                                $query->orderBy('card_code', $order);
                            })
                            ->orderColumn('card_name', function ($query, $order) {
                                $query->orderBy('card_name', $order);
                            })
                            ->orderColumn('name', function ($query, $order) {
                                $query->orderBy('card_name', $order);
                            })
                            ->orderColumn('created_at', function ($query, $order) {
                                $query->orderBy('created_at', $order);
                            })
                            ->orderColumn('credit_limit', function ($query, $order) {
                                $query->orderBy('credit_limit', $order);
                            })
                            ->orderColumn('u_card_code', function ($query, $order) {
                                $query->orderBy('u_card_code', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->orderColumn('class', function ($query, $order) {
                                $query->orderBy('u_classification', $order);
                            })
                            ->orderColumn('u_cust_segment', function ($query, $order) {
                                $query->orderBy('u_cust_segment', $order);
                            })
                            ->orderColumn('u_sector', function ($query, $order) {
                                $query->orderBy('u_sector', $order);
                            })
                            ->orderColumn('u_subsector', function ($query, $order) {
                                $query->orderBy('u_subsector', $order);
                            })
                            ->orderColumn('u_rgn', function ($query, $order) {
                                $query->orderBy('u_rgn', $order);
                            })
                            ->orderColumn('u_province', function ($query, $order) {
                                $query->orderBy('u_province', $order);
                            })
                            ->orderColumn('city', function ($query, $order) {
                                $query->orderBy('city', $order);
                            })
                            ->orderColumn('group', function ($query, $order) {
                                $query->join('customer_groups', 'customers.group_code', '=', 'customer_groups.code')->orderBy('customer_groups.name', $order);
                            })
                            ->orderColumn('vat', function ($query, $order) {
                                $query->orderBy('vat_group', $order);
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

        if($request->filter_territory != ""){
            $data->whereHas('territories', function($q) use ($request){
                $q->where('id', $request->filter_territory);
            });
        }

        if(@$filter->filter_company != ""){
            $data->where('sap_connection_id',$filter->filter_company);
        }

        if(@$filter->filter_market_sector != ""){
            $data->where('u_sector',$filter->filter_market_sector);
        }

        if(@$filter->filter_market_sub_sector != ""){
            $data->where('u_subsector',$filter->filter_market_sub_sector);
        }

        if(@$filter->filter_region != ""){
            $data->where('u_rgn',$filter->filter_region);
        }

        if(@$filter->filter_province != ""){
            $data->where('u_province',$filter->filter_province);
        }

        if(@$filter->filter_city != ""){
            $data->where('city',$filter->filter_city);
        }

        if(@$filter->filter_branch != ""){
            $data->where('group_code',$filter->filter_branch);
        }

        if(@$filter->filter_brand != ""){
            $data->whereHas('product_groups', function($q) use($filter){
                $q->where('product_group_id', $filter->filter_brand);
            });
        }

        if(@$filter->filter_sales_specialist != ""){
            $data->whereHas('sales_specialist', function($q) use($filter){
                $q->where('ss_id', $filter->filter_sales_specialist);
            });
        }

        if(@$filter->filter_customer_class != ""){
            $data->where('u_classification',$filter->filter_customer_class);
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

            if(@$filter->module_type == "customer-tagging"){
                $records[] = array(
                                    'no' => $key + 1,
                                    'card_code' => $value->card_code ?? "-",
                                    'card_name' => $value->card_name ?? "-",
                                    'class' => @$value->u_classification_sap_value->value ?? @$value->u_classification ?? "-",
                                    'customer_segment' => @$value->u_cust_segment_sap_value->value ?? @$value->u_cust_segment ?? "-",
                                    'market_sector' => @$value->u_sector_sap_value->value ?? @$value->u_sector ?? "-",
                                    'market_sub_sector' => @$value->u_subsector_sap_value->value ?? @$value->u_subsector ?? "-",
                                    'region' => @$value->u_rgn ?? "-",
                                    'province' => @$value->u_province_sap_value->value ?? @$value->u_province ?? "-",
                                    'territory' => @$value->territoriesv2->description ?? "-",
                                    'city' => @$value->city ?? "-",
                                );
            }else{
                $aName = '';
                // foreach(@$row->sales_specialist as $ss){
                //     $comma = ($count > 0) ? ', ' : '';
                //     if(strpos($aName, $ss->assignment->assignment_name) === false){
                //         $aName .=  $comma.$ss->assignment->assignment_name;
                //     }
                //     $count ++;
                // }
                $assgn = @$value->territory_sales_specialist->first();
                $aName = ($assgn) ? $assgn->salesAssignment->assignment_name : '-';
                
                $records[] = array(
                                    'no' => $key + 1,
                                    'company' => @$value->sap_connection->company_name ?? "-",
                                    'card_code' => $value->card_code ?? "-",
                                    'card_name' => $value->card_name ?? "-",
                                    'email' => @$value->user->email ?? "-",
                                    'u_card_code' => $value->u_card_code ?? "-",
                                    'credit_limit' => $value->credit_limit ?? "-",
                                    'group_name' => @$value->group->name ?? "-",
                                    'assignment_name' => $aName,
                                    'territory' => @$value->territories->description ?? "-",
                                    'class' => @$value->u_classification_sap_value->value ?? @$value->u_classification ?? "-",
                                    'created_at' => date('M d, Y',strtotime($value->created_date)),
                                    // 'status' => $value->is_active ? "Active" : "Inctive",
                                );
            }
        }
        if(count($records)){

            if(@$filter->module_type == "customer-tagging"){
                $title = 'Customer Tagging Report '.date('dmY').'.xlsx';
                return Excel::download(new CustomerTaggingExport($records), $title);
            }else{
                $title = 'Customer Report '.date('dmY').'.xlsx';
                return Excel::download(new CustomerExport($records), $title);
            }
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }


    public function customerTaggingIndex(){
        $company = SapConnection::all();
        return view('customer.tagging', compact('company'));
    }


    public function customerTaggingGetTerritory(Request $request){

        $response = $territory_ids = array();
        $search = $request->search;

        if(@$request->sap_connection_id != "" && @$request->brand_id != ""){

            $customers = CustomerProductGroup::has('customer')->with('customer')->where('product_group_id', $request->brand_id)->get()->toArray();

            $territory_ids = array_map( function ( $ar ) {
                           return $ar['territory'];
                        }, array_column( $customers, 'customer' ) );

            if(!empty($territory_ids)){
                $data = Territory::whereIn('territory_id', $territory_ids)->orderby('description','asc')->select('territory_id','description')->limit(50);

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
            }
        }

        return response()->json($response);
    }


    public function customerTaggingGetMarketSector(Request $request){

        $response = array();
        $search = $request->search;

        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
            if($request->sap_connection_id == ""){
                return response()->json($response);
            }
        }else{
            $user = User::where('id',Auth::id())->first();
            $request->sap_connection_id = $user->sap_connection_id;
        }

        if(@$request->sap_connection_id != "" && @$request->brand_id != ""){

            $data = CustomerProductGroup::has('customer')->with('customer')->where('product_group_id', $request->brand_id);

            if($search != ''){
                $data->where('customer',function($q) use ($search) {
                    $q->where('u_sector', 'like', '%' .$search . '%');
                });
            }

            $data = $data->get();

            foreach($data as $value){


                if(isset($response[$value->customer->u_sector]) || is_null($value->customer->u_sector)){
                   continue;
                }

                $text = @$value->customer->u_sector_sap_value->value ?? $value->customer->u_sector;
                $response[$text] = array(
                    "id" => $value->customer->u_sector,
                    "text" => $text
                );
            }

            sort($response);
        }

        return response()->json($response);
    }

    public function customerTaggingGetMarketSubSector(Request $request){

        $response = array();
        $search = $request->search;
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
            if($request->sap_connection_id == ""){
                return response()->json($response);
            }
        }else{
            $user = User::where('id',Auth::id())->first();
            $request->sap_connection_id = $user->sap_connection_id;
        }

        if(@$request->sap_connection_id != "" && @$request->brand_id != ""){

            $data = CustomerProductGroup::has('customer')->with('customer')->where('product_group_id', $request->brand_id);

            if($search != ''){
                $data->where('customer',function($q) use ($search) {
                    $q->where('u_subsector', 'like', '%' .$search . '%');
                });
            }

            $data = $data->get();

            foreach($data as $value){


                if(isset($response[$value->customer->u_subsector]) || is_null($value->customer->u_subsector)){
                   continue;
                }

                $text = @$value->customer->u_subsector_sap_value->value ?? $value->customer->u_subsector;
                $response[$text] = array(
                    "id" => $value->customer->u_subsector,
                    "text" => $text
                );
            }

            sort($response);
        }

        return response()->json($response);
    }

    public function customerTaggingGetCustomerClass(Request $request){

        $response = array();
        $search = $request->search;
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
            if($request->sap_connection_id == ""){
                return response()->json($response);
            }
        }else{
            $user = User::where('id',Auth::id())->first();
            $request->sap_connection_id = $user->sap_connection_id;
        }

        if(@$request->sap_connection_id != "" && @$request->brand_id != ""){

            $data = CustomerProductGroup::has('customer')->with('customer')->where('product_group_id', $request->brand_id);

            if($search != ''){
                /*$data->where('customer',function($q) use ($search) {
                    $q->where('u_classification', 'like', '%' .$search . '%');
                });*/

                $data->whereHas('customer.u_classification_sap_value', function($q) use ($search) {
                    $q->where('value','LIKE',"%".$search."%");
                });
            }

            $data = $data->get();

            foreach($data as $value){

                if(isset($response[$value->customer->u_classification])){
                   continue;
                }

                $text = @$value->customer->u_classification_sap_value->value ?? @$value->customer->u_classification;
                $response[$text] = array(
                    "id" => $value->customer->u_classification,
                    "text" => $text
                );
            }

            sort($response);
        }

        return response()->json($response);
    }


    public function customerTaggingGetSalesSpecialist(Request $request){
        $response = $customer_ids = array();
        $search = $request->search;

        if(@$request->sap_connection_id != "" && @$request->brand_id != ""){
            $customer_ids = CustomerProductGroup::has('customer')->with('customer')->where('product_group_id', $request->brand_id)->pluck('customer_id')->toArray();

            if(!empty($customer_ids)){
                if(Auth::user()->role_id == 6){
                    $data = User::where('role_id', 2)
                                    ->where('parent_id', Auth::id())
                                    ->has('sales_specialist_customers')
                                    ->orderby('sales_specialist_name','asc');
                }else if(Auth::user()->role_id == 1){
                    if(@$request->filter_manager != ''){
                        $data = User::where('role_id', 2)
                                    ->where('parent_id', $request->filter_manager)
                                    ->has('sales_specialist_customers')
                                    ->orderby('sales_specialist_name','asc');
                    } else{
                        return response()->json($response);
                    } 
                }else{
                    $data = User::where('role_id', 2)->has('sales_specialist_customers')->orderby('sales_specialist_name','asc');
                }                

                $data->whereHas('sales_specialist_customers', function($q) use ($customer_ids) {
                    $q->whereIn('customer_id', $customer_ids);
                });

                $data = $data->get();

                foreach($data as $value){

                    $response[] = array(
                        "id" => $value->id,
                        "text" => $value->sales_specialist_name
                    );
                }
            }

        }

        return response()->json($response);
    }

    public function updateCustomerCurrency($sap_connection){
        $sap_connections = SapConnection::where('id', $sap_connection)->first();

        $sap_customer = new SAPCustomer($sap_connections->db_name, $sap_connections->user_name , $sap_connections->password, false, '');
        $customers = $sap_customer->fetchCustomers();

        $customer_ids = [];
        foreach($customers['value'] as $key => $cust){
            // $customer_ids[$key] = Customer::where('card_code', $cust['CardCode'])->where('sap_connection_id', $sap_connection)->pluck('id')->toArray();
            Customer::where('card_code', $cust['CardCode'])->where('sap_connection_id', $sap_connection)->update(['currency' =>$cust['Currency']]);
        }
    }

    public function customerTarget(){
        $company = SapConnection::all();
        return view('customer-target.view', compact('company'));
    }

    public function customerTargetFetch(Request $request){
        
        $data = CustomerTarget::with('product_group', 'product_category')
                                ->where(['b_unit' => $request->sap_connection_id,
                                        'customer_id' => $request->customer_id,
                                        'year' => $request->year
                                        ])->get();
        
        $response = ['status' => true, 'data'=>$data];
        return $response;
    }

    public function customerTargetAdd(Request $request){

        $target = [];
        $target['b_unit'] = $request->sap_connection_id;
        $target['customer_id'] = $request->customer_id;
        $target['brand_id'] = $request->brand;
        $target['category_id'] = $request->category;
        foreach($request->monthly_target as $key => $mt){
            $month = date("F", strtotime("$key/12/1997"));
            $target[strtolower($month)] = $mt;
        }
        $target['year'] = $request->year;

        $record = CustomerTarget::where(['b_unit' => $request->sap_connection_id,
                                        'customer_id' => $request->customer_id,
                                        'year'   => $request->year,
                                        'brand_id' => $request->brand,
                                        'category_id' => $request->category
                                        ])->get();        
        if($record->count() > 0){
            $response = ['status' => false, 'data'=> [], 'message' => 'Monthly target record already exists. Please add a unique one.'];
        }else{
            $id = CustomerTarget::insertGetId($target);
            $response = ['status' => true, 'data'=> $target, 'message' => 'Monthly target record successfully added.'];
        }
        
        return $response;
    }

    public function customerTargetUpdate(Request $request){
        $target = [];
        $target['brand_id'] = $request->new_brand;
        $target['category_id'] = $request->new_category;
        foreach($request->monthly_target as $key => $mt){
            $month = date("F", strtotime("$key/12/1997"));
            $target[strtolower($month)] = $mt;
        }

        CustomerTarget::where(['b_unit' => $request->sap_connection_id,
                               'customer_id' => $request->customer_id,
                               'year'   => $request->year,
                               'brand_id' => $request->brand,
                               'category_id' => $request->category
                             ])->update($target);

        $response = ['status' => true, 'data'=>[], 'message' => 'Monthly target record updated successfully.'];
        return $response;
    }

    
    public function customerTargetDelete(Request $request){

        CustomerTarget::where(['b_unit' => $request->sap_connection_id,
                               'customer_id' => $request->customer_id,
                               'year'   => $request->year,
                               'brand_id' => $request->brand,
                               'category_id' => $request->category
                             ])->delete();

        $response = ['status' => true, 'data'=>[], 'message' => 'Monthly target record successfully removed.'];
        return $response;
    }


    public function fetchActualSalesAndTargetSales(Request $request){
        $actual = [];
        $target = CustomerTarget::selectRaw('SUM(january) as January, SUM(february) as February, SUM(march) as March, SUM(april) as April,
                                                     SUM(may) as May, SUM(june) as June, SUM(july) as July, SUM(august) as August, SUM(september) as September,
                                                     SUM(october) as October, SUM(november) as November, SUM(december) as December')
                                ->where(['b_unit' => $request->sap_connection_id,
                                            'customer_id' => $request->customer_id,
                                            'year'   => $request->year,
                                        ]);

        if($request->brand_id !== null){                                
            $target->where('brand_id', $request->brand_id);
        }else{
            $target->where('category_id', $request->category_id);
        }

        $target_results =  $target->groupBy('year')->get();
        
        if($request->sap_connection_id == 5){
            $request->sap_connection_id = 1; //apbw
        }

        $request->year = $request->year - 1;
        $prdata = $this->getActualSales($request);

        $request->year = $request->year + 1;
        $adata = $this->getActualSales($request);
        
        $response = [];
        $actual_sales_year = [];
        $prev_sales_year = [];
        $target_sales_year = [];
        for($x = 1; $x <= 12; $x++){

            $year = $request->year;
            $month =  date("F", strtotime("$x/12/$year"));
            if(isset($adata[$month])){
                $actual_sales_year[] = $adata[$month];
            }else{
                $actual_sales_year[] = 0;
            }

            if(isset($prdata[$month])){ //prev year sales
                $prev_sales_year[] = $prdata[$month];
            }else{
                $prev_sales_year[] = 0;
            }

            if($target_results->count() > 0){ //in database, it's already set to zero.
                $target_sales_year[] = (int) $target_results[0]->$month;
            }else{
                $target_sales_year[] = 0;
            }
        }

        $acQ1 = $actual_sales_year[0] + $actual_sales_year[1] + $actual_sales_year[2];
        $acQ2 = $actual_sales_year[3] + $actual_sales_year[4] + $actual_sales_year[5];
        $acQ3 = $actual_sales_year[6] + $actual_sales_year[7] + $actual_sales_year[8];
        $acQ4 = $actual_sales_year[9] + $actual_sales_year[10] + $actual_sales_year[11];

        $actual_sales_quarter = [$acQ1, $acQ2, $acQ3, $acQ4];

        $prQ1 = $prev_sales_year[0] + $prev_sales_year[1] + $prev_sales_year[2];
        $prQ2 = $prev_sales_year[3] + $prev_sales_year[4] + $prev_sales_year[5];
        $prQ3 = $prev_sales_year[6] + $prev_sales_year[7] + $prev_sales_year[8];
        $prQ4 = $prev_sales_year[9] + $prev_sales_year[10] + $prev_sales_year[11];

        $prev_sales_quarter = [$prQ1, $prQ2, $prQ3, $prQ4];

        $taQ1 = $target_sales_year[0] + $target_sales_year[1] + $target_sales_year[2];
        $taQ2 = $target_sales_year[3] + $target_sales_year[4] + $target_sales_year[5];
        $taQ3 = $target_sales_year[6] + $target_sales_year[7] + $target_sales_year[8];
        $taQ4 = $target_sales_year[9] + $target_sales_year[10] + $target_sales_year[11];
        
        $target_sales_quarter = [$taQ1, $taQ2, $taQ3, $taQ4];

        $response = ['year' => [ 'series' =>[
                                    ['name' => 'Actual Sales','data' => $actual_sales_year], 
                                    ['name' => 'Target Sales','data' => $target_sales_year]
                                  ],
                                'bar' => ['columnWidth' => '55%'],
                                'stroke' => ['width' => 3],
                                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                                'colors' => ['#034F84', '#FA7A35']
                            ],
                     'quarter' => [ 'series' =>[
                                        ['name' => 'Actual Sales','data' => $actual_sales_quarter], 
                                        ['name' => 'Target Sales','data' => $target_sales_quarter]
                                    ],
                                'bar' => ['columnWidth' => '-10%'],
                                'stroke' => ['width' => 20],
                                'categories' => ['Q1', 'Q2', 'Q3', 'Q4'],
                                'colors' => ['#afafaf', '#12365d']
                            ],
                      'monthly_comparison' => [ 'series' =>[
                                    ['name' => 'Previous Year','data' => $prev_sales_year], 
                                    ['name' => 'Current Year','data' => $actual_sales_year]
                                ],
                            'bar' => ['columnWidth' => '55%'],
                            'stroke' => ['width' => 3],
                            'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            'colors' => ['#034F84', '#FA7A35']
                        ],
                      'quarterly_comparison' => [ 'series' =>[
                                        ['name' => 'Previous Year','data' => $prev_sales_quarter], 
                                        ['name' => 'Current Year','data' => $actual_sales_quarter]
                                    ],
                                'bar' => ['columnWidth' => '-10%'],
                                'stroke' => ['width' => 20],
                                'categories' => ['Q1', 'Q2', 'Q3', 'Q4'],
                                'colors' => ['#afafaf', '#12365d']
                            ],
                    ];

       return ['status' => true, 'data'=> $response, 'message' => 'Record successfully fetch.'];

    }

    private function getActualSales($request){
        $actual = InvoiceItem::selectRaw('MONTH(invoices.doc_date) as month, SUM(invoice_items.quantity) as total_quantity')
                            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                            ->join('products', 'invoice_items.item_code', '=', 'products.item_code')
                            ->where('invoice_items.real_sap_connection_id', $request->sap_connection_id)
                            ->where('invoices.card_code', $request->customer_code)
                            ->where('invoices.cancelled', 'No')
                            ->whereYear('invoices.doc_date', $request->year)
                            ->where('invoices.real_sap_connection_id', $request->sap_connection_id); //to make sure
        
        if($request->brand !== null){                    
            $actual->where('products.items_group_code', $request->brand);
        }else{
            $actual->where('products.u_tires', $request->category);
        }

        $actual_results = $actual->where('products.sap_connection_id', $request->sap_connection_id) //to make sure
                                 ->groupBy(DB::raw('MONTH(invoices.doc_date)'))
                                 ->orderBy(DB::raw('MONTH(invoices.doc_date)'), 'ASC')
                                 ->get();
        
        $adata = [];
        foreach($actual_results as $result){ //recreate data
            $year = $request->year;
            $month = date('F', strtotime("$result->month/12/$year"));

            $adata[$month] = $result->total_quantity;
        }

        return $adata;
    }



}
