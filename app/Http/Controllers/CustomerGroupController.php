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
        return view('customer-group.index');
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

    public function syncCustomerGroups(){
        try {

            // // Save Data of customer group in database
            // SyncCustomerGroups::dispatch('TEST-APBW', 'manager', 'test');

            $sap_connections = SapConnection::all();
            foreach ($sap_connections as $value) {

                // Save Data of customer group in database
                SyncCustomerGroups::dispatch($value->db_name, $value->user_name , $value->password);
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
                            ->orderColumn('name', function ($query, $order) {
                                $query->orderBy('name', $order);
                            })
                            ->orderColumn('code', function ($query, $order) {
                                $query->orderBy('code', $order);
                            })
                            ->rawColumns(['name', 'code'])
                            ->make(true);
    }
}
