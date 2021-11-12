<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Models\RoleModuleAccess;

class CheckRoleModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if(Auth::user()->role_id != 1){ //Not Super Admin
            $status = true;
            $message = "";
            
            $role = Auth::user()->role;

            if(is_null($role)){
                return abort(404);
            }

            $access = array();
            
            $role_module_access = RoleModuleAccess::where('role_id',$role->id)->get();
            foreach ($role_module_access as $value) {

                if($value->module->slug){
                    $access[$value->module->slug] = $value->toArray();
                }
            }

            if(!empty($access)){

                // User Module
                if(!isset($access['user'])){

                    $status = false;
                    $message = "Oops ! you have not access for user module.";

                }else{

                    if(in_array($request->route()->getName(), ['user.index','user.get-all'])){
                       
                        if($access['user']['view_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for user module.";

                        }

                    }elseif(in_array($request->route()->getName(), ['user.create'])){

                        if($access['user']['add_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for create user.";

                        }
                       
                    }elseif(in_array($request->route()->getName(), ['user.store'])){
                       
                        if(isset($request->id)){
                            if($access['user']['edit_access'] != 1){
                                
                                $status = false;
                                $message = "Oops ! you have not access for edit user.";

                            }
                        }else{
                            if($access['user']['add_access'] != 1){
                                
                                $status = false;
                                $message = "Oops ! you have not access for create user.";

                            }
                        }

                    }elseif(in_array($request->route()->getName(), ['user.edit','user.status'])){

                        if($access['user']['edit_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for edit user.";

                        }
                       
                    }elseif(in_array($request->route()->getName(), ['user.destroy'])){
                        if($access['user']['delete_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for delete user.";

                        }
                    }
                }

                if(!$status){
                    if($request->ajax()){
                        $response = array('status' => $status, "message" => $message);
                        return response()->json($response);
                    }else{
                        \Session::flash('role_access_error_message', $message);

                        if(url()->previous() == url()->current()){
                            return redirect()->route('home');
                        }else{
                            return redirect()->back();
                        }
                    }
                }

            }

        }
        return $next($request);
    }
}
