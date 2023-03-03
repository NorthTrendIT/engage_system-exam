<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPTerritory;
use App\Jobs\SyncTerritories;
use App\Models\Territory;
use App\Models\SapConnection;
use DataTables;

class TerritoriesController extends Controller
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
        return view('territory.index');
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

    public function syncTerritories(){
        try {
            // Add Sync Territories data log.
            // add_log(22, null);

            $sap_connection = SapConnection::first();

            if(!is_null($sap_connection)){

                $log_id = add_sap_log([
                                    'ip_address' => userip(),
                                    'activity_id' => 22,
                                    'user_id' => userid(),
                                    'data' => null,
                                    'type' => "S",
                                    'status' => "in progress",
                                    'sap_connection_id' => $sap_connection->id,
                                ]);

                // Save Data of Territories in database
                SyncTerritories::dispatch($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $log_id);
                // SyncTerritories::dispatch('TEST-NTMC', 'manager', 'test');
                // SyncTerritories::dispatch('TEST-PHILCREST', 'manager', 'test');
                // SyncTerritories::dispatch('TEST-PHILSYN', 'manager', 'test');
            }

            $response = ['status' => true, 'message' => 'Sync Territories successfully !'];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Territory::query();

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('description','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('name', function($row) {
                                return @$row->description;
                            })
                            ->orderColumn('name', function ($query, $order) {
                                $query->orderBy('description', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->addColumn('status', function($row) {

                                // $btn = "";
                                // if($row->is_active){
                                //     $btn .= '<div class="form-group">
                                //     <div class="col-3">
                                //      <span class="switch">
                                //       <label>
                                //        <input type="checkbox" disabled checked="checked" name="status"/>
                                //        <span></span>
                                //       </label>
                                //      </span>
                                //     </div>';
                                // }else{
                                //     $btn .= '<div class="form-group">
                                //     <div class="col-3">
                                //      <span class="switch">
                                //       <label>
                                //        <input type="checkbox" disabled name="status"/>
                                //        <span></span>
                                //       </label>
                                //      </span>
                                //     </div>';
                                // }

                                // return $btn;

                                $btn = "";
                                if($row->is_active){
                                  $btn .= '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                                }else{
                                  $btn .= '<a href="javascript:" class="btn btn-sm btn-light-danger btn-inline status">Inctive</a>';
                                }

                                return $btn;
                            })
                            ->rawColumns(['status'])
                            ->make(true);
    }
}
