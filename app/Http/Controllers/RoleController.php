<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Module;
use App\Models\RoleModuleAccess;
use Validator;
use DataTables;
use Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('role.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modules = Module::where('slug','!=','role')->get();
        return view('role.add',compact('modules'));
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
                    'all_module_access' => 'required',
                    'name' => 'required|max:185|unique:roles,name,NULL,id,deleted_at,NULL'
                );

        if(isset($input['id'])){
            $rules['name'] = 'required|max:185|unique:roles,name,'.$input['id'].',id,deleted_at,NULL';
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){
                $role = Role::find($input['id']);
                $message = "Role details updated successfully.";
            }else{
                $role = new Role();
                $message = "New Role created successfully.";
            }

            $role->fill($input)->save();

            if($role->id){
                if(isset($input['modules'])){
                    $modules = $input['modules'];

                    $module_ids = [];
                    foreach ($modules as $key => $value) {
                        $module_ids[] = $key;
                        $insert = array(
                                    'role_id' => $role->id,
                                    'module_id' => $key,
                                    'add_access' => isset($value['add']) && $value['add'] == 1 ? true : false,
                                    'edit_access' => isset($value['edit']) && $value['edit'] == 1 ? true : false,
                                    'view_access' => isset($value['view']) && $value['view'] == 1 ? true : false,
                                    'delete_access' => isset($value['delete']) && $value['delete'] == 1 ? true : false,
                                );

                        $obj = RoleModuleAccess::updateOrCreate(
                                            [
                                                'role_id' => $role->id,
                                                'module_id' => $key
                                            ],
                                            $insert
                                        );
                    }

                    RoleModuleAccess::where('role_id',$role->id)->whereNotIn('module_id',$module_ids)->delete();

                }elseif ($role->all_module_access == 1) {
                    
                    $modules = Module::where('slug','!=','role')->get();

                    foreach ($modules as $key => $value) {
                        $insert = array(
                                    'role_id' => $role->id,
                                    'module_id' => $value->id,
                                    'add_access' => true,
                                    'edit_access' => true,
                                    'view_access' => true,
                                    'delete_access' => true,
                                );

                        $obj = RoleModuleAccess::updateOrCreate(
                                            [
                                                'role_id' => $role->id,
                                                'module_id' => $value->id
                                            ],
                                            $insert
                                        );
                    }

                }
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
        $edit = Role::where('id','!=',1)->where('id',$id)->firstOrFail();
        $modules = Module::where('slug','!=','role')->get();

        $role_module_access = array();
        if($edit->role_module_access){
            $role_module_access = $edit->role_module_access->toArray();
            $key = array_column($role_module_access, 'module_id');

            $role_module_access = array_combine($key, $role_module_access);

            // dd($role_module_access);
        }
        return view('role.add',compact('modules','edit','role_module_access'));
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
        $data = Role::find($id);
        if(!is_null($data)){
            $data->delete();
            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Role::where('id','!=',1)->orderBy('id','desc')->get();

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('role.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-color-success">
                                    <i class="fa fa-edit"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('role.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-color-danger delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';
                                
                                return $btn;
                            })
                            ->addColumn('access', function($row) {
                                
                                if($row->all_module_access == 1){
                                    return "All Module Access";
                                }else{
                                    return "Custom Module Access";
                                }
                            })
                            ->rawColumns(['action', 'access'])
                            ->make(true);
    }
}
