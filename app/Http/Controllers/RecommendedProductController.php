<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use App\Support\PostOrder;
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
use DataTables;

class RecommendedProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('recommended-products.index');
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

    public function getAll(Request $request){
        $customer = collect();
        $customer_id = null;
        $customer_price_list_no = null;

        if(userrole() == 4){
            if (!is_null(@Auth::user()->created_by)) {
                $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
                if(!is_null($customer)){
                    $customer = @$customer->customer;
                    $customer_price_list_no = @$customer->price_list_num;
                }
            } else {
                $customer = @Auth::user()->customer;
                $customer_price_list_no = @Auth::user()->customer->price_list_num;
            }
        }

        if($request->custumer_id){
            $customer = User::where('role_id', 4)->where('id', $request->custumer_id)->first();
            if(!is_null($customer)){
                $customer = @$customer->customer;
                $customer_price_list_no = @$customer->price_list_num;
            }
        }

        $products = LocalOrderItem::orderBy('id', 'DESC');

        if (userrole() == 4) {
            $products->whereHas('order', function($q) use ($customer){
                $q->where('customer_id' ,'=', $customer->id);
            });
        }

        if($request->filter_search != ""){
            $products->whereHas('product', function($q) use ($request) {
                $q->where('item_name','LIKE',"%".$request->filter_search."%");
            });
        } else {
            $products->with('product');
        }

        $products->groupBy('product_id');

        return DataTables::of($products)
                          ->addIndexColumn()
                          ->addColumn('item_name', function($row) {
                              return @$row->product->item_name ?? "";
                          })
                          ->addColumn('item_code', function($row) {
                              return @$row->product->item_code ?? "";
                          })
                          ->addColumn('price', function($row) use ($customer_price_list_no) {
                              return "₱ ".get_product_customer_price(@$row->product->item_prices,$customer_price_list_no);
                          })
                          ->addColumn('action', function($row) use ($request) {
                            if(userrole() == 2){
                                if(is_in_cart(@$row->product->id, $request->customer_id) == 1){
                                    $btn = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" href="'.route('cart.index').'" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                }else{
                                    $btn = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm addToCart" data-url="'.route('recommended-products.cart.add',@$row->product->id).'" title="Add to Cart"><i class="fa fa-cart-arrow-down"></i></a>
                                    <a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm goToCart" href="'.route('cart.index').'" style="display:none" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                }
                            }
                            if(userrole() == 4){
                                if(is_in_cart(@$row->product->id) == 1){
                                    $btn = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" href="'.route('cart.index').'" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                }else{
                                    $btn = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm addToCart" data-url="'.route('cart.add',@$row->id).'" title="Add to Cart"><i class="fa fa-cart-arrow-down"></i></a>
                                    <a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm goToCart" href="'.route('cart.index').'" style="display:none" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                }
                            }


                            $btn .= '<a href="' . route('product-list.show',@$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm m-3">
                                    <i class="fa fa-eye"></i>
                                </a>';

                            return $btn;
                          })
                          ->orderColumn('item_name', function ($query, $order) {
                            $query->join("products",function($join){
                                $join->on("products.id","=","product_id");
                            })->orderBy('product_groups.group_name', $order);
                          })
                          ->rawColumns(['action'])
                          ->make(true);
    }

    function getCustomers(Request $request){
        $search = $request->search;
        $data = Customer::select('id','card_name')->whereHas('sales_specialist', function($q){
            $q->where('ss_id', @Auth::user()->id);
        });

        if($search != ''){
            $data = $data->where('card_name', 'like', '%' .$search . '%');
        }

        $data = $data->orderby('card_name','asc')->limit(50)->get();

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->card_name
            );
        }

        return response()->json($response);
    }


    /***
     * Customer Cart Management.
     */

    public function customerCart($customer_id)
    {
        // $customer_id = Auth::user()->customer_id;
        $total = 0;
        $user = User::where(['role_id' => 4, 'customer_id' => $customer_id])->first();
        $data = Cart::with(['product', 'customer'])->where('customer_id', $customer_id)->get();
        $address = CustomerBpAddress::where('customer_id', $customer_id)->get();
        $dates = User::whereHas('customer_delivery_schedules', function($q){
            $q->where('date','>',date("Y-m-d"));
        })->where('customer_id', $user->id)->first();

        $dates = [];

        foreach($data as $item){
            $subTotal = get_product_customer_price(@$item->product->item_prices,@Auth::user()->customer->price_list_num) * $item->qty;
            $total += $subTotal;
        }
        // dd($data);
        return view('recommended-products.cart', compact(['data', 'address', 'total', 'user', 'dates']));
    }

    public function addToCart($id, Request $request){
        $user = User::with('customer')->where('customer_id',$request->customer_id)->firstOrFail();
        if(!@$user->customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        }

        // if(isset($id)){
        //     $product = Product::findOrFail($id);
        //     if($product->sap_conection_id != @Auth::user()->customer->sap_connection_id){
        //         return $response = ['status'=>false,'message'=>"Oops! Customer or Items can not be located in the DataBase."];
        //     }
        // }

        if(isset($id)){
            $cart = Cart::updateOrCreate(
                ['customer_id' => $request->customer_id, 'product_id' => $id]
            );
            $cart->customer_id = $request->customer_id;
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

            $customer_id = @Auth::user()->customer_id;
            $total = 0;
            $data = Cart::with('product')->where('customer_id', $customer_id)->get();

            foreach($data as $item){
                $subTotal = get_product_customer_price(@$item->product->item_prices,@Auth::user()->customer->price_list_num) * $item->qty;
                $total += $subTotal;
            }

            return $response = ['status'=>true,'message'=>"Product quantity updated successfully.", 'total' => $total];
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
            $customer_id = @Auth::user()->customer_id;
            $total = 0;
            $data = Cart::with('product')->where('customer_id', $customer_id)->get();

            foreach($data as $item){
                $subTotal = get_product_customer_price(@$item->product->item_prices,@Auth::user()->customer->price_list_num) * $item->qty;
                $total += $subTotal;
            }

            return $response = ['status'=>true,'message'=> $message, 'total' => $total, 'count' => count($data)];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong !"];
    }

    public function removeFromCart($id){
        if(isset($id)){
            Cart::where('id', $id)->delete();
            $customer_id = @Auth::user()->customer_id;
            $total = 0;
            $data = Cart::with('product')->where('customer_id', $customer_id)->get();

            foreach($data as $item){
                $subTotal = get_product_customer_price(@$item->product->item_prices,@Auth::user()->customer->price_list_num) * $item->qty;
                $total += $subTotal;
            }

            return $response = ['status'=>true,'message'=>"Product removed from cart.", 'total' => $total, 'count' => count($data)];
        }


        return $response = ['status'=>false,'message'=>"Something went wrong please try again."];
    }

    public function placeOrder(Request $request){
        $data = $request->all();

        $customer_id = $data['customer_id'];
        $customer = Customer::find($customer_id);
        if(!@$customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        }

        $rules = array(
                'customer_id' => 'required',
                'address_id' => 'required|string|max:185',
                'due_date' => 'required',
                'products.*.product_id' => 'distinct|exists:products,id,sap_connection_id,'.@$customer->sap_connection_id,
            );

        $messages = array(
                'products.*.product_id.exists' => "Oops! Customer or Items can not be located in the DataBase.",
                'due_date.required' => "Please select delivery date.",
            );

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $address_id = $data['address_id'];
            $due_date = strtr($data['due_date'], '/', '-');

            // $customer = Customer::find($customer_id);
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
        }

        try{
            $sap_connection = SapConnection::find($customer->sap_connection_id);
            Cart::where('customer_id', $customer_id)->delete();

            if(!is_null($sap_connection)){
                $sap = new SAPOrderPost($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password);

                if($order->id){
                    $sap->pushOrder($order->id);
                }
            }
        } catch (\Exception $e) {

        }

        return $response = ['status' => true, 'message' => 'Order Placed Successfully!'];
    }

    public function saveToDraft(Request $request){
        // dd($request->all());
        $data = $request->all();

        $customer_id = $data['customer_id'];
        $customer = Customer::find($customer_id);
        if(!@$customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        }

        $rules = array(
                'customer_id' => 'required',
                'address_id' => 'required|string|max:185',
                'due_date' => 'required',
                'products.*.product_id' => 'distinct|exists:products,id,sap_connection_id,'.@$customer->sap_connection_id,
            );

        $messages = array(
                'products.*.product_id.exists' => "Oops! Customer or Items can not be located in the DataBase.",
                'due_date.required' => "Please select delivery date.",
            );

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $address_id = $data['address_id'];
            $due_date = strtr($data['due_date'], '/', '-');

            // $customer = Customer::find($customer_id);
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
            $response = ['status'=>true,'message'=> 'Order saved to customer draft Successfully.'];
        }

        return $response;
    }
}
