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
        return view('product-group.index');
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

            $sap_connections = SapConnection::all();
            foreach ($sap_connections as $value) {
                // Save Data of Product group in database
                SyncProductGroups::dispatch($value->db_name, $value->user_name , $value->password);
            }

            $response = ['status' => true, 'message' => 'Sync Product Groups Successfully !'];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
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

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('group_name', function($row) {
                                return @$row->group_name ?? "-";
                            })
                            ->addColumn('number', function($row) {
                                return @$row->number ?? "-";
                            })
                            ->orderColumn('group_name', function ($query, $order) {
                                $query->orderBy('group_name', $order);
                            })
                            ->orderColumn('number', function ($query, $order) {
                                $query->orderBy('number', $order);
                            })
                            ->rawColumns(['group_name', 'number'])
                            ->make(true);
    }
}
