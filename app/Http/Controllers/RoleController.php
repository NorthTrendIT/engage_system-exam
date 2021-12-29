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

        // If not admin then show only its
        if(userrole() != 1){
            $roles = Role::where('user_id',Auth::id())->whereNotNull('parent_id')->get();
        }else{
            $roles = Role::where('id','!=',1)->whereNull('user_id')->whereNotNull('parent_id')->get();
        }


        if(count($roles)){
            $ids = array_column($roles->toArray(), 'parent_id');
        }
        // $parents = Role::where('id','!=',1)->whereNull('parent_id')->orwhereIn('id',$ids)->get();

        // If not admin then show only its
        if(userrole() != 1){
            $parents = Role::where('user_id',Auth::id())->whereNull('parent_id')->orwhereIn('id',$ids)->get();
        }else{
            $parents = Role::where('id','!=',1)->whereNull('user_id')->whereNull('parent_id')->orwhereIn('id',$ids)->get();
        }

        return view('role.index',compact('parents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        // If not admin then edit only its
        // $disable_modules = $this->getDisableModules();


        $modules = [];
        // If not admin then show only its
        if(userrole() != 1){
            $parents = Role::where('user_id',Auth::id())->get();
            $module = Module::whereIn('id', $this->getEnableModules())->get();

            foreach ($module as $value) {
                if($value->slug){
                    $modules[$value->slug] = $value->toArray();
                }
            }

        }else{
            $parents = Role::where('id','!=',1)->whereNull('user_id')->get();
            $modules = get_modules();
        }

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

            if(userrole() != 1){
                $input['user_id'] = Auth::id();
            }

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

                    if(userrole() != 1){
                        $modules = Module::whereIn('id', $this->getEnableModules())->get();
                    }else{
                        $modules = Module::all();
                    }

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
        $edit = Role::whereNotIn('id',[3,1])->where('id',$id);

        // If not admin then edit only its
        if(userrole() != 1){
            $edit->where('user_id',Auth::id());
        }
        $edit = $edit->firstOrFail();



        // If not admin then show only its
        if(userrole() != 1){
            $parents = Role::where('id','!=',$id)->where('user_id',Auth::id())->get();

            $module = Module::whereIn('id', $this->getEnableModules())->get();

            foreach ($module as $value) {
                if($value->slug){
                    $modules[$value->slug] = $value->toArray();
                }
            }
        }else{
            
            $parents = Role::where('id','!=',$id)->whereNull('user_id')->where('id','!=',1)->get();
            $modules = get_modules();
        }
        

        $role_module_access = array();
        if($edit->role_module_access){
            $role_module_access = $edit->role_module_access->toArray();
            $key = array_column($role_module_access, 'module_id');

            $role_module_access = array_combine($key, $role_module_access);
        }

        // If not admin then edit only its
        // $disable_modules = $this->getDisableModules();

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
        $data = Role::where('id', $id);

        // If not admin then edit only its
        if(userrole() != 1){
            $data->where('user_id',Auth::id());
        }
        $data = $data->first();

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

        // If not admin then edit only its
        if(userrole() != 1){
            $data->where('user_id',Auth::id());
        }else{
            $data->whereNull('user_id');
        }

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

        $parent_roles = Role::where('id','!=',1)->whereNull('user_id')->whereNull('parent_id')->get();
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

    public function getDisableModules(){
        
        // If not admin then edit only its
        $disable_modules = [];
        if(userrole() != 1){

            $get_user_role_module_access = array_keys(get_user_role_module_access(Auth::user()->role_id));
            $disable_modules = Module::whereNotNull('parent_id')->whereNotIn('slug',$get_user_role_module_access)->pluck('id')->toArray();
            $enable_modules = Module::whereIn('slug',$get_user_role_module_access)->pluck('id')->toArray();
            
            $modules = Module::whereNotIn('id',[1,2,3])->whereNull('parent_id')->get();
            foreach($modules as $m){
                $sub_modules = Module::where('parent_id',$m->id)->pluck('id')->toArray();

                if(!array_intersect($sub_modules, $enable_modules)){
                    array_push($disable_modules,$m->id);
                }
            }

            // User Management
            if(count(array_intersect([4,5,7], $disable_modules)) == 3){
                array_push($disable_modules,1);
            }
            // Customer Management
            if(count(array_intersect([8,9,10], $disable_modules)) == 3){
                array_push($disable_modules,2);
            }
            // Product Management
            if(count(array_intersect([13,17], $disable_modules)) == 2){
                array_push($disable_modules,3);
            }
        }

        return $disable_modules;
    }

    public function getEnableModules(){
        $get_user_role_module_access = array_keys(get_user_role_module_access(Auth::user()->role_id));

        $enable_modules = Module::whereIn('slug',$get_user_role_module_access)->pluck('id')->toArray();
            
        $modules = Module::whereNotIn('id',[1,2,3])->whereNull('parent_id')->get();
        foreach($modules as $m){
            $sub_modules = Module::where('parent_id',$m->id)->pluck('id')->toArray();

            if(array_intersect($sub_modules, $enable_modules)){
                array_push($enable_modules,$m->id);
            }
        }

        // User Management
        if(array_intersect([4,5,7], $enable_modules)){
            array_push($enable_modules,1);
        }
        // Customer Management
        if(array_intersect([8,9,10], $enable_modules)){
            array_push($enable_modules,2);
        }
        // Product Management
        if(array_intersect([13,17], $enable_modules)){
            array_push($enable_modules,3);
        }

        return $enable_modules;
    }
}
