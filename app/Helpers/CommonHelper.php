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
use App\Models\Notification;
use App\Models\LocalOrderItem;
use App\Models\SapApiUrl;
use App\Models\ConversationMessage;
use App\Models\CustomerPromotion;
use App\Models\Customer;
use App\Models\Product;
use App\Models\RecommendedProductItem;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\CustomersSalesSpecialist;

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

        $access['module_id'][] = $value->module_id;
        $access['role_id'] = $value->role_id;
        $access['parent_id'][] = @$value->module->parent_id;
    }
    
    // dd($access);

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

function get_product_customer_price($item_prices, $number, $discount = false, $discount_fix_amount = false, $customer = false)
{
    if(is_null($number)){
        $number = 1;
    }
    $item_prices = json_decode($item_prices,true);
    if($item_prices && count($item_prices) > 0){

        $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
        $price = @$prices[$number]['Price'] ?? 0;

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

        $vat_rate = 0;
        if($price !== 0){ //for vatrate
            if($customer !== false){
                $vat_rate = get_vat_rate($customer);

                if($vat_rate !== 0){
                    $price = $price / $vat_rate;
                    $price = round($price, 2);
                }
            }
        }


        return $price;
    }

    return 0;
}


function get_vat_rate($customer){
    $customer_vat = 0;
    if($customer->vat_group !== null){
        $lines = json_decode($customer->vatgroup->vatgroups_lines);
        $rounded = $lines[0]->Rate * 1;

        $customer_vat = ($rounded === 0 || $rounded === 0.0) ? 0 : '1.'.$rounded; 
    }

    return $customer_vat;
}



function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

function is_in_cart($product_id, $customer_id = null){
    if($customer_id == null){
        $cart = Cart::where(['product_id' => $product_id, 'customer_id' => @Auth::user()->customer_id])->get();
    } else {
        $cart = Cart::where(['product_id' => $product_id, 'customer_id' => $customer_id])->get();
    }
    if(count($cart)){
        return 1;
    }
    return 0;
}

function is_in_cart_qty($product_id, $customer_id = null){
    if($customer_id == null){
        $cart = Cart::where(['product_id' => $product_id, 'customer_id' => @Auth::user()->customer_id])->orderBy('id','DESC')->first();
    } else {
        $cart = Cart::where(['product_id' => $product_id, 'customer_id' => $customer_id])->orderBy('id','DESC')->first();
    }

    return @$cart->qty;
}

function is_in_cart1($product_id, $customer_id = null){
    if($customer_id == null){
        $sap_customer_arr = get_sap_customer_arr(@Auth::user());
        $product = Product::findOrFail($product_id);
        $customer_id = @$sap_customer_arr[$product->sap_connection_id];

        $cart = Cart::where(['product_id' => $product_id, 'customer_id' => $customer_id])->get();
    } else {
        $cart = Cart::where(['product_id' => $product_id, 'customer_id' => $customer_id])->get();
    }
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

    $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@$!%*#?&_-~<>;') , 0 , 1);

    $password .= substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@$!%*#?&_-~<>;') , 0 , $length - 1 );

    return $password;
}

