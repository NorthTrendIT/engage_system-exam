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

        if($request->filter_status != ""){
            $data->where('status',$request->filter_status);
        }

        if($request->filter_type != ""){
            $data->where('type',$request->filter_type);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->whereHas('activity', function($q1) use ($request) {
                    $q1->where('name','LIKE',"%".$request->filter_search."%");
                });

                // $q->OrWhereHas('user', function($q1) use ($request) {
                //     $q1->where('first_name','LIKE',"%".$request->filter_search."%");
                //     $q1->where('last_name','LIKE',"%".$request->filter_search."%");
                // });
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('activity', function($row) {
                    return @$row->activity->name ?? "-";
                })
                ->addColumn('user_name', function($row) {
                    $name = @$row->user->first_name;
                    if($name && !empty(@$row->user->last_name)){
                        $name .= " ".@$row->user->last_name;
                    }
                    return $name;
                })
                ->addColumn('company', function($row) {
                    return @$row->sap_connection->company_name ?? "-";
                })
                ->addColumn('status', function($row) {

                    $btn = "";
                    if($row->status == "completed"){
                        $btn .= '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline ">Completed</a>';
                    }else if($row->status == "error"){
                        $btn .= '<a href="javascript:" class="btn btn-sm btn-light-danger btn-inline ">Error</a>';
                    }else if(!is_null($row->status)){
                        $btn .= '<a href="javascript:" class="btn btn-sm btn-light-info btn-inline ">'.ucfirst(@$row->status ?? "").'</a>';
                    }

                    return $btn;
                })
                ->addColumn('type', function($row) {

                    $btn = "";
                    if($row->type == "S"){
                        $btn .= "SAP";
                    }else if($row->type == "O"){
                        $btn .= 'OMS';
                    }

                    return $btn;
                })
                ->addColumn('ip_address', function($row) {
                    return $row->ip_address;
                })
                ->addColumn('date_time', function($row){
                    return date("M d, Y H:m A", strtotime($row->created_at));
                })
                ->orderColumn('date_time', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->orderColumn('type', function ($query, $order) {
                    $query->orderBy('type', $order);
                })
                ->orderColumn('status', function ($query, $order) {
                    $query->orderBy('status', $order);
                })
                ->orderColumn('user_name', function ($query, $order) {
                    $query->join('users', 'activity_logs.user_id', '=', 'users.id')
                        ->orderBy('users.first_name', $order);
                })
                ->orderColumn('activity', function ($query, $order) {
                    $query->join('activity_masters', 'activity_logs.activity_id', '=', 'activity_masters.id')
                        ->orderBy('activity_masters.name', $order);
                })
                ->rawColumns(['status'])
                ->make(true);
    }
}
