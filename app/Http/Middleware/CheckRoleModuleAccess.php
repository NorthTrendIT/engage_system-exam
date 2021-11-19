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
                if(!isset($access['user']) && in_array($request->route()->getName(), ['user.index','user.get-all','user.create','user.store','user.edit','user.status','user.destroy']) ){

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

                // Customer Module
                if(!isset($access['customer']) && in_array($request->route()->getName(), ['customer.index','customer.get-all','customer.sync-customers']) ){

                    $status = false;
                    $message = "Oops ! you have not access for customer module.";
                }else{

                    if(in_array($request->route()->getName(), ['customer.index','customer.get-all'])){
                       
                        if($access['customer']['view_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for customer module.";

                        }
                    }elseif(in_array($request->route()->getName(), ['customer.sync-customers'])){
                        if($access['customer']['add_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for sync customers.";

                        }
                    }
                }

                // Sales Person Module
                if(!isset($access['sales-person']) && in_array($request->route()->getName(), ['sales-persons.index','sales-persons.get-all','sales-persons.sync-sales-persons'])){

                    $status = false;
                    $message = "Oops ! you have not access for sales person module.";
                }else{

                    if(in_array($request->route()->getName(), ['sales-persons.index','sales-persons.get-all'])){
                       
                        if($access['sales-person']['view_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for sales person module.";

                        }
                    }elseif(in_array($request->route()->getName(), ['sales-persons.sync-sales-persons'])){
                        if($access['sales-person']['add_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for sync sales persons.";

                        }
                    }
                }


                // Product Module
                if(!isset($access['product']) && in_array($request->route()->getName(), ['product.index','product.get-all','product.sync-products','product.store','product.edit','product.show']) ){

                    $status = false;
                    $message = "Oops ! you have not access for product module.";
                }else{

                    if(in_array($request->route()->getName(), ['product.index','product.get-all','product.show'])){
                       
                        if($access['product']['view_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for product module.";

                        }
                    }elseif(in_array($request->route()->getName(), ['product.sync-products'])){
                        if($access['product']['add_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for sync products.";

                        }
                    }elseif(in_array($request->route()->getName(), ['product.store'])){
                       
                        if(isset($request->id)){
                            if($access['product']['edit_access'] != 1){
                                
                                $status = false;
                                $message = "Oops ! you have not access for edit product.";

                            }
                        }else{
                            if($access['product']['add_access'] != 1){
                                
                                $status = false;
                                $message = "Oops ! you have not access for create product.";

                            }
                        }

                    }elseif(in_array($request->route()->getName(), ['product.edit'])){

                        if($access['product']['edit_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for edit product.";

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
