<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Department;
use App\Models\DepartmentRole;
use Validator;
use DataTables;
use Auth;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return view('department.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $roles = Role::where('id','!=',1)->get();
      return view('department.add',compact('roles'));
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
                    'name' => 'required|max:185|unique:departments,name,NULL,id,deleted_at,NULL',
                );

        if(isset($input['id'])){
            $rules['name'] = 'required|max:185|unique:departments,name,'.$input['id'].',id,deleted_at,NULL';
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){
                $obj = Department::find($input['id']);
                $message = "Department details updated successfully.";
            }else{
                $obj = new Department();
                $message = "New Department created successfully.";
            }

            $obj->fill($input)->save();

            if($obj->id){
                if(isset($input['roles'])){
                    $roles = $input['roles'];

                    $role_ids = [];
                    foreach ($roles as $key => $value) {
                        $role_ids[] = $key;
                        $insert = array(
                                    'department_id' => $obj->id,
                                    'role_id' => $key,
                                );

                        $dr_obj = DepartmentRole::updateOrCreate($insert,$insert);
                    }

                    DepartmentRole::where('department_id',$obj->id)->whereNotIn('role_id',$role_ids)->delete();

                }else{
                    DepartmentRole::where('department_id',$obj->id)->delete();
                }
            }

            if($message == "New Department created successfully."){
                // Add Department Created log
                add_log(9, array('department_id' => $obj->id));
            } else if($message == "Department details updated successfully."){
                // Add Department Updated log
                add_log(10, array('department_id' => $obj->id));
            }

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
      $data = Department::where('id',$id)->firstOrFail();

      $tree = json_encode($this->getDepartmentTreeData($id));

      return view('department.view',compact('data','tree'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = Department::where('id',$id)->whereNotIn('id',[1])->firstOrFail();
        $roles = Role::where('id','!=',1)->get();

        $department_roles = array();
        if($edit->roles){
          $department_roles = $edit->roles->toArray();
          $department_roles = array_column($department_roles, 'role_id');
        }

        return view('department.add',compact('roles','edit','department_roles'));
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
        $data = Department::find($id);
        if(!is_null($data)){
            $data->delete();

            // Add Department Deleted log.
            add_log(11, array('department_data' => $data));

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function updateStatus($id)
    {
        $data = Department::find($id);
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

        $data = Department::query();

        if($request->filter_status != ""){
            $data->where('departments.is_active',$request->filter_status);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('departments.name','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('departments.id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {

                                $btn = '<a href="' . route('department.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('department.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete" style="margin-right: 5px;">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                $btn .= '<a href="' . route('department.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                if(!in_array($row->id, [1])){
                                  return $btn;
                                }else{
                                  return "-";
                                }
                            })
                            ->addColumn('status', function($row) {

                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<a href="javascript:"  data-url="' . route('department.status',$row->id) . '" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                                }else{
                                    $btn .= '<a href="javascript:"  data-url="' . route('department.status',$row->id) . '" class="btn btn-sm btn-light-danger btn-inline status">Inctive</a>';
                                }

                                if(in_array($row->id, [1])){
                                  $btn = '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline">Active</a>';
                                  return $btn;
                                }

                                return $btn;
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

    public function getDepartmentTreeData($id){

      $result = $children = array();
      $result['children'] = array();
      $department = Department::find($id);

      if($department){
          $temp = array(
                          'name' => "Department",
                          'title' => @$department->name,
                      );
          $result = array_merge($result,$temp);

          $parent_departments = DepartmentRole::where('department_id',$id)->get();
          if(count($parent_departments)){
              foreach ($parent_departments as $key => $value) {
                  $temp = array(
                              'name' => "Role",
                              'title' => @$value->role->name,
                          );

                  $children[$key] = $temp;
              }

          }

          $result['children'] = array_merge($result['children'],$children);
      }

      return $result;
    }
}
