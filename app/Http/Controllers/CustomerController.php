<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPCustomer;
use App\Jobs\SyncCustomers;
use App\Models\Customer;
use App\Models\Classes;
use App\Models\CustomerGroup;
use App\Models\SapConnection;
use DataTables;
use Auth;

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
        $customer_groups = CustomerGroup::all();
        $classes = Classes::all();
        return view('customer.index',compact('customer_groups','classes'));
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

        $data = Customer::query();

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_customer_group != ""){
            $data->where('group_code',$request->filter_customer_group);
        }

        if($request->filter_class != ""){
            $data->where('u_class',$request->filter_class);
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

                                if($row->email != null){
                                    $html .= " | Email: ".$row->email;
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
                            ->addColumn('created_at', function($row) {
                                return date('M d, Y',strtotime($row->created_date));
                            })
                            ->orderColumn('name', function ($query, $order) {
                                $query->orderBy('card_name', $order);
                            })
                            ->orderColumn('created_at', function ($query, $order) {
                                $query->orderBy('created_at', $order);
                            })
                            ->orderColumn('city', function ($query, $order) {
                                $query->orderBy('city', $order);
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
                                $query->join('customer_groups', 'customers.group_code', '=', 'customer_groups.code')
                                    ->orderBy('customer_groups.name', $order);
                            })
                            ->rawColumns(['name', 'role','status','action','credit_limit','group','class'])
                            ->make(true);
    }
}
