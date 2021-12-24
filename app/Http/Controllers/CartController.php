<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Support\PostOrder;
use App\Models\Cart;
use App\Models\CustomerBpAddress;
use App\Models\Customer;
use App\Models\LocalOrder;
use App\Models\LocalOrderItem;
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
        $customer_id = Auth::user()->customer_id;
        $total = 0;
        $data = Cart::with(['product', 'customer'])->where('customer_id', $customer_id)->get();
        $address = CustomerBpAddress::where('customer_id', $customer_id)->get();

        foreach($data as $item){
            $subTotal = get_product_customer_price(@$item->product->item_prices,@Auth::user()->customer->price_list_num) * $item->qty;
            $total += $subTotal;
        }
        // dd($data);
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
        if(!@Auth::user()->customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        }

        if(isset($id)){
            $cart = new Cart();
            $cart->customer_id = Auth::user()->customer_id;
            $cart->product_id = $id;
            $cart->qty = 1;
            $cart->save();

            return $response = ['status'=>true,'message'=>"Product added to cart successfully."];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong please try again."];
    }

    public function updateQty(Request $request, $id){
        $data = $request->all();
        if(isset($id) && isset($data['qty'])){
            $cart = Cart::findOrFail($id);
            if(is_numeric($data['qty'])){
                if($data['qty'] > 0){
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
            $cart->qty = $cart->qty + 1;
            $cart->save();

            return $response = ['status'=>true,'message'=>"Product quantity updated successfully."];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong !"];
    }

    public function qtyMinus($id){
        if(isset($id)){
            $cart = Cart::findOrFail($id);
            $cart->qty = $cart->qty - 1;
            if($cart->qty <= 0){
                $cart->delete();
            } else {
                $cart->save();
            }

            return $response = ['status'=>true,'message'=>"Product quantity updated successfully."];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong !"];
    }

    public function removeFromCart($id){
        if(isset($id)){
            Cart::where('id', $id)->delete();

            return $response = ['status'=>true,'message'=>"Product removed from cart."];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong please try again."];
    }

    public function placeOrder(Request $request){
        if(!@Auth::user()->customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        }

        $data = $request->all();
        // dd($data);
        $customer_id = Auth::user()->customer_id;
        $address_id = $data['address_id'];
        $due_date = strtr($data['due_date'], '/', '-');
        $obj = array();

        $customer = Customer::find($customer_id);
        $address = CustomerBpAddress::find($address_id);

        $order = new LocalOrder();
        if(!empty($customer) && !empty($address)){
            $order->customer_id = $customer_id;
            $order->address_id = $address_id;
            $order->due_date = date('Y-m-d',strtotime($due_date));
            $order->placed_by = "C";
            $order->confirmation_status = "P";
            $order->save();

            $products = Cart::where('customer_id', $customer_id)->get();
            if( !empty($products) ){
                foreach($products as $value){
                    // dd($value);
                    $item = new LocalOrderItem();
                    $item->local_order_id = $order->id;
                    $item->product_id = @$value['product_id'];
                    $item->quantity = @$value['qty'];
                    $item->save();
                }
            }
        }

        if($order->id){
            Cart::where('customer_id', $customer_id)->delete();
            $order = LocalOrder::where('id', $order->id)->with(['sales_specialist', 'customer', 'address', 'items.product'])->first();

            $obj['CardCode'] = $order->customer->card_code;
            $obj['DocDueDate'] = $order->due_date;

            $products = array();
            foreach($order->items as $item){
                $products[] = array(
                    'ItemCode' => $item->product->item_code,
                    'Quantity' => $item->quantity,
                    'TaxCode' => $order->address->tax_code,
                    'UnitPrice' => '30',
                );

            }
            $obj['DocumentLines'] = $products;

            $address = array();
            $address['ShipToStreet'] = $order->address->street;
            $address['ShipToZipCode'] = $order->address->zip_code;
            $address['ShipToCity'] = $order->address->city;
            $address['ShipToCountry'] = $order->address->country;
            $address['ShipToState'] = $order->address->state;
            $address['BillToAddressType'] = $order->address->address_type;

            $obj['AddressExtension'] = $address;
        }
        try {

            $post = new PostOrder('TEST-APBW', 'manager', 'test');

            $post = $post->pushOrder($obj);
            $order = LocalOrder::where('id', $order->id)->first();
            if($post['status']){
                $order->confirmation_status = 'C';
            } else {
                $order->confirmation_status = 'ERR';
                $order->message = $post['message'];
            }
            $order->save();

            $response = ['status' => true, 'message' => 'Order Placed Successfully!'];
        } catch (\Exception $e) {
            // dd($e);
            $response = ['status' => true, 'message' => 'Order Placed Successfully!'];
        }
        return $response;
    }
}
