<?php
use App\Models\LoginLog;
use App\Models\ActivityLog;
use App\Models\ActivityMaster;

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

function add_log($user_id, $activity_id, $data = NULL, $ip_address = NULL){
    $log = new ActivityLog;
    $log->ip_address = $ip_address;
    $log->activity_id = $activity_id;
    $log->user_id = $user_id;
    $log->data = $data;
    $log->save();
}
