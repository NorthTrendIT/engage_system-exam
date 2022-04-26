<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SyncProductGroups;
use App\Models\ProductGroup;
use App\Models\SapConnection;
use DataTables;

class ProductGroupController extends Controller
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

        return view('product-group.index',compact('company'));
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

    public function syncProductGroups(){
        try {

            $sap_connections = SapConnection::where('id', '!=', 5)->get();
            foreach ($sap_connections as $value) {

                $log_id = add_sap_log([
                                'ip_address' => userip(),
                                'activity_id' => 32,
                                'user_id' => userid(),
                                'data' => null,
                                'type' => "S",
                                'status' => "in progress",
                                'sap_connection_id' => $value->id,
                            ]);

                // Save Data of Product group in database
                SyncProductGroups::dispatch($value->db_name, $value->user_name , $value->password, $log_id);
            }

            $response = ['status' => true, 'message' => 'Sync Product Brands Successfully !'];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    public function updateStatus($id){

        $data = ProductGroup::where('id', $id)->first();

        if(!is_null($data)){
            $data->is_active = !$data->is_active;
            $data->save();
            $response = ['status'=>true,'message'=>'Status update successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = ProductGroup::query();

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('number','LIKE',"%".$request->filter_search."%");
                $q->orwhere('group_name','LIKE',"%".$request->filter_search."%");
            });
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });


        $data->whereNotIn('group_name', ['Items', 'MKTG. MATERIALS', 'OFFICIAL DOCUMENT']);

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('group_name', function($row) {
                                return @$row->group_name ?? "-";
                            })
                            ->addColumn('number', function($row) {
                                return @$row->number ?? "-";
                            })
                            ->addColumn('company', function($row) {
                                $name = "";
                                if(in_array(userrole(),[1,2]) && @$row->sap_connection->company_name){
                                    $name = @$row->sap_connection->company_name;
                                }
                                return $name;
                            })
                            ->addColumn('status', function($row) {

                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<div class="form-group">
                                    <div class="col-3">
                                     <span class="switch">
                                      <label>
                                       <input type="checkbox" checked="checked" name="status" class="status" data-url="' . route('product-group.status', $row->id) . '"/>
                                       <span></span>
                                      </label>
                                     </span>
                                    </div>';
                                }else{
                                    $btn .= '<div class="form-group">
                                    <div class="col-3">
                                     <span class="switch">
                                      <label>
                                       <input type="checkbox" name="status" class="status" data-url="' . route('product-group.status', $row->id) . '"/>
                                       <span></span>
                                      </label>
                                     </span>
                                    </div>';
                                }

                                return $btn;
                            })
                            ->orderColumn('group_name', function ($query, $order) {
                                $query->orderBy('group_name', $order);
                            })
                            ->orderColumn('number', function ($query, $order) {
                                $query->orderBy('number', $order);
                            })
                            ->orderColumn('company', function ($query, $order) {
                                $query->join('sap_connections', 'product_groups.sap_connection_id', '=', 'sap_connections.id')->orderBy('sap_connections.company_name', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->rawColumns(['group_name', 'number', 'company', 'status'])
                            ->make(true);
    }


}