function getOrderStatus($data){
    // $data = Quotation::with(['order', 'invoice'])->where('id', $id)->firstOrFail();
    $status = '';

    if(!empty($data)){
        if(!empty($data->order) && $data->order->cancelled == 'Yes'){
            $status = 'Cancelled';
        } else {
            if(!empty($data->order) && $data->order->document_status == 'bost_open'){
                $status = 'On Process';
                if(!empty($data->invoice)){
                    if($data->invoice->u_sostat == 'For Delivery')
                        $status = 'For Delivery';

                    if($data->invoice->u_sostat == 'Delivered')
                        $status = 'Delivered';

                    if($data->invoice->u_sostat == 'Confirmed')
                        $status = 'Completed';

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


function getOrderStatusV2($line_stat){
    $line_status = '';
    if( count($line_stat) === 1 && in_array('Unserved', $line_stat) )
    {
        $line_status = 'Pending';
    }

    if( (count($line_stat) === 1 && in_array('Partial Served', $line_stat)) || count($line_stat) > 1 )
    {
        $line_status = 'Partially Served';
    }

    if( count($line_stat) === 1 && in_array('Fully Served', $line_stat) )
    {
        $line_status = 'Completed';
    }

    return $line_status;
}



function get_promotion_type_criteria($scope){
    $value = "";
    if($scope == "P"){
      $value = "Discount in Percentage";
    }
    elseif($scope == "R"){
      $value = "Discount Percentage Range";
    }
    elseif($scope == "U"){
      $value = "Percentage discount + Up to amount limit";
    }

    return $value;
}

function date_difference($tCreatedDate)
{
    //date_default_timezone_set("Asia/Kolkata");
    $date = date('Y-m-d H:i:s');

    $delta = strtotime($date) - strtotime($tCreatedDate);

    if (!defined("SECOND")) define("SECOND", 1);
    if (!defined("MINUTE")) define("MINUTE", 60 * SECOND);
    if (!defined("HOUR")) define("HOUR", 60 * MINUTE);
    if (!defined("DAY")) define("DAY", 24 * HOUR);
    if (!defined("MONTH")) define("MONTH", 30 * DAY);

    if ($delta < 1 * MINUTE)
    {
        return $delta == 1 ? "one second ago" : $delta . " seconds ago";
    }
    if ($delta < 2 * MINUTE)
    {
        return "a minute ago";
    }
    if ($delta < 45 * MINUTE)
    {
        return floor($delta / MINUTE) . " minutes ago";
    }
    if ($delta < 90 * MINUTE)
    {
        return "an hour ago";
    }
    if ($delta < 24 * HOUR)
    {
        return floor($delta / HOUR) . " hours ago";
    }
    if ($delta < 48 * HOUR)
    {
        return "yesterday";
    }
    if ($delta < 30 * DAY)
    {
        return floor($delta / DAY) . " days ago";
    }
    if ($delta < 12 * MONTH)
    {
        $months = floor($delta / DAY / 30);
        return $months <= 1 ? "one month ago" : $months . " months ago";
    }
    else
    {
        $years = floor($delta / DAY / 365);
        return $years <= 1 ? "one year ago" : $years . " years ago";
    }
    return date('F d',strtotime($tCreatedDate));
}

function getMyNotifications(){
    $data = Notification::whereHas('connections', function($q){
            $q->where('user_id', '=', @Auth::user()->id)
              ->where('is_seen', '=', 0);
        })->orderBy('id', 'desc')->take(5)->get();

    return $data;
}

function getNotificationType($type){
    if(!empty($type)){
        if($type == 'A' || $type == 'a'){
            return 'Announcement';
        } else if($type == 'N' || $type == 'n'){
            return 'News';
        } else if($type == 'OU' || $type == 'ou'){
            return 'Order';
        }else if($type == 'LC' || $type == 'lc'){
            return 'Live Chat';
        }else if($type == 'HD' || $type == 'hd'){
            return 'Help Desk';
        }else if($type == 'CP' || $type == 'cp'){
            return 'Claimed Promotion';
        }else if($type == 'WTY' || $type == 'wty'){
            return 'Warranty';
        } else {
            return '-';
        }
    } else {
        return '-';
    }
}

function array_value_recursive($key, array $arr){
    $val = array();
    array_walk_recursive($arr, function ($v, $k) use ($key, &$val) {
        if ($k == $key) array_push($val, $v);
    });
    return count($val) > 1 ? $val : array_pop($val);
}

function getRecommendedProducts($customer_id = false){
    // $customer = collect();
    // if(userrole() == 4){
    //     $customer = @Auth::user()->customer;
    // }elseif (!is_null(@Auth::user()->created_by)) {
    //     $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
    //     if(!is_null($customer)){
    //         $customer = @$customer->customer;
    //     }
    // }elseif($customer_id){
    //     $customer = Customer::findOrFail($customer_id);
    // }

    $data = collect([]);

    if($customer_id){
        // $data = LocalOrderItem::with('product')->whereHas('order', function($q) use ($customer_id){
        //         $q->where('customer_id' ,'=', $customer_id);
        //     })->orderBy('id', 'desc')->get()->take(10);
        
        $customer_id = [$customer_id];
        if(userrole() == 4){
            $customer_id = explode(',', Auth::user()->multi_customer_id);
        }elseif (!is_null(@Auth::user()->created_by)) {
            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                $customer_id = explode(',', @$customer->multi_customer_id);
            }
        }elseif(userrole() == 14){
            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
        }

        $data = RecommendedProductItem::whereHas('product', function($q){
                    $q->where('is_active', true);
                })->whereHas('recommended.assignments.customer', function($q) use ($customer_id){
                    $q->whereIn('customer_id', $customer_id);
                })->orderBy('id', 'desc')->get()->take(10);
    }

    return $data;

}

function encryptValue($value){
    if($value){
        return \Crypt::encryptString($value);
    }
    return "";
}

function decryptValue($value){
    if($value){
        try {
            return \Crypt::decryptString($value);
        } catch (\Exception $e) {
            abort(404);
        }
    }
    return "";
}

function getCartCount(){
    $customer_id = explode(',', @Auth::user()->multi_customer_id);
    if($customer_id){
        $cart = Cart::whereIn('customer_id', $customer_id)->get();
        return count($cart);
    }
    return 0;
}

function getOrderStatusByDocEntry($id){
    $data = Quotation::with(['order', 'invoice'])->where('doc_entry', $id)->firstOrFail();
    $status = '';

    if(!empty($data)){
        if(!empty($data->order) && $data->order->cancelled == 'No'){
            $status = 'Cancelled';
        } else {
            if(!empty($data->order) && $data->order->document_status == 'bost_open'){
                $status = 'On Process';
                if(!empty($data->invoice)){
                    if($data->invoice->u_sostat == 'For Delivery')
                        $status = 'For Delivery';

                    if($data->invoice->u_sostat == 'Delivered')
                        $status = 'Delivered';

                    if($data->invoice->u_sostat == 'Confirmed')
                        $status = 'Completed';

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

function common_error_msg($key = ""){
    $array = array(
        'excel_download' => "There is no data found so can't download the document.",
    );

    return @$array[$key] ?? "Error !";
}

function get_sap_api_url(){
    $obj = SapApiUrl::where('active', true)->first();

    return @$obj->url ?? env('SAP_API_URL') ?? "";
}

function convert_hex_to_rgba($hex = "", $opacity = 1){
    if($hex){
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        return sprintf('rgba(%s, %s, %s, %s)', $r, $g, $b, $opacity);
    }
}

function get_sort_char($string){
    $str_array = explode(" ", $string);
    return strtoupper(substr(@$str_array[0], 0,1).substr(@$str_array[1], 0,1));
}

function get_hex_color(){
    return '#'.substr(md5(rand()), 0, 6); //Generate Color Code
}

function get_login_user_un_read_message_count(){
    $un_read_message = ConversationMessage::where('user_id','!=', userid())->where('is_read',false);

    $un_read_message->where(function($query){
        $query->orwhere(function($q1){
            $q1->whereHas('conversation',function($qs1){
                $qs1->where('receiver_id', userid())->where('receiver_delete',false);
            });
        });

        $query->orwhere(function($q2){
            $q2->whereHas('conversation',function($qs2){
                $qs2->where('sender_id', userid())->where('sender_delete',false);
            });
        });
    });


    $un_read_message = $un_read_message->count();

    return $un_read_message;
}

function getOrderStatusArray($key = ""){
    $array = array(
                'PN' => 'Pending', //For List only
                'OP' => 'On Process',
                'FD' => 'For Delivery',
                'DL' => 'Delivered',
                'CF' => 'Confirmed',
                'CM' => 'Completed',
                'IN' => 'Invoiced',
                'CL' => 'Cancelled', //For List only
                'PS' => 'Partially Served'
            );

    if($key != ""){
        return @$array[$key] ?? $array['PN'];
    }

    return $array;
}

function getOrderStatusArray1($key = ""){
    $array = array(
                'PN' => 'Pending', //For List only
                'OP' => 'On Process',
                // 'FD' => 'For Delivery',
                // 'DL' => 'Delivered',
                //'CF' => 'Confirmed',
                'CM' => 'Completed',
                //'IN' => 'Invoiced',
                'CL' => 'Cancelled', //For List only
                'PS' => 'Partially Served'
            );

    if($key != ""){
        return @$array[$key] ?? $array['PN'];
    }

    return $array;
}

// Start Status
function getOrderStatusByInvoice($data){
    $status = getOrderStatusArray("CL");

    if(!empty($data)){

        if($data->cancelled == 'Yes'){
            $status = getOrderStatusArray('CL');
        }else{
            if(!empty($data->order) && $data->order->cancelled == 'Yes'){
                $status = getOrderStatusArray('CL');
            } else {
                if($data->document_status == 'bost_Open' && !empty($data->u_sostat)){

                    $status = getOrderStatusArray($data->u_sostat);

                } else {
                    $status = getOrderStatusArray("PN");
                }
            }
        }

    } else {
        $status = getOrderStatusArray("PN");
    }

    return $status;
}

// Start Status
function getOrderStatusByQuotation($data, $with_date = false){

    $status = getOrderStatusArray("PN");

    if(!empty($data)){
        if($data->cancelled == 'Yes'){
            $status = getOrderStatusArray('CL');
        }elseif(!empty(@$data->order)){

            if($data->order->cancelled == 'Yes'){
                $status = getOrderStatusArray('CL');
            }else{
                if($data->order->u_omsno != ""){
                    $status = getOrderStatusArray("OP");
                }
                if(!empty(@$data->order->invoice)){

                    if($data->order->invoice->cancelled == 'Yes'){
                        $status = getOrderStatusArray('CL');

                    }else if(@$data->order->invoice->u_omsno != null){
                        
                        if(@$data->order->invoice->u_sostat == 'DL' || @$data->order->invoice->u_sostat == 'CM'){
                            $status = getOrderStatusArray(@$data->order->invoice->u_sostat);
                        }else{
                          $status = getOrderStatusArray('FD');  
                        }
                    }else{
                        $status = getOrderStatusArray("PN");
                    }
                }
            }
        }else{
            $status = getOrderStatusArray("PN");
        }
    }

    if($with_date){
        $date_array = array(
                            'Pending' => @$data->created_at ?? null,
                            //'On Process' => @$data->order->created_at ?? null,
                            'On Process' => @$data->order->doc_date ?? null,
                            //'For Delivery' => @$data->order->invoice->created_at ?? null,
                            'For Delivery' => @$data->order->invoice->u_commitment ?? null,
                            'Delivered' => @$data->order->invoice->u_delivery ?? null,
                            //'Completed' => @$data->order->invoice->completed_date ?? null,
                            'Completed' => (@$data->order->invoice->u_sostat == 'CM')?@$data->order->invoice->update_date : null,
                            'Cancelled' => @$data->order->invoice->cancel_date ?? @$data->order->cancel_date ?? @$data->cancel_date,
                        );

        return [ 'status' => $status, 'date_array' => $date_array];
    }

    return $status;
    
}

function getStatusData($data, $with_date = false){
    $status = getOrderStatusArray('FD');
    if(@$data->u_omsno != null){
        if(@$data->u_sostat == 'DL' || @$data->u_sostat == 'CM'){
            $status = getOrderStatusArray(@$data->u_sostat);
        }else{
          $status = getOrderStatusArray('FD');  
        }
    }

    if($with_date){
        $date_array = array(
                            'For Delivery' => @$data->u_commitment ?? null,
                            'Delivered' => @$data->u_delivery ?? null,
                            'Completed' => (@$data->u_sostat == 'CM')?@$data->update_date : null,
                            'Cancelled' => @$data->cancel_date,
                        );
        return [ 'status' => $status, 'date_array' => $date_array];
    }
}


function getOrderStatusProcessArray($status){
    $array = array();
    switch ($status) {
        case "Pending":
            $array = array(
                            'Pending'
                        );
            break;
        case "On Process":
            $array = array(
                            'Pending',
                            'On Process',
                        );
            break;
        case "For Delivery":
            $array = array(
                            'Pending',
                            'On Process',
                            'For Delivery',
                        );
            break;
        case "Delivered":
            $array = array(
                            'Pending',
                            'On Process',
                            'For Delivery',
                            'Delivered',
                        );
            break;
        case "Confirmed":
            $array = array(
                            'Pending',
                            'On Process',
                        );
            break;
        case "Invoiced":
            $array = array(
                            'Pending',
                            'On Process',
                            'For Delivery',
                            'Confirmed',
                            // 'Delivered',
                            // 'Completed',
                            'Invoiced',
                        );
            break;
        case "Completed":
            $array = array(
                            'Pending',
                            'On Process',
                            'For Delivery',
                            'Delivered',
                            'Confirmed',
                            'Completed',
                        );
            break;
        case "Cancelled":
            break;
        default:
            break;
    }

    return $array;
}

function number_format_value($value){
    return number_format($value,2);
}


function getOrderStatusBtnHtml($status){

    $btn = "";
    switch ($status) {
        case "Pending":
            $btn = '<b style="color:'.convert_hex_to_rgba('#78909c').';background-color:'.convert_hex_to_rgba('#78909c',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "On Process":
            $btn = '<b style="color:'.convert_hex_to_rgba('#ffa726').';background-color:'.convert_hex_to_rgba('#ffa726',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "For Delivery":
            $btn = '<b style="color:'.convert_hex_to_rgba('#29b6f6').';background-color:'.convert_hex_to_rgba('#29b6f6',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "Delivered":
            $btn = '<b style="color:'.convert_hex_to_rgba('#66bb6a').';background-color:'.convert_hex_to_rgba('#66bb6a',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "Confirmed":
            $btn = '<b style="color:'.convert_hex_to_rgba('#2e7d32').';background-color:'.convert_hex_to_rgba('#2e7d32',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "Completed":
            $btn = '<b style="color:'.convert_hex_to_rgba('#2e7d32').';background-color:'.convert_hex_to_rgba('#2e7d32',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "Invoiced":
            $btn = '<b style="color:'.convert_hex_to_rgba('#2e7d32').';background-color:'.convert_hex_to_rgba('#2e7d32',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "Cancelled":
            $btn = '<b style="color:'.convert_hex_to_rgba('#f44336').';background-color:'.convert_hex_to_rgba('#f44336',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "Error":
            $btn = '<b style="color:'.convert_hex_to_rgba('#f44336').';background-color:'.convert_hex_to_rgba('#f44336',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "Open":
            $btn = '<b style="color:'.convert_hex_to_rgba('#66bb6a').';background-color:'.convert_hex_to_rgba('#66bb6a',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "Close":
            $btn = '<b style="color:'.convert_hex_to_rgba('#ffa726').';background-color:'.convert_hex_to_rgba('#ffa726',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "Partially Served":
            $btn = '<b style="color:'.convert_hex_to_rgba('#29b6f6').';background-color:'.convert_hex_to_rgba('#29b6f6',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        case "Fully Served":
            $btn = '<b style="color:'.convert_hex_to_rgba('#2e7d32').';background-color:'.convert_hex_to_rgba('#2e7d32',0.1).';" class="btn btn-sm">'.$status.'</b>';
            break;
        default:
            break;
    }

    return $btn;
}

function current_datetime(){
    return date("Y-m-d H:i:s");
}

function get_un_read_customer_promotion_count(){
    $count = CustomerPromotion::where('is_admin_read', false)->count();

    return $count;
}

function get_customer_price_list_no_arr(array $customer_ids){

    $customer_price_list_no_arr = [];

    $customers = Customer::whereIn('id', $customer_ids)->get();

    if(count($customers)){
        $customer_price_list_no_keys = $customers->pluck('real_sap_connection_id')->toArray();
        $customer_price_list_no_values = $customers->pluck('price_list_num')->toArray();

        $customer_price_list_no_arr = array_combine($customer_price_list_no_keys, $customer_price_list_no_values);
    }

    return $customer_price_list_no_arr;
}

function get_sap_customer_arr($user){
    $customer_id = explode(',', $user->multi_customer_id);
    $sap_connection_id = Customer::whereIn('id',$customer_id)->pluck('real_sap_connection_id')->toArray();
    //$sap_connection_id = explode(',', $user->multi_real_sap_connection_id);
    //print_r($sap_connection_id);exit();
    return array_combine($sap_connection_id, $customer_id);
}

function get_timezone_date_time($date){
    $dt = new DateTime($date);
    $tz = new DateTimeZone('Asia/Manila'); 
    $dt->setTimezone($tz);
    $local_time = $dt->format('Y-m-d H:i:s');
    return date("M d, Y h:i A", strtotime($local_time));
    
}


function get_product_customer_currency($item_prices, $cust_price_list){
    $item_prices = json_decode($item_prices);

    $currency = '';
    foreach($item_prices as $price){
        if($cust_price_list == $price->PriceList){
            $currency = $price->Currency;
        }
    }
    // $cust_price_list = $cust_price_list - 1;
    // $currency = $item_currency[$cust_price_list]->Currency;

    $currency_symbol = '';
    switch($currency){
        case 'USD':
            $currency_symbol = '$';
            break;
        case 'EUR':
            $currency_symbol = '€';
            break;
        default:
            $currency_symbol = '₱';
    }

    return $currency_symbol;

}

function get_product_currency($currency){
    $currency_symbol = '';
    switch($currency){
        case 'USD':
            $currency_symbol = '$';
            break;
        case 'EUR':
            $currency_symbol = '€';
            break;
        default:
            $currency_symbol = '₱';
    }

    return $currency_symbol;

}
