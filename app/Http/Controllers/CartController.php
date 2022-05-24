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

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_id = @Auth::user()->customer_id;

        $address = CustomerBpAddress::where('customer_id', $customer_id)->orderBy('order', 'ASC')->get();

        $total = 0;
        $customer_id = explode(',', Auth::user()->multi_customer_id);
        $data = Cart::with(['product', 'customer'])->whereIn('customer_id', $customer_id)->get();

        foreach($data as $item){

            $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

            $subTotal = get_product_customer_price(@$item->product->item_prices, $customer_price_list_no) * $item->qty;
            $total += $subTotal;
        }
        return view('cart.index', compact(['data', 'address', 'total']));
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

    public function addToCart($id){
        if(!@Auth::user()->multi_sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        }

        $product = Product::findOrFail($id);

        $avl_qty = $product->quantity_on_stock - $product->quantity_ordered_by_customers;
        if($avl_qty < 1){
            return $response = ['status'=>false,'message'=>"The product quantity is not available."];
        }

        $sap_customer_arr = get_sap_customer_arr(@Auth::user());

        if(isset($id)){
            $cart = new Cart();
            $cart->customer_id = @$sap_customer_arr[$product->sap_connection_id];
            $cart->product_id = $id;
            $cart->qty = 1;
            $cart->save();

            $count = Cart::where('customer_id', $cart->customer_id)->count();

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
                    if($avl_qty < ($data['qty'])){
                        return $response = ['status'=>false,'message'=>"The product quantity is not available."];
                    }
                    $cart->qty = $data['qty'];
                } else {
                    return $response = ['status'=>false,'message'=>"Quantity value must be greater than 0(Zero)."];
                }
            } else {
                return $response = ['status'=>false,'message'=>"Quantity value must be numeric."];
            }
            $cart->save();

            return $response = ['status'=>true,'message'=>"Product quantity updated successfully."];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong !"];
    }

    public function qtyPlus($id){
        if(isset($id)){
            $cart = Cart::findOrFail($id);

            $avl_qty = $cart->product->quantity_on_stock - $cart->product->quantity_ordered_by_customers;
            if($avl_qty < ($cart->qty + 1)){
                return $response = ['status'=>false,'message'=>"The product quantity is not available."];
            }

            $cart->qty = $cart->qty + 1;
            $cart->save();

            $customer_id = explode(',', @Auth::user()->multi_customer_id);
            $total = 0;
            $data = Cart::with('product')->whereIn('customer_id', $customer_id)->get();
            
            foreach($data as $item){

                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no) * $item->qty;
                $total += $subTotal;
            }

            return $response = ['status'=>true,'message'=>"Product quantity updated successfully.", 'total' => number_format_value($total)];
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
            $total = 0;
            $data = Cart::with('product')->where('customer_id', $customer_id)->get();
            $cartCount = Cart::whereIn('customer_id', $customer_id)->count();

            foreach($data as $item){
                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no) * $item->qty;
                $total += $subTotal;
            }

            return $response = ['status'=>true,'message'=> $message, 'total' => number_format_value($total), 'count' => count($data), 'cart_count' => $cartCount];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong !"];
    }

    public function removeFromCart($id){
        if(isset($id)){
            Cart::where('id', $id)->delete();
            $customer_id = explode(',', Auth::user()->multi_customer_id);
            $total = 0;
            $data = Cart::with('product')->whereIn('customer_id', $customer_id)->get();

            foreach($data as $item){
                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no) * $item->qty;
                $total += $subTotal;
            }

            return $response = ['status' => true,'message' => 'Product removed from cart.', 'total' => number_format($total, 2), 'count' => count($data)];
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

                $product = @$value->product;
                $avl_qty = $product->quantity_on_stock - $product->quantity_ordered_by_customers;
                if($avl_qty == 0){
                    return $response = ['status'=>false, 'message'=> 'The product "'.$product->item_name.'" quantity is not available at the moment please remove from order.'];
                }else if($avl_qty < @$value->qty){
                    return $response = ['status'=>false, 'message'=> 'The product "'.$product->item_name.'" quantity value must be less then '.$avl_qty.'.'];
                }

                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$value->product->sap_connection_id];

                $total_amount += get_product_customer_price(@$value->product->item_prices, @$customer_price_list_no);
            }
        }

        if($total_amount < 1){
            return $response = ['status'=>false,'message'=>"Oops! The amount is not valid."];
        }
        
        $rules = array(
                'address_id' => 'required|exists:customer_bp_addresses,id',
                'due_date' => 'required',
                // 'products.*.product_id' => 'distinct|exists:products,id,sap_connection_id,'.@Auth::user()->customer->sap_connection_id,
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
                        $order->due_date = date('Y-m-d',strtotime($due_date));
                        $order->placed_by = "C";
                        $order->confirmation_status = "P";
                        $order->sap_connection_id = $real_sap_connection_id;
                        $order->save();

                        $total = 0;

                        $is_need_delete_order = false;
                        foreach($products as $value){

                            $avl_qty = $value->product->quantity_on_stock - $value->product->quantity_ordered_by_customers;
                            if($avl_qty > @$value['qty']){
                                $item = new LocalOrderItem();
                                $item->local_order_id = $order->id;
                                $item->product_id = @$value['product_id'];
                                $item->quantity = @$value['qty'];
                                $item->price = get_product_customer_price(@$value->product->item_prices,@$customer->price_list_num);
                                $item->total = $item->price * $item->quantity;
                                $item->save();
                                
                                $total += $item->total;
                            }else{
                                $is_need_delete_order = true;
                            }
                        }

                        $order->total = $total;
                        $order->save();
                        
                        if($is_need_delete_order && count($products) == 1){
                            $order->delete();
                        }else{
                            try{
                                $sap_connection = SapConnection::find($real_sap_connection_id);

                                if(!is_null($sap_connection)){
                                    $sap = new SAPOrderPost($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $sap_connection->id);

                                    if($order->id){
                                        $sap->pushOrder($order->id);
                                    }
                                }
                            } catch (\Exception $e) {

                            }
                        }
                    }

                    // End Order
                }
            }

            Cart::whereIn('customer_id', $customer_id)->delete();
        }

        return $response = ['status' => true, 'message' => 'Order Placed Successfully!'];
    }
}
