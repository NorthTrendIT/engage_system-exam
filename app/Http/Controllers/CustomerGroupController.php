<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SyncCustomerGroups;
use App\Models\CustomerGroup;
use App\Models\SapConnection;
use DataTables;

class CustomerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(in_array(userrole(),[1,2])){
            $company = SapConnection::all();
        }else{
            $company = collect();
        }
        return view('customer-group.index',compact('company'));
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
        $emails = explode(";", $request->email);

        $dups = array();
        foreach(array_count_values($emails) as $val => $c){
            if($c > 1) $dups[] = $val;
        }
        if(!empty($dups)){
            $response = ['status'=>false,'message'=>'Duplicate email available.'];
        }
        foreach($emails as $e){
            $checkEmail = (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $e)) ? FALSE : TRUE;
            if($checkEmail == FALSE){ 
                $response = ['status'=>false,'message'=>'Email is not valid'];
            } 
        }
        
        $group = CustomerGroup::find($request->id);
        $group->emails = $request->email;
        $group->save();

        $response = ['status'=>true,'message'=>'Customer group emails updated.','data'=>$group]; 

        return $response;
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = CustomerGroup::where('id',$id)->firstOrFail();
        return view('customer-group.edit',compact('edit'));
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

    public function syncCustomerGroups(){
        try {

            // // Save Data of customer group in database
            // SyncCustomerGroups::dispatch('TEST-APBW', 'manager', 'test');


            $sap_connections = SapConnection::where('id', '!=', 5)->get();
            foreach ($sap_connections as $value) {

                $log_id = add_sap_log([
                                'ip_address' => userip(),
                                'activity_id' => 31,
                                'user_id' => userid(),
                                'data' => null,
                                'type' => "S",
                                'status' => "in progress",
                                'sap_connection_id' => $value->id,
                            ]);

                // Save Data of customer group in database
                SyncCustomerGroups::dispatch($value->db_name, $value->user_name , $value->password, $log_id);
            }


            $response = ['status' => true, 'message' => 'Sync Customer Groups Successfully !'];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }


    public function getAll(Request $request){

        $data = CustomerGroup::query();

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('code','LIKE',"%".$request->filter_search."%");
                $q->orwhere('name','LIKE',"%".$request->filter_search."%");
            });
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('name', function($row) {
                                return @$row->name ?? "-";
                            })
                            ->addColumn('code', function($row) {
                                return @$row->code ?? "-";
                            })
                            ->addColumn('company', function($row) {
                                $name = "";
                                if(in_array(userrole(),[1,2]) && @$row->sap_connection->company_name){
                                    $name = @$row->sap_connection->company_name;
                                }
                                return $name;
                            })
                            ->addColumn('action', function($row) {
                                $btn = "";
                                $btn .= '<a href="' . route('customer-group.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                      </a>';
                                return $btn;
                            })
                            ->orderColumn('name', function ($query, $order) {
                                $query->orderBy('name', $order);
                            })
                            ->orderColumn('code', function ($query, $order) {
                                $query->orderBy('code', $order);
                            })
                            ->orderColumn('company', function ($query, $order) {
                                $query->join('sap_connections', 'customer_groups.sap_connection_id', '=', 'sap_connections.id')->orderBy('sap_connections.company_name', $order);
                            })
                            ->rawColumns(['name', 'code','company','action'])
                            ->make(true);
    }
}
