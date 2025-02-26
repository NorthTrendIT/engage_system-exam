<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Support\SAPOrderPost;
use App\Models\Cart;
use App\Models\CustomerBpAddress;
use App\Models\Customer;
use App\Models\LocalOrder;
use App\Models\LocalOrderItem;
use App\Models\SapConnection;
use App\Models\Product;
use Validator;
use Auth;
use App\Models\Quotation;
use App\Models\CustomersSalesSpecialist;
use App\Models\CustomerProductGroup;
use App\Models\CustomerProductItemLine;
use App\Models\CustomerProductTiresCategory;
use DataTables;
use App\Support\SAPVatGroup;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customer_id = explode(',', Auth::user()->multi_customer_id);
        $customer = Customer::where('id',$customer_id[0])->first();

        $auth_sap_id = Auth::user()->sap_connection_id;
        $auth_sap_id = ($auth_sap_id == 5) ? 1 : $auth_sap_id;
        $u_card_code = Auth::user()->u_card_code;
        $customer_id_add = [];

        foreach($customer_id as $id){
            $cust_id = Customer::select('id', 'sap_connection_id','u_card_code')->where('id',$id)->first();
            if(($cust_id->sap_connection_id == $auth_sap_id) && ($u_card_code == $cust_id->u_card_code)){
                array_push($customer_id_add, $cust_id->id);
            }
        }

        $address = CustomerBpAddress::whereIn('customer_id', $customer_id_add)->orderBy('order', 'ASC')->get();

        $total = 0;
        $weight = 0;
        $volume = 0;
        $data = Cart::with(['product', 'customer'])->whereIn('customer_id', $customer_id)->get();

        foreach($data as $item){

            $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

            $subTotal = get_product_customer_price(@$item->product->item_prices, $customer_price_list_no, false, false, $cust_id) * $item->qty;
            $total += $subTotal;

            $weight = $weight + ($item->qty * @$item->product->sales_unit_weight);
            $volume = $volume + ($item->qty * @$item->product->sales_unit_volume);
        }

        $sales_agent = CustomersSalesSpecialist::with('sales_person')->where('customer_id',$customer_id)->get();

        $c_product_groups = $c_product_line = $c_product_category = collect();
        $customer_id = $sap_connection_id = [];

        if(userrole() == 4){
            $customer_id = explode(',', Auth::user()->multi_customer_id);
            $sap_connection_id = explode(',', Auth::user()->multi_real_sap_connection_id);
        }elseif(userrole() == 14){
            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
            $sap_connection_id = array( @Auth::user()->sap_connection_id );
        }elseif (!is_null(@Auth::user()->created_by)) {
            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                $customer_id = explode(',', @$customer->multi_customer_id);
                $sap_connection_id = explode(',', @$customer->multi_real_sap_connection_id);
            }
        }

        if(!empty($customer_id)){

            // Product Group
            $c_product_groups = CustomerProductGroup::with('product_group')->whereIn('customer_id', $customer_id)->get()->unique('product_group_id');
            $product_groups = array_map( function ( $ar ) {
                return $ar['number'];
            }, array_column( $c_product_groups->toArray(), 'product_group' ) );


            // Product Item Line
            $c_product_line = CustomerProductItemLine::with('product_item_line')->whereIn('customer_id', $customer_id)->get();
            $c_product_line = array_map( function ( $ar ) {
                return $ar['u_item_line'];
            }, array_column( $c_product_line->toArray(), 'product_item_line' ) );



            // Product Tires Category
            $c_product_category = CustomerProductTiresCategory::with('product_tires_category')->whereIn('customer_id', $customer_id)->get();
            $c_product_category = array_map( function ( $ar ) {
                return $ar['u_tires'];
            }, array_column( $c_product_category->toArray(), 'product_tires_category' ) );



            $brand_product = Product::where('is_active', true)->whereIn('items_group_code', $product_groups);
            $brand_product->whereHas('group', function($q){
                $q->where('is_active', true);
            });

            if(in_array(5, $sap_connection_id)){
                array_push($sap_connection_id, '5');
            }

            $brand_product->whereIn('sap_connection_id', $sap_connection_id);
            $c_product_line = $brand_product->groupBy('u_item_line')->get();
            $brand_product = $brand_product->get()->toArray();

            $c_product_category = array_unique(
                                        array_filter(
                                                array_merge($c_product_category,
                                                    array_column($brand_product, 'u_tires')
                                                )
                                            )
                                    );
            asort($c_product_category);

        }

        $cart_address = Cart::whereIn('customer_id', $customer_id)->orderBy('id','DESC')->first();

        $selected_address = CustomerBpAddress::where('id', @$cart_address->address)->first();

        $api_conn = SapConnection::where('id', '!=', 5)->whereNull('deleted_at')->firstOrFail();

        return view('cart.index', compact(['data', 'address', 'total','sales_agent','customer','c_product_groups','c_product_category','c_product_line','weight','volume','selected_address','cart_address','api_conn']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function addToCart(Request $request,$id){

        if(!@Auth::user()->multi_sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        }

        $product = Product::findOrFail($id);

        $avl_qty = $product->quantity_on_stock - $product->quantity_ordered_by_customers;
        $customer_id = explode(',', Auth::user()->multi_customer_id);
        $customer_price_list_no = get_customer_price_list_no_arr($customer_id);

        $customer_vat  = Customer::whereIn('id', $customer_id)->get();
        foreach($customer_vat as $cust){
            if($product->sap_connection_id === $cust->real_sap_connection_id){
                $price = get_product_customer_price(@$product->item_prices,@$customer_price_list_no[$product->sap_connection_id], false, false, $cust);
            }
        }

        if($price < 1){
            return $response = ['status'=>false,'message'=>"The product price is not a valid."];
        }

        $sap_customer_arr = get_sap_customer_arr(@Auth::user());
        if(isset($id)){
                $request->due_date = ($request->due_date === null) ? date('m/d/Y') : $request->due_date;
                $cart_info = Cart::where(['customer_id'=>@$sap_customer_arr[$product->sap_connection_id],'product_id'=>$id])->first();
                if($cart_info){
                    $due_date = strtr($request->due_date, '/', '-');
                    $due_date_new = \Carbon\Carbon::createFromFormat('m-d-Y', $due_date)->format('Y-m-d');
                    $cart = Cart::find($cart_info->id);
                    $cart->qty = $cart_info->qty + $request->qty;
                    $cart->customer_id = @$sap_customer_arr[$product->sap_connection_id];
                    $cart->product_id = $id;
                    $cart->address = @$request->address;
                    if($request->due_date != ""){
                        $cart->due_date = date('Y-m-d',strtotime($due_date_new));
                    }else{
                        $cart->due_date = '';
                    }
                }else{
                    $due_date = strtr($request->due_date, '/', '-');
                    $due_date_new = \Carbon\Carbon::createFromFormat('m-d-Y', $due_date)->format('Y-m-d');
                    $cart = new Cart();
                    $cart->qty = $request->qty;
                    $cart->customer_id = @$sap_customer_arr[$product->sap_connection_id];
                    $cart->product_id = $id;
                    $cart->address = @$request->address;
                    if($request->due_date != ""){
                        $cart->due_date = date('Y-m-d',strtotime($due_date_new));
                    }else{
                        $cart->due_date = '';
                    }
                }


                $cart->save();

            $customer_id = explode(',', @Auth::user()->multi_customer_id);
            $count = Cart::whereIn('customer_id', $customer_id)->count();

            return $response = ['status'=>true,'message'=>"Product added to cart successfully.", 'count'=> $count];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong please try again."];
    }

    public function updateQty(Request $request, $id){
        $data = $request->all();
        if(isset($id) && isset($data['qty'])){
            $cart = Cart::findOrFail($id);

            if(is_numeric($data['qty'])){
                if($data['qty'] > 0){
                    $avl_qty = $cart->product->quantity_on_stock - $cart->product->quantity_ordered_by_customers;
                    // if($avl_qty < ($data['qty'])){
                    //     return $response = ['status'=>false,'message'=>"The product quantity is not available."];
                    // }
                    $cart->qty = $data['qty'];
                    $cart->save();
                    $pr_weight = Product::where('id',$cart->product_id)->first();
                    $weight_individual = ($cart->qty * @$pr_weight->sales_unit_weight);

                    $customer_id = explode(',', @Auth::user()->multi_customer_id);

                    $price_no = @get_customer_price_list_no_arr($customer_id)[@$cart->product->sap_connection_id];
                    $price = get_product_customer_price(@$cart->product->item_prices,$price_no, false, false, $cart->customer) * $cart->qty;

                    $total = 0;
                    $data = Cart::with('product')->whereIn('customer_id', $customer_id)->get();

                    $weight = 0;
                    $volume = 0;
                    foreach($data as $item){

                        $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                        $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no, false, false, $item->customer) * $item->qty;
                        $total += $subTotal;
                        $weight = $weight + ($item->qty * @$item->product->sales_unit_weight);
                        $volume = $volume + ($item->qty * @$item->product->sales_unit_volume);

                    }
                } else {
                    return $response = ['status'=>false,'message'=>"Quantity value must be greater than 0(Zero)."];
                }
            } else {
                return $response = ['status'=>false,'message'=>"Quantity value must be numeric."];
            }


            return $response = ['status'=>true,'message'=>"Product quantity updated successfully.",'total' => number_format_value($total),'weight' => number_format_value($weight),'volume' => number_format_value($volume),'price' => number_format_value($price),'weight_individual'=>number_format_value($weight_individual)];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong !"];
    }

    public function qtyPlus($id){
        if(isset($id)){
            $cart = Cart::findOrFail($id);

            $avl_qty = $cart->product->quantity_on_stock - $cart->product->quantity_ordered_by_customers;
            // if($avl_qty < ($cart->qty + 1)){
            //     return $response = ['status'=>false,'message'=>"The product quantity is not available."];
            // }
            $cart->qty = $cart->qty + 1;
            $cart->save();

            $pr_weight = Product::where('id',$cart->product_id)->first();
            $weight_individual = ($cart->qty * @$pr_weight->sales_unit_weight);

            $customer_id = explode(',', @Auth::user()->multi_customer_id);

            $price_no = @get_customer_price_list_no_arr($customer_id)[@$cart->product->sap_connection_id];
            $price = get_product_customer_price(@$cart->product->item_prices,$price_no) * $cart->qty;

            $total = 0;
            $data = Cart::with('product')->whereIn('customer_id', $customer_id)->get();

            $weight = 0;
            $volume = 0;
            foreach($data as $item){

                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no) * $item->qty;
                $total += $subTotal;
                $weight = $weight + ($item->qty * @$item->product->sales_unit_weight);
                $volume = $volume + ($item->qty * @$item->product->sales_unit_volume);

            }

            return $response = ['status'=>true,'message'=>"Product quantity updated successfully.", 'total' => number_format_value($total),'weight' => ($weight),'volume' => ($volume),'price' => number_format_value($price),'weight_individual'=>number_format_value($weight_individual)];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong !"];
    }

    public function qtyMinus($id){
        if(isset($id)){
            $cart = Cart::findOrFail($id);
            $cart->qty = $cart->qty - 1;
            if($cart->qty <= 0){
                $cart->delete();
                $message = "Product removed from cart.";
            } else {
                $cart->save();
                $message = "Product quantity updated successfully.";
            }
            $customer_id = explode(',', @Auth::user()->multi_customer_id);
            $price_no = @get_customer_price_list_no_arr($customer_id)[@$cart->product->sap_connection_id];
            $price = get_product_customer_price(@$cart->product->item_prices,$price_no) * $cart->qty;

            $total = 0;
            $weight = 0;
            $volume = 0;
            $data = Cart::with('product')->whereIn('customer_id', $customer_id)->get();

            $pr_weight = Product::where('id',$cart->product_id)->first();
            $weight_individual = ($cart->qty * @$pr_weight->sales_unit_weight);
            //$weight_individual = number_format($weight_individual);

            foreach($data as $item){
                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no) * $item->qty;
                $total += $subTotal;

                $weight = $weight + ($item->qty * @$item->product->sales_unit_weight);
                $volume = $volume + ($item->qty * @$item->product->sales_unit_volume);
            }

            return $response = ['status'=>true,'message'=> $message, 'total' => number_format_value($total), 'count' => count($data), 'cart_count' => count($data),'weight' => number_format_value($weight),'volume' => number_format_value($volume),'price' => number_format_value($price),'weight_individual'=>number_format_value($weight_individual)];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong !"];
    }

    public function removeFromCart($id){
        if(isset($id)){
            Cart::where('id', $id)->delete();
            $customer_id = explode(',', Auth::user()->multi_customer_id);
            $total = 0;
            $weight = 0;
            $volume = 0;

            $data = Cart::with('product')->whereIn('customer_id', $customer_id)->get();

            foreach($data as $item){
                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no, false, false, $item->customer) * $item->qty;
                $total += $subTotal;

                $weight = $weight + ($item->qty * @$item->product->sales_unit_weight);
                $volume = $volume + ($item->qty * @$item->product->sales_unit_volume);
            }

            return $response = ['status' => true,'message' => 'Product removed from cart.', 'total' => number_format($total, 2), 'count' => count($data),'weight' => number_format_value($weight),'volume' => number_format_value($volume)];
        }


        return $response = ['status'=>false,'message'=>"Something went wrong please try again."];
    }

    public function placeOrder(Request $request){
        $customer_sap_connection_ids = explode(',', @Auth::user()->multi_sap_connection_id);

        if(empty($customer_sap_connection_ids)){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in our database."];
        }

        $data = $request->all();
        $total_amount = 0;
        $customer_id = explode(',', Auth::user()->multi_customer_id);
        $products = Cart::whereIn('customer_id', $customer_id)->get();
        if( !empty($products) ){
            foreach($products as $value){

                if($value->customer->vat_group === null){
                    return ['status'=>false, 'message' => "VatGroup for this Customer is emtpy, please contact CMD."];  
                }

                $product = @$value->product;
                $avl_qty = $product->quantity_on_stock - $product->quantity_ordered_by_customers;
                // if($avl_qty == 0){
                //     return $response = ['status'=>false, 'message'=> 'The product "'.$product->item_name.'" quantity is not available at the moment please remove from order.'];
                // }else if($avl_qty < @$value->qty){
                //     return $response = ['status'=>false, 'message'=> 'The product "'.$product->item_name.'" quantity value must be less then '.$avl_qty.'.'];
                // }

                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$value->product->sap_connection_id];

                $price = get_product_customer_price(@$value->product->item_prices, @$customer_price_list_no, false, false, $value->customer);
                if($price < 1){
                    return $response = ['status'=>false,'message'=>'The product "'.@$value->product->item_name.'" price is not a valid so please remove that product from cart for further process. '];
                }

                $total_amount += $price;
            }
        }

        if($total_amount < 1){
            return $response = ['status'=>false,'message'=>"Oops! The amount is not valid."];
        }

        $rules = array(
                'address_id' => 'required|exists:customer_bp_addresses,id',
                'due_date' => 'required',
                // 'products.*.product_id' => 'distinct|exists:products,id,sap_connection_id,'.@Auth::user()->customer->sap_connection_id,
                'remark' => 'nullable|max:254'
            );

        $messages = array(
                'products.*.product_id.exists' => "Oops! Customer or Items can not be located in the DataBase.",
                'due_date.required' => "Please select delivery date.",
            );

        $validator = Validator::make($data, $rules, $messages);
        // dd($validator->errors());
        if ($validator->fails()) {
            return $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            //$customer_id = @Auth::user()->customer_id;
            $address_id = $data['address_id'];
            $due_date = strtr($data['due_date'], '/', '-');
            $due_date_new = \Carbon\Carbon::createFromFormat('m-d-Y', $due_date)->format('Y-m-d');

            $address = CustomerBpAddress::find($address_id);

            foreach ($customer_sap_connection_ids as $key => $sap_connection_id) {

                $real_sap_connection_id = $sap_connection_id;

                if($sap_connection_id == 5){ //Solid Trend
                    $sap_connection_id = 1;
                }

                $products = Cart::whereIn('customer_id', $customer_id)
                                ->whereHas('product', function($q) use ($sap_connection_id){
                                    $q->where('sap_connection_id', $sap_connection_id);
                                })->get();

                if(count($products)){

                    // Start Order
                    $customer = Customer::where('sap_connection_id', $real_sap_connection_id)->where('u_card_code', @Auth::user()->u_card_code)->first();

                    if(!empty($customer) && !empty($address)){

                        $order = new LocalOrder();
                        $order->customer_id = $customer->id;
                        $order->address_id = $address_id;
                        $order->due_date = date('Y-m-d',strtotime($due_date_new));
                        $order->placed_by = "C";
                        $order->confirmation_status = "P";
                        $order->sap_connection_id = $real_sap_connection_id;
                        $order->remarks = $request->remark;
                        $order->save();

                        $total = 0;

                        $is_need_delete_order = false;
                        foreach($products as $value){

                            $avl_qty = $value->product->quantity_on_stock - $value->product->quantity_ordered_by_customers;
                            //if($avl_qty >= @$value['qty']){
                                $item = new LocalOrderItem();
                                $item->local_order_id = $order->id;
                                $item->product_id = @$value['product_id'];
                                $item->quantity = @$value['qty'];
                                $item->price = get_product_customer_price(@$value->product->item_prices,@$customer->price_list_num, false, false, $customer);
                                $item->total = $item->price * $item->quantity;
                                $item->save();

                                $total += $item->total;
                            // }else{
                            //     $is_need_delete_order = true;
                            // }
                        }

                        $order->total = $total;
                        $order->save();

                        // if($is_need_delete_order && count($products) == 1){
                        //     $order->delete();
                        // }else{

                            //========================= start of working code =======================
                            // try{
                            //     $sap_connection = SapConnection::find($real_sap_connection_id);

                            //     if(!is_null($sap_connection)){
                            //         $sap = new SAPOrderPost($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $sap_connection->id);

                            //         if($order->id){
                            //             $sap->pushOrder($order->id);
                            //         }

                            //         // $localOrders = LocalOrder::find($order->id);
                            //         // $quotation = Quotation::with('customer')->where('doc_entry',@$localOrders->doc_entry)->first();

                            //         //$user = @$quotation->customer->user;

                            //         // $link = route('orders.show', @$quotation->id);
                            //         // // Send Mail.
                            //         // Mail::send('emails.order_placed', array('link'=>$link, 'order_no'=>@$quotation->doc_entry, 'status'=>getOrderStatusByQuotation($quotation)), function($message) use($user) {
                            //         //     $message->to('mansiparikh95@gmail.com', $user->name)
                            //         //             ->subject('Order Placed');
                            //         // });

                            //     }
                            // } catch (\Exception $e) {

                            // }
                            //========================= end of working code =======================


                        //}
                    }

                    // End Order
                }
            }

            Cart::whereIn('customer_id', $customer_id)->delete();
        }

        return $response = ['status' => true, 'message' => 'Your order placed successfully!'];
    }

    public function removeAllFromCart(Request $request){
        if(!empty($request->chk)){
            Cart::whereIn('id', $request->chk)->delete();
            $customer_id = explode(',', Auth::user()->multi_customer_id);
            $total = 0;
            $weight = 0;
            $volume = 0;

            $data = Cart::with('product')->whereIn('customer_id', $customer_id)->get();

            foreach($data as $item){
                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no, false, false, $item->customer) * $item->qty;
                $total += $subTotal;

                $weight = $weight + ($item->qty * @$item->product->sales_unit_weight);
                $volume = $volume + ($item->qty * @$item->product->sales_unit_volume);
            }

            return $response = ['status' => true,'message' => 'Product removed from cart.', 'total' => number_format($total, 2), 'count' => count($data),'weight' => number_format_value($weight),'volume' => number_format_value($volume)];
        }


        return $response = ['status'=>false,'message'=>"Something went wrong please try again."];
    }


    public function getAllProductList(Request $request){
        $c_product_tires_category = $c_product_item_line = $c_product_group = array();

        $customer_id = [];
        $customer = collect();
        $sap_connection_id = [];
        $customer_price_list_no = [];

        if(userrole() == 4){
            $customer_id = explode(',', Auth::user()->multi_customer_id);
            $sap_connection_id = explode(',', Auth::user()->multi_real_sap_connection_id);
            $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
            $customer_vat  = Customer::whereIn('id', $customer_id)->get();

        }elseif (!is_null(@Auth::user()->created_by)) {
            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                $customer_id = explode(',', @$customer->multi_customer_id);
                $sap_connection_id = explode(',', @$customer->multi_real_sap_connection_id);
                $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
            }
        }elseif(userrole() == 14){
            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
            $sap_connection_id = array( @Auth::user()->sap_connection_id );
        }

        if(in_array(5, $sap_connection_id)){
            array_push($sap_connection_id, '5');
        }

        // Is Customer
        if(!empty($customer_id)){

            // Product Group
            $c_product_group = CustomerProductGroup::with('product_group')->whereIn('customer_id', $customer_id)->get();
            $c_product_group = array_column( $c_product_group->toArray(), 'product_group_id' );

            // Product Item Line
            $c_product_item_line = CustomerProductItemLine::with('product_item_line')->whereIn('customer_id', $customer_id)->get();
            $c_product_item_line = array_column( $c_product_item_line->toArray(), 'product_item_line_id' );

            // Product Tires Category
            $c_product_tires_category = CustomerProductTiresCategory::with('product_tires_category')->whereIn('customer_id', $customer_id)->get();
            $c_product_tires_category = array_column( $c_product_tires_category->toArray(), 'product_tires_category_id' );
        }

        if(empty($c_product_group) && empty($c_product_tires_category) && empty($c_product_item_line)){
            $products = collect([]);
            return DataTables::of($products)->make(true);
        }

        //$where = array('is_active' => 1);

        $products = Product::whereRaw('last_sync_at > "2023-03-27 09:39:36"');

        $products->where('products.is_active',1);

        $products->whereHas('group', function($q){
            $q->where('is_active', true);
        });

        if(@$request->filter_search != ""){
            $products->where('item_name','LIKE',"%".$request->filter_search."%");
        }

        if(@$request->filter_search1 != ""){
          $products->where(function($q) use ($request) {
            $q->orwhere('products.item_code','LIKE',"%".$request->filter_search1."%");
            $q->orwhere('products.item_name','LIKE',"%".$request->filter_search1."%");
          });
        }

        $products->where(function($q) use ($request, $c_product_tires_category, $c_product_item_line, $c_product_group) {

            if(!empty($c_product_group)){
                $q->orwhereHas('group', function($q1) use ($c_product_group){
                    $q1->whereIn('id', $c_product_group);
                });
            }

            if(!empty($c_product_tires_category)){
                $q->orwhereHas('product_tires_category', function($q1) use ($c_product_tires_category){
                    $q1->whereIn('id', $c_product_tires_category);
                });
            }

            if(!empty($c_product_item_line)){
                $q->orwhereHas('product_item_line', function($q1) use ($c_product_item_line){
                    $q1->whereIn('id', $c_product_item_line);
                });
            }
        });

        $products->whereIn('products.sap_connection_id', $sap_connection_id);

        $products->when(!isset($request->order), function ($q) {
            $q->orderBy('item_name', 'asc');
        });

        if(@$request->filter_brand != ""){
            $products->whereHas('group', function($q) use ($request){
                $q->where('group_name', $request->filter_brand);
            });
        }

        if($request->filter_product_category != ""){
            $products->where('u_tires',$request->filter_product_category);
        }

        if($request->filter_product_line != ""){
            $products->where('u_item_line',$request->filter_product_line);
        }

        return DataTables::of($products)
                          ->addIndexColumn()
                          ->addColumn('item_name', function($row) {
                            $html = "";
                                $html .= '<div class="d-flex align-items-center">

                                            <div class="d-flex justify-content-start flex-column">';

                                $html .= @$row->item_name ?? " ";

                                $html .= '<span class="text-muted fw-bold text-muted d-block fs-7">';

                                $html .= "Code: ".$row->item_code;
                                return $html;
                            })
                            ->addColumn('item_code', function($row) {
                                if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.(@$row->item_code ?? "").'</span>';
                                }else{
                                    return @$row->item_code ?? "";
                                }
                            })
                            ->addColumn('brand', function($row) {
                                if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.(@$row->group->group_name ?? "").'</span>';
                                }else{
                                    return @$row->group->group_name ?? "";
                                }
                            })
                            ->addColumn('u_item_line', function($row) {
                                if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.(@$row->u_item_line_sap_value->value ?? @$row->u_item_line ?? "-").'</span>';
                                }else{
                                    return @$row->u_item_line_sap_value->value ?? @$row->u_item_line ?? "-";
                                }
                            })
                            ->addColumn('u_tires', function($row) {
                                if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.(@$row->u_tires ?? "").'</span>';
                                }else{
                                    return @$row->u_tires ?? "-";
                                }
                            })
                            ->addColumn('price', function($row) use ($customer_price_list_no, $customer_vat) {

                                $sap_connection_id = $row->sap_connection_id;
                                
                                // $vat_rate = 0;
                                $currency_symbol = '';
                                foreach($customer_vat as $cust){
                                    if($sap_connection_id === $cust->real_sap_connection_id){
                                    //   $vat_rate = get_vat_rate($cust);
                                        $currency_symbol = get_product_customer_currency(@$row->item_prices, $cust->price_list_num);
                                        $price = get_product_customer_price(@$row->item_prices,@$customer_price_list_no[$sap_connection_id]);
                                    }
                                }
                                
                                // if($vat_rate !== 0){
                                //     $price = $price / $vat_rate;
                                // }

                                if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.$currency_symbol.' '.number_format_value($price).'</span>';
                                }else{
                                    return $currency_symbol." ".number_format_value($price);

                                }
                            })
                            ->addColumn('action', function($row) {
                                $btn = "";
                                if(@Auth::user()->role_id == 4){
                                    if(is_in_cart1(@$row->id) == 1){
                                        $btn = '<a class="btn btn-icon btn-bg-primary btn-active-color-primary btn-sm custom_add_product" href="'.route('cart.index').'" title="Go to cart">Add Product</a>';
                                    }else{
                                            $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-primary btn-active-color-success btn-sm addToCart custom_add_product" data-url="'.route('cart.add',@$row->id).'" title="Add Product">Add Product</a>';

                                            $btn .= '<a class="btn btn-icon btn-bg-primary btn-active-color-primary btn-sm goToCart custom_add_product" href="'.route('cart.index').'" style="display:none" title="Add Product">Add Product</a>';

                                    }
                                }

                                return $btn;
                            })
                            ->orderColumn('item_name', function ($query, $order) {
                                $query->orderBy('item_name', $order);
                            })
                          ->orderColumn('item_code', function ($query, $order) {
                              $query->orderBy('item_code', $order);
                          })
                          ->orderColumn('u_tires', function ($query, $order) {
                              $query->orderBy('u_tires', $order);
                          })
                          ->orderColumn('u_item_line', function ($query, $order) {
                              $query->orderBy('u_item_line', $order);
                          })
                          ->orderColumn('created_date', function ($query, $order) {
                              $query->orderBy('created_date', $order);
                          })
                          ->orderColumn('status', function ($query, $order) {
                              $query->orderBy('is_active', $order);
                          })
                          ->orderColumn('brand', function ($query, $order) {

                              $query->join("product_groups",function($join){
                                  $join->on("products.items_group_code","=","product_groups.number")
                                      ->on("products.sap_connection_id","=","product_groups.sap_connection_id");
                              })->orderBy('product_groups.group_name', $order);
                          })
                          ->rawColumns(['status','action','item_name', 'item_code','brand','u_tires','u_item_line','price'])
                          ->make(true);
    }
}
