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
      $ids = array();
      $roles = Role::where('id','!=',1)->whereNotNull('parent_id')->get();
      if(count($roles)){
        $ids = array_column($roles->toArray(), 'parent_id');
      }
      $parents = Role::where('id','!=',1)->whereNull('parent_id')->orwhereIn('id',$ids)->get();
      return view('role.index',compact('parents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $modules = get_modules();
      $parents = Role::where('id','!=',1)->get();
      return view('role.add',compact('modules','parents'));
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

        // dd($input);

        $rules = array(
                    'all_module_access' => 'required',
                    'name' => 'required|max:185|unique:roles,name,NULL,id,deleted_at,NULL',
                    'parent_id' => 'nullable|exists:roles,id',
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

              // Add Role Updatede log.
              add_log(7, array('role_id' => $role->id));
            }else{
              $role = new Role();
              $message = "New Role created successfully.";
              
              // Add Role Created log.
              add_log(6, array('role_id' => $role->id));
            }

            $role->fill($input)->save();

            if($role->id){
                if($role->all_module_access == 0 && isset($input['modules'])){
                    $modules = $input['modules'];

                    $module_ids = [];
                    foreach ($modules as $key => $value) {
                        $module_ids[] = $key;
                        $insert = array(
                                    'role_id' => $role->id,
                                    'module_id' => $key,
                                    'access' => true,
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

                    $modules = Module::all();

                    foreach ($modules as $key => $value) {
                        $insert = array(
                                    'role_id' => $role->id,
                                    'module_id' => $value->id,
                                    'access' => true,
                                );

                        $obj = RoleModuleAccess::updateOrCreate(
                                            [
                                              'role_id' => $role->id,
                                              'module_id' => $value->id
                                            ],
                                            $insert
                                        );
                    }
                }elseif ($role->all_module_access == 0 && !isset($input['modules'])) {
                  RoleModuleAccess::where('role_id',$role->id)->delete();
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
        $edit = Role::whereNotIn('id',[3,1])->where('id',$id)->firstOrFail();
        $modules = get_modules();;
        $parents = Role::where('id','!=',$id)->where('id','!=',1)->get();

        $role_module_access = array();
        if($edit->role_module_access){
          $role_module_access = $edit->role_module_access->toArray();
          $key = array_column($role_module_access, 'module_id');

          $role_module_access = array_combine($key, $role_module_access);
        }
        return view('role.add',compact('modules','edit','role_module_access','parents'));
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

            // Add Role Deleted log.
            add_log(8, array('role_data' => $data));

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Role::where('id','!=',1)->orderBy('id','desc');

        if($request->filter_parent != ""){
            $data->where('parent_id',$request->filter_parent);
        }

        return DataTables::of($data->get())
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('role.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm ">
                                    <i class="fa fa-pencil"></i>
                                  </a>';

                                if(!in_array($row->id, [2,4])){
                                  $btn .= ' <a href="javascript:void(0)" data-url="' . route('role.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                      <i class="fa fa-trash"></i>
                                    </a>';
                                }

                                if(in_array($row->id, [3])){
                                  return "-";
                                }

                                return $btn;
                            })
                            ->addColumn('access', function($row) {

                                if($row->all_module_access == 1){
                                    return "All Module Access";
                                }else{
                                    return "Custom Module Access";
                                }
                            })
                            ->addColumn('parent', function($row) {

                                if(@$row->parent){
                                    return @$row->parent->name;
                                }else{
                                    return "-";
                                }
                            })
                            ->rawColumns(['action', 'access'])
                            ->make(true);
    }

    public function getRoleChart(){
      $result = $children = array();
      $result['children'] = array();
      $role = Role::find(1);

      if($role){
        $temp = array(
                        'name' => @$role->name,
                    );
        $result = array_merge($result,$temp);

        $parent_roles = Role::where('id','!=',1)->whereNull('parent_id')->get();
        if(count($parent_roles)){

            foreach ($parent_roles as $key => $value) {
                $temp = array(
                            'name' => @$value->name,
                        );

                $child = $this->getRoleChildData($value->id);

                if(count($child)){
                  $temp['children'] = $child;
                }

                $children[$key] = $temp;
            }

        }

        $result['children'] = array_merge($result['children'],$children);
      }

      $tree = json_encode($result);

      return view('role.chart',compact('tree'));
    }

    public function getRoleChildData($role_id)
    {
      $result = array();
      $roles = Role::where('parent_id',$role_id)->get();

      if(count($roles)){
          foreach ($roles as $key => $value) {

              $temp = array(
                          'name' => @$value->name,
                      );

              $child = $this->getRoleChildData($value->id);

              if(count($child)){
                  $temp['children'] = $child;
              }

              $result[$key] = $temp;
          }
      }

      return $result;
    }
}
