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
                    return $response = array('status'=>false,'message'=>"Your role has not available. Please contact system admin.");
                }

    			$credentials = $request->only(['email', 'password']);

    			if (!is_null($user) && Auth::attempt($credentials)) {

	                add_login_log();
                    add_log(1, null);

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

    public function loginByLink($hash = ""){

    	if($hash){
    		$hash = decryptValue($hash);
    		
    		$hash = explode("-", $hash);
    		$id = @$hash[0];
    		$time = @$hash[1];

    		if($id && $time){
    			$expiry = strtotime('+24 hours', $time);
    			
    			if($time <= $expiry){
		    		$user = User::where('is_active',true)->where('id', $id)->first();

		    		if($user){

		                if(!is_null($user->role)){
			                Auth::loginUsingId($id);

			                //save log
            				add_log(45, $user->toArray());

			                \Session::flash('login_success_message', "Login successfully !");
			                return redirect()->route('home');
		                }
		    			
		    		}
    			}

    		}

    	}

    	return abort(404);
    	
    }
}
