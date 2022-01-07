<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Promotions;
use App\Models\PromotionTypes;
use App\Models\PromotionTypeProduct;
use App\Models\PromotionInterest;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerPromotion;
use App\Models\CustomerPromotionProduct;
use App\Models\CustomerPromotionProductDelivery;
use App\Models\CustomerBpAddress;

use App\Support\SAPCustomerPromotion;
use App\Models\SapConnection;

use Validator;
use DataTables;
use Auth;

class CustomerPromotionController extends Controller
{
    public function index()
    {
      	return view('customer-promotion.index');
    }


    public function getAll(Request $request){
  		if ($request->ajax()) {
            
            $where = array('is_active' => true);

            $now = date("Y-m-d");
            // $now = "2021-12-24";

            $promotions = Promotions::where($where)
            						->orderBy('id', 'DESC')
            						->where('promotion_start_date','<=',$now)
            						->where('promotion_end_date','>=',$now)
            						->limit(12);

            if ($request->id > 0) {
                $promotions->where('id', '<', $request->id);
            }
            
            $promotions = $promotions->get();

            $output = "";
            $button = "";
            $last_id = "";

            $last = Promotions::where($where)
                                ->where('promotion_start_date','<=',$now)
                                ->where('promotion_end_date','>=',$now)
                                ->select('id')
                                ->first();

            if (!$promotions->isEmpty()) {

                foreach ($promotions as $promotion) {

                	$is_continue = false;

                	if($promotion->promotion_for == "Limited"){

            			if(!is_null($promotion->promotion_data)){
	                		
	                		if($promotion->promotion_scope == "C"){ //Customer 

	                			$check = $promotion->promotion_data->firstWhere('customer_id',@Auth::user()->customer_id);

	                			if(is_null($check)){
	                				$is_continue = true;
	                			}

	                		}elseif($promotion->promotion_scope == "CL"){ //Class 

	                			$check = $promotion->promotion_data->firstWhere('class_id',@Auth::user()->customer->class_id);

	                			if(is_null($check)){
	                				$is_continue = true;
	                			}

	                		}elseif($promotion->promotion_scope == "SS"){ //Sales Specialists 

                                $check = $promotion->promotion_data->firstWhere('sales_specialist',@Auth::id());

                                if(is_null($check)){
                                    $is_continue = true;
                                }

                            }elseif($promotion->promotion_scope == "T"){ //Territory 

                                $check = $promotion->promotion_data->firstWhere('territory_id',@Auth::user()->customer->territories->id);

                                if(is_null($check)){
                                    $is_continue = true;
                                }
                            }

            				
            			}else{
            				$is_continue = true;
            			}
                	}

                    // Check into promotion interests
                    $interest = @$promotion->promotion_interests->firstWhere('user_id' , Auth::id());

                    if(isset($interest->is_interested) && $interest->is_interested == 0){
                        $is_continue = true;
                    }

                	if($is_continue){
                		continue;
                	}

                    $output .= view('customer-promotion.ajax.promotion',compact('promotion'))->render();
                }

                $last_id = $promotions->last()->id;

                if ($last_id != $last->id) {
                    $button = '<a href="javascript:" class="btn btn-primary" data-id="' . $last_id . '" id="view_more_btn">View More Promotions</a>';
                }

            } else {

                $button = '';

            }

            return response()->json(['output' => $output, 'button' => $button]);
        }
  	}

    public function show($id)
    {
        $data = Promotions::where('is_active',true)->where('id',$id)->firstOrFail();

        $now = date("Y-m-d");
        // $now = "2021-12-12";
        if( !($now >= $data->promotion_start_date && $now <= $data->promotion_end_date) ){
            return abort(404);
        }

        // Add Log.
        add_log(26, array('id' => $data->id));
        
        return view('customer-promotion.view',compact('data'));
    }

