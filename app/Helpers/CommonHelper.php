<?php
use App\Models\LoginLog;
use App\Models\RoleModuleAccess;
use App\Models\ActivityLog;
use App\Models\ActivityMaster;
use App\Models\Module;
use App\Models\Cart;
use App\Models\Quotation;
use App\Models\Order;
use App\Models\Invoice;
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

        if(@$value->module->slug){
            $access[$value->module->slug] = $value->access;
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
    $log->type = "O";
    $log->status = null;
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

function get_product_customer_price($item_prices,$number, $discount = false, $discount_fix_amount = false)
{
    if(is_null($number)){
        $number = 1;
    }

    $item_prices = json_decode($item_prices,true);
    if(count($item_prices) > 0){

        $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));

        $price = $prices[$number]['Price'] ?? 0;

        if($discount){

            $discount_amount = ( ( $price * $discount ) / 100 );

            if($discount_fix_amount){

                if($discount_amount > $discount_fix_amount){
                    $discount_amount = $discount_fix_amount;
                }

            }

            $price = $price - $discount_amount;

            if($price < 0){
                $price = 0;
            }
        }

        return $price;
    }

    return 0;
}

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

function is_in_cart($id){
    $cart = Cart::where(['product_id' => $id, 'customer_id' => @Auth::user()->customer_id])->get();
    if(count($cart)){
        return 1;
    }
    return 0;
}

function add_sap_log($data, $id = false){

    if($id){
        $log = ActivityLog::find($id);
    }else{
        $log = new ActivityLog();
    }
    $log->fill($data)->save();

    return @$log->id;
}

function userid(){
    return @Auth::user()->id;
}

function userip(){
    return \Request::ip();
}

function get_random_password($length = 8){

    $password = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ') , 0 , 1);

    $password .= substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=~!@#$%^&*()_+,./<>?;:[]{}\|') , 0 , $length - 1 );

    return $password;
}

function getOrderStatus($id){
    $data = Quotation::with(['order', 'invoice'])->where('id', $id)->firstOrFail();
    $status = '';

    if(!empty($data)){
        if(!empty($data->order) && $data->order->cancelled != 'Yes'){
            $status = 'Cancelled';
        } else {
            if(!empty($data->order) && $data->order->document_status == 'bost_open'){
                $status = 'On Process';
                if(!empty($data->invoice)){
                    if($data->invoice == 'For Delivery')
                        $status = 'For Delivery';

                    if($data->invoice == 'Delivered')
                        $status = 'Delivered';

                    if($data->invoice == 'Delivered')
                        $status = 'Delivered';
                }
            } else {
                $status = 'Pending';
            }
        }
    } else {
        $status = 'Pending';
    }

    return $status;
}
