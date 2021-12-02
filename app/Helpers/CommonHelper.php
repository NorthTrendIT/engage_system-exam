<?php
use App\Models\LoginLog;
use App\Models\RoleModuleAccess;
use App\Models\ActivityLog;
use App\Models\ActivityMaster;
use App\Models\Module;
use Auth as Auth;

function add_login_log(){
	$insert = array(
					'user_id' => Auth::id(),
					'ip_address' => request()->ip(),
				);

	LoginLog::insert($insert);
}

function get_login_user_profile(){
	if(@Auth::user()->profile && file_exists(public_path('sitebucket/users/') . "/" . @Auth::user()->profile)){
		return asset('sitebucket/users/'.@Auth::user()->profile);
	}else{
		return false;
	}
}

function get_valid_file_url($path,$name)
{
	if(file_exists(public_path('/') . $path."/" . $name)){
		return asset($path."/" . $name);
	}else{
		return false;
	}
}

function get_user_role_module_access($role_id){

    $access = array();

    $role_module_access = RoleModuleAccess::where('role_id',$role_id)->get();
    foreach ($role_module_access as $value) {

        if($value->module->slug){
            $access[$value->module->slug] = $value->toArray();
        }
    }

    return $access;
}

function add_log($activity_id, $data = NULL){
    $log = new ActivityLog;
    $log->ip_address = \Request::ip();
    $log->activity_id = $activity_id;
    $log->user_id = \Auth::id();
    $log->data = json_encode($data);
    $log->save();
}

function userrole(){
	return @Auth::user()->role_id;
}

function userdepartment(){
    return @Auth::user()->department_id;
}

function get_modules(){

    $result = array();

    $module = Module::all();
    foreach ($module as $value) {

        if($value->slug){
            $result[$value->slug] = $value->toArray();
        }
    }

    return $result;
}
