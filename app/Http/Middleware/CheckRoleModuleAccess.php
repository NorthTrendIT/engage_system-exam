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
        // return $next($request);
        if(Auth::user()->role_id != 1){ //Not Super Admin
            $status = false;
            $message = "Oops! you don't have access to this module.";

            $role = Auth::user()->role;

            if(is_null($role)){
                return abort(404);
            }

            $access = get_user_role_module_access($role->id);

            if(!empty($access)){
                $status = true;

                // User Module
                if(in_array($request->route()->getName(), ['role.index','role.get-all','role.show'])){

                    if(@$access['view-role'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for role module.";

                    }

                }elseif(in_array($request->route()->getName(), ['role.create'])){

                    if(@$access['add-role'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for create role.";

                    }

                }elseif(in_array($request->route()->getName(), ['role.store'])){

                    if(isset($request->id)){
                        if(@$access['edit-role'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for edit role.";

                        }
                    }else{
                        if(@$access['add-role'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for create role.";

                        }
                    }

                }elseif(in_array($request->route()->getName(), ['role.edit','role.status'])){

                    if(@$access['edit-role'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for edit role.";

                    }

                }elseif(in_array($request->route()->getName(), ['role.destroy'])){
                    if(@$access['delete-role'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for delete role.";

                    }
                }

                // User Module
                if(in_array($request->route()->getName(), ['user.index','user.get-all','user.show'])){

                    if(@$access['view-user'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for user module.";

                    }

                }elseif(in_array($request->route()->getName(), ['user.create'])){

                    if(@$access['add-user'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for create user.";

                    }

                }elseif(in_array($request->route()->getName(), ['user.store'])){

                    if(isset($request->id)){
                        if(@$access['edit-user'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for edit user.";

                        }
                    }else{
                        if(@$access['add-user'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for create user.";

                        }
                    }

                }elseif(in_array($request->route()->getName(), ['user.edit','user.status'])){

                    if(@$access['edit-user'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for edit user.";

                    }

                }elseif(in_array($request->route()->getName(), ['user.destroy'])){
                    if(@$access['delete-user'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for delete user.";

                    }
                }

                // Customer Module
                if(in_array($request->route()->getName(), ['customer.index','customer.get-all'])){

                    if(@$access['view-customer'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for customer module.";

                    }
                }elseif(in_array($request->route()->getName(), ['customer.sync-customers'])){
                    if(@$access['add-customer'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for sync customers.";

                    }
                }

                // Sales Person Module
                if(in_array($request->route()->getName(), ['sales-persons.index','sales-persons.get-all'])){

                    if(@$access['view-sales-person'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for sales person module.";

                    }
                }elseif(in_array($request->route()->getName(), ['sales-persons.sync-sales-persons'])){
                    if(@$access['add-sales-person'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for sync sales persons.";

                    }
                }


                // Product Module
                if(in_array($request->route()->getName(), ['product.index','product.get-all','product.show'])){

                    if(@$access['view-product'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for product module.";

                    }
                }elseif(in_array($request->route()->getName(), ['product.sync-products'])){
                    if(@$access['add-product'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for sync products.";

                    }
                }elseif(in_array($request->route()->getName(), ['product.store'])){

                    if(isset($request->id)){
                        if(@$access['edit-product'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for edit product.";

                        }
                    }else{
                        if(@$access['add-product'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for create product.";

                        }
                    }
                }elseif(in_array($request->route()->getName(), ['product.edit'])){

                    if(@$access['edit-product'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for edit product.";

                    }
                }

                //Recommended Product
                if(in_array($request->route()->getName(), ['product.recommended', 'product.recommended-add'])){
                    if(@$access['view-recommended-product'] != 1){
                        $status = false;
                        $message = "Oops! access denied.";
                    }
                    if(@$access['add-recommended-product'] != 1){
                        $status = false;
                        $message = "Oops! access denied."; 
                    }
                }

                // Invoice Module
                if(in_array($request->route()->getName(), ['invoices.index','invoices.get-all'])){
                    if(@$access['view-invoice'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for invoice module.";

                    }
                }elseif(in_array($request->route()->getName(), ['invoices.sync-invoices'])){
                    if(@$access['add-invoice'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for sync invoices.";

                    }
                }

                // Order Module
                if(in_array($request->route()->getName(), ['orders.index','orders.get-all'])){

                    if(@$access['view-order'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for order module.";

                    }
                }elseif(in_array($request->route()->getName(), ['orders.sync-orders'])){
                    if($access['add-order'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for sync orders.";

                    }
                }

                // Location Module
                if(in_array($request->route()->getName(), ['location.index','location.get-all'])){

                    if(@$access['view-location'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for location module.";

                    }

                }elseif(in_array($request->route()->getName(), ['location.create'])){

                    if(@$access['add-location'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for create location.";

                    }

                }elseif(in_array($request->route()->getName(), ['location.store'])){

                    if(isset($request->id)){
                        if(@$access['edit-location'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for edit location.";

                        }
                    }else{
                        if(@$access['add-location'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for create location.";

                        }
                    }

                }elseif(in_array($request->route()->getName(), ['location.edit','location.status'])){

                    if(@$access['edit-location'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for edit location.";

                    }

                }elseif(in_array($request->route()->getName(), ['location.destroy'])){
                    if(@$access['delete-location'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for delete location.";

                    }
                }

                // Department Module
                if(in_array($request->route()->getName(), ['department.index','department.get-all'])){

                    if(@$access['view-department'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for department module.";

                    }

                }elseif(in_array($request->route()->getName(), ['department.create'])){

                    if(@$access['add-department'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for create department.";

                    }

                }elseif(in_array($request->route()->getName(), ['department.store'])){

                    if(isset($request->id)){
                        if(@$access['edit-department'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for edit department.";

                        }
                    }else{
                        if(@$access['add-department'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for create department.";

                        }
                    }

                }elseif(in_array($request->route()->getName(), ['department.edit','department.status'])){

                    if(@$access['edit-department'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for edit department.";

                    }

                }elseif(in_array($request->route()->getName(), ['department.destroy'])){
                    if(@$access['delete-department'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for delete department.";

                    }
                }

                // Product List Module
                // if(in_array($request->route()->getName(), ['product-list.index','product-list.show'])){

                //     if(@$access['view-product-list'] != 1){

                //         $status = false;
                //         $message = "Oops ! you have not access for product list module.";

                //     }
                // }


                // Customer Module
                if(in_array($request->route()->getName(), ['customer-group.index','customer-group.get-all'])){

                    if(@$access['view-customer-group'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for customer group module.";

                    }
                }elseif(in_array($request->route()->getName(), ['customer-group.sync-customer-groups'])){
                    if(@$access['add-customer-group'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for sync customer groups.";

                    }
                }


                // Class Module
                if(in_array($request->route()->getName(), ['class.index','class.get-all'])){

                    if(@$access['view-class'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for class module.";

                    }
                }

                // Product List Module
                if(in_array($request->route()->getName(), ['product-list.index','product-list.get-all','product-list.show'])){

                    if(@$access['view-product-list'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for product list module.";

                    }
                }

                // My Promotions Module
                if(in_array($request->route()->getName(), ['customer-promotion.index','customer-promotion.get-all','customer-promotion.show','customer-promotion.get-all-product-list','customer-promotion.store-interest','customer-promotion.product-detail','customer-promotion.order.index','customer-promotion.order.create','customer-promotion.order.show','customer-promotion.order.store','customer-promotion.order.get-all'])){

                    if(@$access['view-my-promotions'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for my promotions module.";

                    }
                }

                // Product Group Module
                if(in_array($request->route()->getName(), ['product-group.index','product-group.get-all'])){

                    if(@$access['view-product-group'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for product group module.";

                    }
                }elseif(in_array($request->route()->getName(), ['product-group.sync-product-groups'])){
                    if(@$access['add-product-group'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for sync product groups.";

                    }
                }


                // Warranty Module
                if(in_array($request->route()->getName(), ['warranty.index','warranty.get-all','warranty.show','warranty.export-view'])){

                    if(@$access['view-warranty'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for warranty module.";

                    }

                }elseif(in_array($request->route()->getName(), ['warranty.create'])){

                    if(@$access['add-warranty'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for create warranty.";

                    }

                }elseif(in_array($request->route()->getName(), ['warranty.store'])){

                    if(isset($request->id)){
                        if(@$access['edit-warranty'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for edit warranty.";

                        }
                    }else{
                        if(@$access['add-warranty'] != 1){

                            $status = false;
                            $message = "Oops ! you have not access for create warranty.";

                        }
                    }

                }elseif(in_array($request->route()->getName(), ['warranty.edit'])){

                    if(@$access['edit-warranty'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for edit warranty.";

                    }

                }elseif(in_array($request->route()->getName(), ['warranty.destroy'])){
                    if(@$access['delete-warranty'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for delete warranty.";

                    }
                }


                // Customer Delivery Schedule Module
                if(in_array($request->route()->getName(), ['customer-delivery-schedule.all-view'])){

                    if(@$access['view-all-customer-delivery-schedule'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for view all customer delivery schedule module.";

                    }
                }

                // Benefits Module
                if(in_array($request->route()->getName(), ['product.benefits','benefits.assignment'])){
                    if(@$access['view-product-benefits'] != 1){

                        $status = false;
                        $message = "Oops ! you have not access for this module.";

                    }
                }
            }

            // Added validation access page =====================================================

            $arr_module = (isset($access['module_id'])) ? $access['module_id'] : []; // delivey schedule
            if(in_array($request->route()->getName(), ['customer-delivery-schedule.index']) && !in_array('1001', array_values($arr_module))){
                return redirect()->route('home');
            }

            $arr_parent = (isset($access['parent_id'])) ? $access['parent_id'] : []; //ss asignment
            if(in_array($request->route()->getName(), ['customers-sales-specialist.index']) && !in_array('72', array_values($arr_parent))){
                return redirect()->route('home');
            }

            //end validation of accessing page ===================================================

            if(in_array($request->route()->getName(), ['news-and-announcement.index','news-and-announcement.get-all','news-and-announcement.show'])){
                $status = true;
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
