<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Validator;
use DataTables;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parents = Location::whereNull('parent_id')->get();
        return view('location.index',compact('parents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parents = Location::whereNull('parent_id')->get();
        return view('location.add',compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $rules = array(
                    'name' => 'required|max:185|unique:locations,name,NULL,id,deleted_at,NULL',
                    'parent_id' => 'nullable|exists:locations,id',
                );

        if(isset($input['id'])){
            $rules['name'] = 'required|max:185|unique:locations,name,'.$input['id'].',id,deleted_at,NULL';
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){
                $obj = Location::find($input['id']);
                $message = "Location details updated successfully.";
            }else{
                $obj = new Location();
                $message = "New Location created successfully.";
            }

            $obj->fill($input)->save();

            $response = ['status'=>true,'message'=>$message];
        }

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
        $edit = Location::where('id',$id)->firstOrFail();
        $parents = Location::where('id','!=',$id)->whereNull('parent_id')->get();

        return view('location.add',compact('edit','parents'));
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
        $data = Location::find($id);
        if(!is_null($data)){
            $data->delete();
            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function updateStatus($id)
    {
        $data = Location::find($id);
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

        $data = Location::query();

        if($request->filter_parent != ""){
            $data->where('locations.parent_id',$request->filter_parent);
        }

        if($request->filter_status != ""){
            $data->where('locations.is_active',$request->filter_status);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('locations.name','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('locations.id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('location.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('location.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';
                                
                                return $btn;
                            })
                            ->addColumn('parent', function($row) {
                                
                                if(@$row->parent){
                                    return @$row->parent->name;
                                }else{
                                    return "-";
                                }
                            })
                            ->addColumn('status', function($row) {
                                
                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<a href="javascript:"  data-url="' . route('location.status',$row->id) . '" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                                }else{
                                    $btn .= '<a href="javascript:"  data-url="' . route('location.status',$row->id) . '" class="btn btn-sm btn-light-danger btn-inline status">Inctive</a>';
                                }

                                return $btn;
                            })
                            ->orderColumn('parent', function ($query, $order) {
                                $query->select('locations.*')->join('locations as l', 'locations.parent_id', '=', 'l.id')
                                    ->orderBy('l.name', $order);
                            })
                            ->orderColumn('name', function ($query, $order) {
                                $query->orderBy('name', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->rawColumns(['action', 'role','status'])
                            ->make(true);
    }
}
