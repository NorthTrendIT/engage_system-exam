<?php
use App\Models\LoginLog;

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