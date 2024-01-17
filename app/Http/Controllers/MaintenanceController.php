<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use DataTables;
use Illuminate\Support\Carbon;

class MaintenanceController extends Controller
{
    public function user(){

        $roles = Role::where('id','!=',1)->whereNull('user_id')->get();

        return view('maintenance/index', compact('roles'));
    }

    public function userGetAll(Request $request){
        $data = User::where('users.id','!=', 1)->where(function($q){
                    $q->where('is_active', 0)->orWhereNotNull('resignation_date');
                });

        if($request->filter_role != ""){
            $data->where('role_id',$request->filter_role);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('first_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('last_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('email','LIKE',"%".$request->filter_search."%");
                $q->orwhere('sales_specialist_name','LIKE',"%".$request->filter_search."%");
            });
        }

        if(userrole() != 1){
            $data->where('created_by',Auth::id());
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {
                                $btn = "";

                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('user.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete mr-10" title="Delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';


                                return $btn;
                            })
                            ->addColumn('territory', function($row) {
                                return @$row->territory->description ?? "-";
                            })
                            ->addColumn('role', function($row) {

                                if(@$row->role){
                                    return @$row->role->name;
                                }else{
                                    return "-";
                                }
                            })
                            ->addColumn('resignation_date',function($row){
                                return ($row->resignation_date) ? Carbon::parse($row->resignation_date)->format('M d, Y') : '-';
                            })
                            ->addColumn('no_of_days',function($row){
                                $days_count_str = '';
                                $resg_date = ($row->resignation_date) ? $row->resignation_date : date('Y-m-d');
                                $start_date= Carbon::parse(date('Y-m-d'));
                                $finish_date = Carbon::parse($resg_date);

                                $days = $start_date->diffInDays($finish_date, false);
                                $days = ( ($finish_date->format('Y-m-d') >= date('Y-m-d')) && $days > 0 ) ? 0 : abs($days);
                                $preposition = ($days > 1) ? ' days' : ' day';
                                $days_count_str .= $days.$preposition;
                                return $days_count_str;
                            })
                            ->addColumn('status', function($row) {

                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<div class="form-group">
                                    <div class="col-3">
                                     <span class="switch">
                                      <label>
                                       <input type="checkbox" checked="checked" name="status" class="status" data-url="' . route('user.status',$row->id) . '" disabled/>
                                       <span></span>
                                      </label>
                                     </span>
                                    </div>';
                                }else{
                                    $btn .= '<div class="form-group">
                                    <div class="col-3">
                                     <span class="switch">
                                      <label>
                                       <input type="checkbox" name="status" class="status" data-url="' . route('user.status',$row->id) . '" disabled/>
                                       <span></span>
                                      </label>
                                     </span>
                                    </div>';
                                }

                                return $btn;
                            })
                            ->orderColumn('role', function ($query, $order) {
                                $query->join('roles', 'users.role_id', '=', 'roles.id')
                                    ->orderBy('roles.name', $order);

                                // $query->whereHas('role',function($q) use ($order) {
                                //     $q->orderBy('name', $order);
                                // });
                            })
                            ->orderColumn('first_name', function ($query, $order) {
                                $query->orderBy('first_name', $order);
                            })
                            ->orderColumn('last_name', function ($query, $order) {
                                $query->orderBy('last_name', $order);
                            })
                            ->orderColumn('email', function ($query, $order) {
                                $query->orderBy('email', $order);
                            })
                            ->orderColumn('resignation_date', function ($query, $order) {
                                $query->orderBy('resignation_date', $order);
                            })
                            ->orderColumn('no_of_days', function ($query, $order) {
                                $query->orderBy('resignation_date', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->orderColumn('territory', function ($query, $order) {
                                $query->select('users.*')->join('territories', 'users.territory_id', '=', 'territories.id')
                                    ->orderBy('territories.description', $order);

                            })
                            ->rawColumns(['action', 'role','status','territory'])
                            ->make(true);
    }
}
