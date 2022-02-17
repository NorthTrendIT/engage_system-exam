<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\PasswordReset;
use Mail;

class ForgotPasswordController extends Controller
{
    public function index(){
        return view('auth.forgot_password');
    }

    public function email(Request $request){
        $input = $request->all();

        $rules = [
                    'email' => 'required|exists:users',
                ];

        $message = array(
                        'email.exists'=>"This email address is not valid !"
                    );

        $validator = Validator::make($input,$rules,$message);
        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            $where = array(
                        'email'=>$input['email'],
                        'is_active'=>1,
                    );
            $user = User::where($where)->first();
            if(!is_null($user)){

                $token = \Str::random(50);
                PasswordReset::updateOrCreate(['email'=>$user->email],['token'=>$token]);

                $link = route('forgot-password.reset',[$user->email,$token]);

                Mail::send('emails.reset_password', array('link'=>$link), function($message) use($user) {
                    $message->to($user->email, $user->name)
                            ->subject('Reset Password Link');
                });

                $response = ['status'=>true,'message'=>'Password Reset Link sent to your email.'];

            }else{
                $response = ['status'=>false,'message'=>'This email address not valid please contact to administrative.'];
            }
        }

        return $response;
    }

    public function showResetPasswordForm($email,$token){
        $user = PasswordReset::where('email',$email)->where('token',$token)->firstOrFail();
        return view('auth.reset_password',compact('email','token'));
    }

    public function resetPassword(Request $request){
        $input = $request->all();
        
        $user = User::where('email',$input['email'])->first();
        if(is_null($user)){
            return $response = ['status'=>false,'message'=>'User not found !'];
        }

        $rules = array(
                    'new_password' => 'required|max:20|regex:/^(?=.*\d)(?=.*[@$!%*#?&_-~<>;])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@$!%*#?&_-~<>;]{8,20}$/',
                    'confirm_password' => 'required|same:new_password',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $user->password = \Hash::make($input['confirm_password']);
            $user->save();

            PasswordReset::where('token',$input['token'])->delete();
            $response = ['status'=>true,'message'=>'Password change successfully !'];           
        }
        return $response;
    }
}
