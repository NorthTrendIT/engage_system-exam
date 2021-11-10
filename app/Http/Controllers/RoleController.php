<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Module;
use App\Models\RoleModuleAccess;
use Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modules = Module::all();
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
                $message = "New Role created successfully.";
            }else{
                $role = new Role();
                $message = "Role details updated successfully.";
            }

            $role->fill($input)->save();

            if($role->id){
                RoleModuleAccess::where('role_id',$role->id)->delete();

                $modules = $input['modules'];

                foreach ($modules as $key => $value) {
                    $insert = array(
                                'role_id' => $role->id,
                                'module_id' => $key,
                                'add_access' => isset($value['add']) && $value['add'] == 1 ? true : false,
                                'edit_access' => isset($value['edit']) && $value['edit'] == 1 ? true : false,
                                'view_access' => isset($value['view']) && $value['view'] == 1 ? true : false,
                                'delete_access' => isset($value['delete']) && $value['delete'] == 1 ? true : false,
                            );
                    $obj = new RoleModuleAccess();
                    $obj->fill($insert)->save();
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
}