    public function getAllProductList(Request $request){
        if ($request->ajax()) {
            
            $where = array('promotion_type_id' => $request->promotion_type_id);

            $products = PromotionTypeProduct::where($where)->orderBy('id', 'DESC')->limit(12);
            
            if ($request->id > 0) {
                $products->where('id', '<', $request->id);
            }
            
            $products = $products->get();

            $output = "";
            $button = "";
            $last_id = "";

            $promotion_id = $request->promotion_id;

            $last = PromotionTypeProduct::where($where)->select('id')->first();

            if (!$products->isEmpty()) {

                foreach ($products as $value) {
                    $product = $value->product;
                    $promotion_type_product = $value;

                    if(isset($request->customer_id)){
                        $customer = Customer::find($request->customer_id);
                    }else{
                        $customer = @Auth::user()->customer;
                    }

                    if(!is_null($product)){
                        $output .= view('customer-promotion.ajax.product',compact('product','promotion_type_product','promotion_id','customer'))->render();
                    }
                }

                $last_id = $products->last()->id;

                if ($last_id != $last->id) {
                    $button = '<a href="javascript:" class="btn btn-primary" data-id="' . $last_id . '" id="view_more_btn">View More Products</a>';
                }

            } else {

                $button = '';

            }

            return response()->json(['output' => $output, 'button' => $button]);
        }
    }

    public function productDetail($id, $promotion_id, $customer_id = false){
        $data = PromotionTypeProduct::where('id',$id)->firstOrFail();
        
        $product = $data->product;

        $promotion = Promotions::findOrFail($promotion_id);

        $now = date("Y-m-d");
        // $now = "2021-12-12";
        if( !($now >= $promotion->promotion_start_date && $now <= $promotion->promotion_end_date) ){
            return abort(404);
        }

        if($customer_id){
            $customer = Customer::find($customer_id);
        }else{
            $customer = @Auth::user()->customer;
        }

        return view('customer-promotion.product-view',compact('product','data','promotion','customer'));
    }

    public function orderCreate($id, $customer_id = false){

        $promotion = Promotions::findOrFail($id);

        $now = date("Y-m-d");
        // $now = "2021-12-12";
        if( !($now >= $promotion->promotion_start_date && $now <= $promotion->promotion_end_date) ){
            return abort(404);
        }

        if($customer_id){
            $customer_user = User::where('customer_id', $customer_id)->firstOrFail();
        }else{

            if(is_null(@Auth::user()->customer_id)){ // If customer refernce id not get
                return abort(404);
            }
            $customer_user = @Auth::user();
        }

        return view('customer-promotion.order_add',compact('promotion','customer_user'));
    }

