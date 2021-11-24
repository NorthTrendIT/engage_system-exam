<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use DataTables;
class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('activity-log.index');
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

    public function getAll(Request $request){

        $data = ActivityLog::with(['activity', 'user']);

        // if($request->filter_status != ""){
        //     $data->where('is_active',$request->filter_status);
        // }

        // if($request->filter_search != ""){
        //     $data->where(function($q) use ($request) {
        //         $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
        //         $q->orwhere('doc_type','LIKE',"%".$request->filter_search."%");
        //     });
        // }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('activity', function($row) {
                    return $row->activity->name;
                })
                ->addColumn('user_name', function($row) {
                    $name = $row->user->first_name;
                    if(!empty($row->user->last_name)){
                        $name = $name." ".$row->user->last_name;
                    }
                    return $name;
                })
                ->addColumn('ip_address', function($row) {
                    return $row->ip_address;
                })
                ->addColumn('date_time', function($row){
                    return date("M d, Y H:m A", strtotime($row->created_at));
                })
                ->make(true);
    }
}
