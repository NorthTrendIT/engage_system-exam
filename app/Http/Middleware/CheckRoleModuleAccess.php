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
            $status = false;
            $message = "Oops ! you have not access for the module.";;
            
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
                $status = true;
                
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

                // Invoice Module
                if(!isset($access['invoice']) && in_array($request->route()->getName(), ['invoices.index','invoices.get-all','invoices.sync-invoices'])){

                    $status = false;
                    $message = "Oops ! you have not access for invoice module.";
                }else{

                    if(in_array($request->route()->getName(), ['invoices.index','invoices.get-all'])){
                       
                        if($access['invoice']['view_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for invoice module.";

                        }
                    }elseif(in_array($request->route()->getName(), ['invoices.sync-invoices'])){
                        if($access['invoice']['add_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for sync invoices.";

                        }
                    }
                }

                // Order Module
                if(!isset($access['order']) && in_array($request->route()->getName(), ['orders.index','orders.get-all','orders.sync-orders'])){

                    $status = false;
                    $message = "Oops ! you have not access for order module.";
                }else{

                    if(in_array($request->route()->getName(), ['orders.index','orders.get-all'])){
                       
                        if($access['order']['view_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for order module.";

                        }
                    }elseif(in_array($request->route()->getName(), ['orders.sync-orders'])){
                        if($access['order']['add_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for sync orders.";

                        }
                    }
                }

                // Location Module
                if(!isset($access['location']) && in_array($request->route()->getName(), ['location.index','location.get-all','location.create','location.store','location.edit','location.status','location.destroy']) ){

                    $status = false;
                    $message = "Oops ! you have not access for location module.";

                }else{

                    if(in_array($request->route()->getName(), ['location.index','location.get-all'])){
                       
                        if($access['location']['view_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for location module.";

                        }

                    }elseif(in_array($request->route()->getName(), ['location.create'])){

                        if($access['location']['add_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for create location.";

                        }
                       
                    }elseif(in_array($request->route()->getName(), ['location.store'])){
                       
                        if(isset($request->id)){
                            if($access['location']['edit_access'] != 1){
                                
                                $status = false;
                                $message = "Oops ! you have not access for edit location.";

                            }
                        }else{
                            if($access['location']['add_access'] != 1){
                                
                                $status = false;
                                $message = "Oops ! you have not access for create location.";

                            }
                        }

                    }elseif(in_array($request->route()->getName(), ['location.edit','location.status'])){

                        if($access['location']['edit_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for edit location.";

                        }
                       
                    }elseif(in_array($request->route()->getName(), ['location.destroy'])){
                        if($access['location']['delete_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for delete location.";

                        }
                    }
                }

                // Department Module
                if(!isset($access['department']) && in_array($request->route()->getName(), ['department.index','department.get-all','department.create','department.store','department.edit','department.status','department.destroy']) ){

                    $status = false;
                    $message = "Oops ! you have not access for department module.";

                }else{

                    if(in_array($request->route()->getName(), ['department.index','department.get-all'])){
                       
                        if($access['department']['view_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for department module.";

                        }

                    }elseif(in_array($request->route()->getName(), ['department.create'])){

                        if($access['department']['add_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for create department.";

                        }
                       
                    }elseif(in_array($request->route()->getName(), ['department.store'])){
                       
                        if(isset($request->id)){
                            if($access['department']['edit_access'] != 1){
                                
                                $status = false;
                                $message = "Oops ! you have not access for edit department.";

                            }
                        }else{
                            if($access['department']['add_access'] != 1){
                                
                                $status = false;
                                $message = "Oops ! you have not access for create department.";

                            }
                        }

                    }elseif(in_array($request->route()->getName(), ['department.edit','department.status'])){

                        if($access['department']['edit_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for edit department.";

                        }
                       
                    }elseif(in_array($request->route()->getName(), ['department.destroy'])){
                        if($access['department']['delete_access'] != 1){
                            
                            $status = false;
                            $message = "Oops ! you have not access for delete department.";

                        }
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
        return $next($request);
    }
}