    public function orderStore(Request $request){

        $input = $request->all();

        $rules = [];
        if(userrole() != 4){ // If its not a customer
            $rules['customer_id'] = 'required|exists:customers,id,sap_connection_id,'.@Auth::user()->sap_connection_id;

            $messages = array(
                        'customer_id.exists' => "Oops! Customer not found.",
                    );

            $validator = Validator::make($input, $rules, $messages);

            if ($validator->fails()) {
                return $response = ['status'=>false,'message'=>$validator->errors()->first()];
            }
            
            $customer_user = User::where('customer_id', $input['customer_id'])->firstOrFail();
        }else{
            $customer_user = @Auth::user();
        }

        if(!$customer_user->customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        }

        $rules = array(
                    'promotion_id' => 'required|exists:promotions,id',
                    'customer_bp_address_id' => 'required|exists:customer_bp_addresses,id',
                    'products' => 'required|array',
                    'products.*.product_id' => 'distinct|exists:products,id,sap_connection_id,'.@$customer_user->customer->sap_connection_id,
                );

        $messages = array(
                        'products.*.product_id.exists' => "Oops! Customer or Items can not be located in the DataBase.",
                        'customer_id.exists' => "Oops! Customer not found.",
                    );

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            // Edit time check its canceled or not 
            if(isset($input['id'])){
                $check = CustomerPromotion::where('id',$input['id'])->where('status','canceled')->first();

                if(!is_null($check)){
                    return $response = ['status'=>false,'message'=> "Opps ! This promotion claim has been canceled already."];
                }

            }

            $promotion = Promotions::findOrFail($input['promotion_id']);

            $now = date("Y-m-d");
            if( !($now >= $promotion->promotion_start_date && $now <= $promotion->promotion_end_date) ){
                $response = ['status'=>false,'message'=> "Opps ! This promotion has been expired."];
            }else{

                // If Promotion Qty fixed and its not match to with buying qty
                if(@$promotion->promotion_type->is_total_fixed_quantity){
                    $total_fixed_quantity = @$promotion->promotion_type->total_fixed_quantity;
                    $total_quantity = array_sum(array_column(@$input['products'], 'quantity'));

                    if($total_quantity != $total_fixed_quantity){
                        return $response = ['status'=>false,'message'=> "Oops! the total quantity is not the same as the total fix quantity."];
                    }
                }

                $total_quantity = $total_price = $total_discount = $total_amount = 0;

                $customer_promotion = CustomerPromotion::firstOrNew(['id'=>@$input['id']]);

                if(@$customer_promotion->id != null){
                    $customer_promotion->last_data = $customer_promotion->toArray();
                }

                $customer_promotion->promotion_id = $input['promotion_id'];
                $customer_promotion->customer_bp_address_id = $input['customer_bp_address_id'];
                $customer_promotion->status = 'pending';
                $customer_promotion->is_sap_pushed = false;
                $customer_promotion->is_approved = true;
                $customer_promotion->sap_connection_id = @$customer_user->customer->sap_connection_id;
                
                //$customer_promotion->user_id = Auth::id();

                if(in_array(userrole(),[2])){ // its a ss
                    $customer_promotion->sales_specialist_id = Auth::id();
                    $customer_promotion->user_id = $customer_user->id;
                    $customer_promotion->is_approved = false;
                }else{
                    
                    if(!isset($input['id'])){
                        $customer_promotion->sales_specialist_id = null;
                    }
                    $customer_promotion->user_id = Auth::id();
                }


                if(isset($input['id'])){
                    $customer_promotion->updated_by = Auth::id();
                }

                $customer_promotion->save();

                if(@$customer_promotion->id){

                    if(isset($input['products']) && !empty($input['products'])){

                        foreach ($input['products'] as $key => $value) {

                            $quantity = 0;

                            $product = Product::find($key);

                            if(!is_null($product)){

                                $where = array(
                                                'promotion_type_id' => $promotion->promotion_type_id,
                                                'product_id' => $product->id,
                                            );

                                $p_product = PromotionTypeProduct::where($where)->first();

                                $customer_promotion_product = CustomerPromotionProduct::firstOrNew(['id'=>@$value['id']]); 
                                if(@$customer_promotion_product->id != null){
                                    $customer_promotion_product->last_data = $customer_promotion_product->toArray();
                                }

                                $customer_promotion_product->customer_promotion_id = @$customer_promotion->id;
                                $customer_promotion_product->product_id = $key;
                                $customer_promotion_product->save();
                                
                                if(@$customer_promotion_product->id){

                                    foreach ($value['delivery_date'] as $d_key => $d_value) {

                                        if($d_value && $value['delivery_quantity'][$d_key]){

                                            $quantity += $value['delivery_quantity'][$d_key];

                                            $c_p_p_d = CustomerPromotionProductDelivery::firstOrNew(['id'=>@$value['delivery_id'][$d_key]]);  
                                            
                                            if(@$c_p_p_d->id != null){
                                                $c_p_p_d->last_data = $c_p_p_d->toArray();
                                            }

                                            $c_p_p_d->customer_promotion_product_id = @$customer_promotion_product->id;
                                            $c_p_p_d->delivery_quantity = $value['delivery_quantity'][$d_key];
                                            $c_p_p_d->delivery_date = date("Y-m-d",strtotime(str_replace("/", "-", $d_value)));
                                            $c_p_p_d->save();

                                        }
                                    }


                                    $discount_percentage = 0;
                                    $discount_fix_amount = false;

                                    if(@$promotion->promotion_type->scope == "P"){
                                        $discount_percentage = @$promotion->promotion_type->percentage;
                                    }else if(@$promotion->promotion_type->scope == "U"){
                                        $discount_percentage = @$promotion->promotion_type->percentage;
                                        $discount_fix_amount = @$promotion->promotion_type->fixed_price;
                                    }elseif(@$promotion->promotion_type->scope == "R"){
                                        $discount_percentage = @$p_product->discount_percentage;
                                    }


                                    $price = get_product_customer_price(@$product->item_prices,@$customer_user->customer->price_list_num);
                                    $amount = $discount = get_product_customer_price(@$product->item_prices,@$customer_user->customer->price_list_num,$discount_percentage,@$discount_fix_amount);

                                    $discount = $price - $discount;

                                    if($amount > 0){
                                        $amount = floatval($amount) * floatval($quantity);
                                    }

                                    $customer_promotion_product->quantity = $quantity;
                                    $customer_promotion_product->price = round($price,2);
                                    $customer_promotion_product->discount = round($discount,2);
                                    $customer_promotion_product->amount = round($amount,2);
                                    $customer_promotion_product->save();


                                    $total_quantity += $quantity;
                                    $total_price += ($price * $quantity);
                                    $total_discount += ($discount * $quantity);
                                    $total_amount += $amount;
                                }
                            }
                        }
                    }

                    $customer_promotion->total_quantity = $total_quantity;
                    $customer_promotion->total_price = round($total_price,2);
                    $customer_promotion->total_discount = round($total_discount,2);
                    $customer_promotion->total_amount = round($total_amount,2);
                    $customer_promotion->save();


                    if(isset($input['id'])){
                        $response = ['status'=>true,'message'=> "Promotion claim details updated successfully !"];
                        
                        // Add Log.
                        add_log(30, $input);
                    }else{
                        $response = ['status'=>true,'message'=> "Promotion claim successfully !"];
                        
                        // Add Log.
                        add_log(27, $input);
                    }

                }else{
                    $response = ['status'=>false,'message'=> "Something went wrong."];
                }

            }
        }

