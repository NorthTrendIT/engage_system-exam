<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Auth;
class LoginController extends Controller
{
    public function index()
    {
    	return view('auth.login');
    }

    public function checkLogin(Request $request)
    {
    	$input = $request->all();
    	
    	$rules = array(
    				'email' => 'required|exists:users,email',
    				'password' => 'required'
    			);

    	$validator = Validator::make($input,$rules);

    	if($validator->fails()){
    		$response = array('status'=>false,'message'=>$validator->errors()->first());
    	}else{

    		$user = User::where('email',$input['email'])->first();

    		if($user){

    			if(!$user->is_active){
    				return $response = array('status'=>false,'message'=>"Your account has been deactivated by administrator. Please contact system admin.");
    			}

                if(is_null($user->role)){
                    return $response = array('status'=>false,'message'=>"Your role has been deleted by administrator. Please contact system admin.");
                }

    			$credentials = $request->only(['email', 'password']);

    			if (!is_null($user) && Auth::attempt($credentials)) {
	                
	                add_login_log();
	                
	                $response = [
	                    'status' => true,
	                    'message' => 'Success ! Login successful.',
	                ];
	            }else{
	                $response = [
	                    'status' => false,
	                    'message' => 'Failed ! Invalid login credentials.'
	                ];
	            }

    		}
    	}

    	return $response;
    }
}