        return $response;
    }

    public function orderIndex(){
        return view('customer-promotion.order_index');
    }

    public function orderGetAll(Request $request){

        $data = CustomerPromotion::query();

        if(in_array(userrole(),[2])){ // its a ss
            $data->where('customer_promotions.sales_specialist_id',Auth::id());
        }else if(Auth::id() != 1){
            $data->where('customer_promotions.user_id',Auth::id());
        }

        if($request->filter_customer != ""){
            $data->where('customer_promotions.user_id',$request->filter_customer);
        }

        if($request->filter_status != ""){
            $data->where('customer_promotions.status',$request->filter_status);
        }

        if($request->filter_search != ""){
            $data->where(function($query) use ($request) {

                $query->whereHas('promotion',function($q) use ($request) {
                    $q->where('title','LIKE',"%".$request->filter_search."%");
                });

            });
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('created_at', '>=' , $start);
            $data->whereDate('created_at', '<=' , $end);
        }
        

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('customer_promotions.id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('promotion', function($row) {
                                return @$row->promotion->title ?? "-";
                            })
                            ->addColumn('user', function($row) {
                                return @$row->user->sales_specialist_name ?? "-";
                            })
                            ->addColumn('action', function($row) {

                                $btn = "";
                                
                                if($row->status != 'canceled' && (Auth::id() == $row->user_id || Auth::id() == $row->sales_specialist_id)){

                                    $url = route('customer-promotion.order.edit', $row->id);
                                    if(userrole() != 4){
                                        $url .= "/".$row->user->customer_id;
                                    }

                                    $btn .= '<a href="' . $url. '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                        <i class="fa fa-pencil"></i>
                                      </a>';
                                }

                                $btn .= '<a href="' . route('customer-promotion.order.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                return $btn;
                            })
                            ->addColumn('created_at', function($row) {
                                return date('M d, Y',strtotime($row->created_at));
                            })
                            ->addColumn('status', function($row) {

                                $btn = "";
                                if($row->status == "approved"){
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline ">Approved</a>';
                                }else if($row->status == "pending"){
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-info btn-inline ">Pending</a>';
                                }else if($row->status == "canceled"){
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-danger btn-inline ">Canceled</a>';
                                }

                                return $btn;
                            })
                            ->orderColumn('promotion', function ($query, $order) {
                                $query->select('customer_promotions.*')->join('promotions', 'customer_promotions.promotion_id', '=', 'promotions.id')
                                    ->orderBy('promotions.title', $order);
                            })
                            ->orderColumn('user', function ($query, $order) {
                                $query->select('customer_promotions.*')->join('users', 'customer_promotions.user_id', '=', 'users.id')
                                    ->orderBy('users.sales_specialist_name', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('status', $order);
                            })
                            ->orderColumn('created_at', function ($query, $order) {
                                $query->orderBy('created_at', $order);
                            })
                            ->rawColumns(['action','status','created_at','user'])
                            ->make(true);
    }

    public function orderShow($id){

        $data = CustomerPromotion::where('id',$id);

        if(in_array(userrole(),[2])){ // its a ss
            $data->where('sales_specialist_id', Auth::id());
        }else if(Auth::id() != 1){
            $data->where('user_id',Auth::id());
        }

        $data = $data->firstOrFail();
        
        return view('customer-promotion.order_view',compact('data'));
    }

    public function orderStatus(Request $request){
        $input = $request->all();

        $rules = array(
                    'id' => 'required|exists:customer_promotions,id',
                    'status' => 'required',
                    'cancel_reason'=> 'required_if:status,==,canceled',
                );

        if($input['status'] != "canceled"){
            $input['cancel_reason'] = null;
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            $obj = CustomerPromotion::find($input['id']);

            if($input['status'] == 'canceled'){
                $input = json_decode($obj->last_data,true);

                $input['last_data'] = NULL;
                $input['status'] = 'canceled';
                $input['cancel_reason'] = $request->cancel_reason;

                unset($input['doc_entry']);

                foreach ($obj->products as $product) {

                    $insert = json_decode($product->last_data,true);
                    $insert['last_data'] = null;

                    $product->fill($insert)->save();


                    foreach ($product->deliveries as $delivery) {
                        $insert = json_decode($delivery->last_data,true);
                        $insert['last_data'] = null;

                        $delivery->fill($insert)->save();
                    }
                }
            }elseif($input['status'] == 'approved'){


                try {

                    $sap_connection = SapConnection::find($obj->sap_connection_id);

                    if(!is_null($sap_connection)){
                        $sap_obj = new SAPCustomerPromotion($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password);
                
                        if($obj->doc_entry){ 

                            // Cancel Old Order
                            $cancel = $sap_obj->cancelOrder($input['id'], $obj->doc_entry);

                            if(isset($cancel['status']) && $cancel['status']){
                                // Create Order
                                $sap_obj->createOrder($input['id']);
                            }

                            // // Update Order
                            // $sap_obj->updateOrder($input['id'], $obj->doc_entry);

                        }else{ 
                            // Create Order
                            $sap_obj->createOrder($input['id']);
                        }
                    }

                } catch (\Exception $e) {
                    
                }
            }

            $obj->fill($input)->save();
            
            $message = "Status updated successfully.";

            $response = ['status'=>true,'message'=>$message];

            // Add Log.
            add_log(28, $input);
        }

        return $response;
    }

    public function orderEdit($id, $customer_id = false){
        $edit = CustomerPromotion::where('id',$id)->where('status','!=','canceled');

        if(in_array(userrole(),[2])){ // its a ss
            $edit->where('sales_specialist_id', Auth::id());
        }else if(Auth::id() != 1){
            $edit->where('user_id', Auth::id());
        }
        $edit = $edit->firstOrFail();

        $promotion = Promotions::findOrFail($edit->promotion_id);

        $edit_products = $edit_deliveries = array();

        if(isset($edit->products) && count($edit->products)){
            foreach($edit->products as $p){
                $edit_products[$p->product_id] = $p->toArray();
                $edit_deliveries[$p->product_id] = $p->deliveries->toArray();
            }
        }

        if($customer_id){
            $customer_user = User::where('customer_id', $customer_id)->firstOrFail();
        }else{

            if(is_null(@Auth::user()->customer_id)){ // If customer refernce id not get
                return abort(404);
            }
            $customer_user = @Auth::user();
        }

        return view('customer-promotion.order_add',compact('promotion','edit','edit_products','edit_deliveries','customer_user'));
    }

    public function orderPushInSap(Request $request){
        $input = $request->all();

        $rules = array(
                    'id' => 'required|exists:customer_promotions,id',
                );


        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            $obj = CustomerPromotion::where('id',$input['id'])->where('is_sap_pushed',false)->first();

            if(!is_null($obj)){
                /*try {
                    $sap_obj = new SAPCustomerPromotion('TEST-APBW', 'manager', 'test');
            
                    if($obj->doc_entry){ 
                        // Update Order
                        $sap_obj->updateOrder($input['id'], $obj->doc_entry);

                    }else{ 
                        // Create Order
                        $sap_obj->createOrder($input['id']);
                    }

                    $response = ['status'=>true,'message'=>"Order pushed in SAP successfully."];
                
                } catch (\Exception $e) {
                    $response = ['status'=>false,'message'=>$e->getMessage()];
                }*/


                try {

                    $sap_connection = SapConnection::find($obj->sap_connection_id);

                    if(!is_null($sap_connection)){
                        $sap_obj = new SAPCustomerPromotion($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password);
                
                        if($obj->doc_entry){ 

                            // Cancel Old Order
                            $cancel = $sap_obj->cancelOrder($input['id'], $obj->doc_entry);

                            if(isset($cancel['status']) && $cancel['status']){
                                // Create Order
                                $sap_obj->createOrder($input['id']);
                            }

                            // // Update Order
                            // $sap_obj->updateOrder($input['id'], $obj->doc_entry);

                        }else{ 
                            // Create Order
                            $sap_obj->createOrder($input['id']);
                        }
                    }

                    $response = ['status'=>true,'message'=>"Order pushed in SAP successfully."];

                } catch (\Exception $e) {
                    $response = ['status'=>false,'message'=>$e->getMessage()];
                }
            }else{
                $response = ['status'=>false,'message'=>"Record Not Found !"];
            }
        }

        return $response;
    }

    public function orderApproved(Request $request){
        $input = $request->all();

        $rules = array(
                    'id' => 'required|exists:customer_promotions,id,user_id,'.@Auth::id(),
                );

        $messages = array(
                        'id.exists' => 'Record not found!'
                    );

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            CustomerPromotion::where('id', $request->id)->update(['is_approved' => true]);
            
            $message = "Approved successfully.";
            $response = ['status'=>true,'message'=>$message];
        }

        return $response;
    }

    public function storeInterest(Request $request){
        $input = $request->all();

        $rules = array(
                    'promotion_id' => 'required|nullable|exists:promotions,id',
                    'is_interested' => 'nullable|required',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            $input['user_id'] = Auth::id();

            $obj = PromotionInterest::firstOrNew([
                                        'promotion_id' => $input['promotion_id'],
                                        'user_id' => $input['user_id'],
                                    ]);

            if($input['is_interested'] == true){
                $message = "Promotion interest added successfully.";
            }else{
                $message = "Promotion interest removed successfully.";
            }

            $obj->fill($input)->save();

            // Add Log.
            add_log(29, $input);

            $response = ['status'=>true,'message'=>$message];
        }

        return $response;
    }

    public function getInterest(Request $request){
        
        if($request->ajax()){
            $data = PromotionInterest::where('user_id', @Auth::id())->latest()->get();

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('promotion', function($row) {
                        return @$row->promotion->title ?? "-";
                    })
                    ->addColumn('is_interested', function($row) {
                        return $row->is_interested ? "Yes" : "No";
                    })
                    ->addColumn('action', function($row) {
                        $btn = "";
                        if($row->is_interested){
                            $btn .= '<a href="javascript:" class="btn btn-sm btn-danger btn-inline btn_interest" data-value="0" data-id="'.$row->promotion_id.'">Mark as Not Interested</a>';
                        }else{
                            $btn .= '<a href="javascript:" class="btn btn-sm btn-success btn-inline btn_interest" data-value="1" data-id="'.$row->promotion_id.'">Mark as Interested</a>';
                        }

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('customer-promotion.interest_index');
    }


    public function getCustomerAddress(Request $request){
        $search = $request->search;

        $customer_id = @Auth::user()->customer->id;
        if(isset($request->customer_id)){
            $customer_id = $request->customer_id;
        }

        $data = CustomerBpAddress::where('customer_id', $customer_id)->orderBy('order','asc');

        if($search != ''){
            $data->where('address', 'like', '%' .$search . '%');
        }

        $data = $data->limit(50)->get();

        return response()->json($data);
    }

    public function getCustomer(Request $request){
        $search = $request->search;

        $data = Customer::has('user')->with('user')->orderBy('card_name','asc');

        // Sales specialist can see only assigned customer
        if(in_array(userrole(),[2])){
            $data->whereHas('sales_specialist', function($q) {
                return $q->where('ss_id', Auth::id());
            });
        }

        if($search != ''){
            $data->where(function($q) use ($search){
                $q->orwhere('card_name', 'like', '%' .$search . '%');
                $q->orwhere('card_code', 'like', '%' .$search . '%');
            });
        }

        $data = $data->limit(50)->get();

        return response()->json($data);
    }
}
