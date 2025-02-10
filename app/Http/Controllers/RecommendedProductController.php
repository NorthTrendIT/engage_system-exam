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
use App\Models\RecommendedProduct;
use App\Models\RecommendedProductAssignment;
use Validator;
use Auth;
use DataTables;

use App\Models\RecommendedProductItem;

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
        $id = decrypt($id);
        $company = SapConnection::all();
        $edit = RecommendedProduct::findOrFail($id);

        return view('product.recommend-edit',compact('company','edit'));
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
        $id = decrypt($id);
        $del = RecommendedProduct::find($id);
         
        if($del){
            $del->delete();
            RecommendedProductAssignment::where('assignment_id', $id)->delete();
            RecommendedProductItem::where('assignment_id', $id)->delete();
            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        
        return $response;
    }

    public function getAll(Request $request){ //previous recommended product
        $customer = collect();
        $customer_id = [];
        $customer_price_list_no = [];
        $sap_id = [];
        if(userrole() == 4){
            $customer_id = explode(',', Auth::user()->multi_customer_id);
            $sap_connection_id = Customer::whereIn('id',$customer_id)->pluck('sap_connection_id')->toArray();
            //$sap_connection_id = explode(',', Auth::user()->multi_real_sap_connection_id);
            $customer_price_list_no = get_customer_price_list_no_arr($customer_id);

        }elseif (!is_null(@Auth::user()->created_by)) {

            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                $customer_id = explode(',', @$customer->multi_customer_id);
                $sap_connection_id = Customer::whereIn('id',$customer_id)->pluck('sap_connection_id')->toArray();
                //$sap_connection_id = explode(',', @$customer->multi_real_sap_connection_id);
                $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
            }

        }else if($request->customer_id){
            $customer = Customer::findOrFail($request->customer_id);
            if(!is_null($customer)){
                $customer_id = array($request->customer_id);
                $sap_connection_id = explode(',', @$customer->real_sap_connection_id);
                $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
            }

        }elseif(userrole() == 14){
            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
            $sap_connection_id = array( @Auth::user()->sap_connection_id );
        }
        $sap_customer_arr = array_combine($sap_connection_id, $customer_id);
        $customer_vat  = Customer::whereIn('id', $customer_id)->get();

        $products = LocalOrderItem::orderBy('id', 'DESC');
        if (!empty($customer_id)) {
            $products->whereHas('order', function($q) use ($customer_id){
                $q->whereIn('customer_id', $customer_id);
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
                                $html = "";
                                $html .= '<div class="d-flex align-items-center">
                                            
                                            <div class="d-flex justify-content-start flex-column">';
                                $html .= @$row->product->item_name ?? " ";

                                $html .= '<span class="text-muted fw-bold text-muted d-block fs-7 text-center">';

                                $html .= "Code: ".$row->product->item_code;
                                return $html;
                          })
                          ->addColumn('item_code', function($row) {
                                if($row->product->quantity_on_stock - $row->product->quantity_ordered_by_customers < 1){
                                    return '<span class="" title="Not Available">'.(@$row->product->item_code ?? "").'</span>';
                                }else{
                                    return @$row->product->item_code ?? "";
                                }
                            })
                          ->addColumn('price', function($row) use ($customer_price_list_no, $customer_vat) {
                                $sap_connection_id = $row->product->sap_connection_id;
                                $currency_symbol = '';
                                foreach($customer_vat as $cust){
                                    if($sap_connection_id === $cust->real_sap_connection_id){                                       
                                        $currency_symbol = get_product_customer_currency(@$row->product->item_prices, $cust->price_list_num);
                                        $price = get_product_customer_price(@$row->product->item_prices,@$customer_price_list_no[$sap_connection_id]);
                                    }
                                }

                                if(round($row->product->quantity_on_stock - $row->product->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.$currency_symbol.' '.number_format_value($price).'</span>';
                                }else{
                                    return $currency_symbol." ".number_format_value($price);
                                }
                          })
                          ->addColumn('qty', function($row){
                            $html= '<div class="button-wrap">
                                        <div class="counter">
                                            <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyMinus">
                                                <i class="fas fa-minus"></i>
                                            </a>

                                            <input class="form-control qty text-center" type="number" min="1" value="1" id="qty_'.$row->product->id.'">

                                            <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyPlus">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>';
                            return $html;
                          })
                          ->addColumn('action', function($row) use ($sap_customer_arr) {
                            $btn = "";
                            if(userrole() == 14){
                                // if(is_in_cart(@$row->product->id, @$sap_customer_arr[@$row->product->sap_connection_id]) == 1){
                                //     $btn = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mx-2" href="'.route('recommended-products.goToCart', @$sap_customer_arr[@$row->product->sap_connection_id]).'" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                // }else{
                                //     if(@$row->product->quantity_on_stock - @$row->product->quantity_ordered_by_customers < 1){
                                //         $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-danger btn-smmx-2 " title="Not Available"><i class="fa fa-cart-arrow-down"></i></a>';
                                //     }else{
                                        $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm mx-2 addToCart" data-url="'.route('cart.add',@$row->product->id).'" title="Add to Cart"><i class="fa fa-cart-arrow-down"></i></a>';
                                    // }

                                //     $btn .= '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mx-2 goToCart" href="'.route('recommended-products.goToCart', @$sap_customer_arr[@$row->product->sap_connection_id]).'" style="display:none" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                // }
                            }
                            if(userrole() == 4){
                                // if(is_in_cart(@$row->product->id, @$sap_customer_arr[@$row->product->sap_connection_id]) == 1){
                                //     $btn = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mx-2" href="'.route('cart.index').'" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                // }else{
                                //     if(@$row->product->quantity_on_stock - @$row->product->quantity_ordered_by_customers < 1){
                                //         $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm mx-2" title="Not Available"><i class="fa fa-cart-arrow-down"></i></a>';
                                //     }else{
                                        $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm mx-2 addToCart" data-url="'.route('cart.add',@$row->product->id).'" title="Add to Cart"><i class="fa fa-cart-arrow-down"></i></a>';
                                //     }

                                //     $btn .= '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mx-2 goToCart" href="'.route('cart.index').'" style="display:none" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                // }
                            }


                            $btn .= '<a href="' . route('product-list.show',['id' => @$row->product->id, 'customer_id' => @$row->customer_id]). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm m-3" target="_blank">
                                    <i class="fa fa-eye"></i>
                                </a>';
                            return $btn;
                          })
                          ->orderColumn('item_name', function ($query, $order) {
                            $query->join("products",function($join){
                                $join->on("products.id","=","product_id");
                            })->orderBy('product_groups.group_name', $order);
                          })
                          ->rawColumns(['action','price','item_name', 'qty'])
                          ->make(true);
    }


    public function getAll1(Request $request){
        $customer = collect();
        $customer_id = [];
        $customer_price_list_no = [];
        $sap_id = [];
        if(userrole() == 4){
            $customer_id = explode(',', Auth::user()->multi_customer_id);
            $sap_connection_id = Customer::whereIn('id',$customer_id)->pluck('sap_connection_id')->toArray();
            //$sap_connection_id = explode(',', Auth::user()->multi_real_sap_connection_id);
            $customer_price_list_no = get_customer_price_list_no_arr($customer_id);

        }elseif (!is_null(@Auth::user()->created_by)) {

            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                $customer_id = explode(',', @$customer->multi_customer_id);
                $sap_connection_id = Customer::whereIn('id',$customer_id)->pluck('sap_connection_id')->toArray();
                //$sap_connection_id = explode(',', @$customer->multi_real_sap_connection_id);
                $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
            }

        }else if($request->customer_id){
            $customer = Customer::findOrFail($request->customer_id);
            if(!is_null($customer)){
                $customer_id = array($request->customer_id);
                $sap_connection_id = explode(',', @$customer->real_sap_connection_id);
                $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
            }

        }elseif(userrole() == 14){
            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
            $sap_connection_id = array( @Auth::user()->sap_connection_id );
        }
        $sap_customer_arr = array_combine($sap_connection_id, $customer_id);
        $customer_vat  = Customer::whereIn('id', $customer_id)->get();
        
        $products = RecommendedProductItem::whereHas('product', function($q){
                        $q->where('is_active', true);
                    });
        if (!empty($customer_id)) {
            $products->whereHas('recommended.assignments.customer', function($q) use ($customer_id){
                $q->whereIn('customer_id', $customer_id);
            });
        }

        if($request->filter_search != ""){
            $products->whereHas('product', function($q) use ($request) {
                $q->where('item_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('item_code','LIKE',"%".$request->filter_search."%");
            });
        }

        $products = $products->get();

        return DataTables::of($products)
                          ->addIndexColumn()
                          ->addColumn('item_name', function($row) {
                                $html = "";
                                $html .= '<div class="d-flex align-items-center">
                                            
                                            <div class="d-flex justify-content-start flex-column">';
                                $html .= @$row->product->item_name ?? " ";

                                $html .= '<span class="text-muted fw-bold text-muted d-block fs-7 text-center">';

                                $html .= "Code: ".$row->product->item_code;
                                return $html;
                          })
                          ->addColumn('item_code', function($row) {
                                if($row->product->quantity_on_stock - $row->product->quantity_ordered_by_customers < 1){
                                    return '<span class="" title="Not Available">'.(@$row->product->item_code ?? "").'</span>';
                                }else{
                                    return @$row->product->item_code ?? "";
                                }
                            })
                            ->addColumn('price', function($row) use ($customer_price_list_no, $customer_vat) {
                                $sap_connection_id = $row->product->sap_connection_id;
                                $currency_symbol = '';
                                foreach($customer_vat as $cust){
                                    if($sap_connection_id === $cust->real_sap_connection_id){                                       
                                        $currency_symbol = get_product_customer_currency(@$row->product->item_prices, $cust->price_list_num);
                                        $price = get_product_customer_price(@$row->product->item_prices,@$customer_price_list_no[$sap_connection_id]);
                                    }
                                }

                                if(round($row->product->quantity_on_stock - $row->product->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.$currency_symbol.' '.number_format_value($price).'</span>';
                                }else{
                                    return $currency_symbol." ".number_format_value($price);
                                }
                          })
                          ->addColumn('qty', function($row){
                            $html= '<div class="button-wrap">
                                        <div class="counter">
                                            <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyMinus">
                                                <i class="fas fa-minus"></i>
                                            </a>

                                            <input class="form-control qty text-center" type="number" min="1" value="1" id="qty_'.$row->product->id.'">

                                            <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyPlus">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>';
                            return $html;
                          })
                          ->addColumn('action', function($row) use ($sap_customer_arr) {
                            $btn = "";
                            if(userrole() == 14){
                                // if(is_in_cart(@$row->product->id, @$sap_customer_arr[@$row->product->sap_connection_id]) == 1){
                                //     $btn = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mx-2" href="'.route('recommended-products.goToCart', @$sap_customer_arr[@$row->product->sap_connection_id]).'" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                // }else{
                                //     if(@$row->product->quantity_on_stock - @$row->product->quantity_ordered_by_customers < 1){
                                //         $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-danger btn-smmx-2 " title="Not Available"><i class="fa fa-cart-arrow-down"></i></a>';
                                //     }else{
                                        $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm mx-2 addToCart" data-url="'.route('cart.add',@$row->product->id).'" title="Add to Cart"><i class="fa fa-cart-arrow-down"></i></a>';
                                //     }

                                //     $btn .= '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mx-2 goToCart" href="'.route('recommended-products.goToCart', @$sap_customer_arr[@$row->product->sap_connection_id]).'" style="display:none" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                // }
                            }
                            if(userrole() == 4){
                                // if(is_in_cart(@$row->product->id, @$sap_customer_arr[@$row->product->sap_connection_id]) == 1){
                                //     $btn = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mx-2" href="'.route('cart.index').'" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                // }else{
                                //     if(@$row->product->quantity_on_stock - @$row->product->quantity_ordered_by_customers < 1){
                                //         $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm mx-2" title="Not Available"><i class="fa fa-cart-arrow-down"></i></a>';
                                //     }else{
                                        $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm mx-2 addToCart" data-url="'.route('cart.add',@$row->product->id).'" title="Add to Cart"><i class="fa fa-cart-arrow-down"></i></a>';
                                    // }

                                //     $btn .= '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mx-2 goToCart" href="'.route('cart.index').'" style="display:none" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                // }
                            }


                            $btn .= '<a href="' . route('product-list.show',['id' => @$row->product->id, 'customer_id' => @$row->recommended->assignments->first()->customer->customer_id]). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm m-3" target="_blank">
                                    <i class="fa fa-eye"></i>
                                </a>';
                            return $btn;
                          })
                        //   ->orderColumn('item_name', function ($query, $order) {
                        //     $query->join("products",function($join){
                        //         $join->on("products.id","=","product_id");
                        //     })->orderBy('product_groups.group_name', $order);
                        //   })
                          ->rawColumns(['action','price','item_name','qty'])
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

        $customer = Customer::findOrFail($customer_id);
        $user = User::where(['role_id' => 4, 'id' => $customer->user->id])->first();
        $data = Cart::with(['product', 'customer'])->where('customer_id', $customer_id)->get();
        $address = CustomerBpAddress::where('customer_id', $customer_id)->get();
        $dates = User::whereHas('customer_delivery_schedules', function($q){
            $q->where('date','>',date("Y-m-d"));
        })->where('id', $user->id)->first();

        $dates = [];

        foreach($data as $item){
            $subTotal = get_product_customer_price(@$item->product->item_prices,@$customer->price_list_num, false, false, $item->customer) * $item->qty;
            $total += $subTotal;
        }
        // dd($data);
        return view('recommended-products.cart', compact(['data', 'address', 'total', 'user', 'dates','customer']));
    }

    public function addToCart($id, Request $request){

        $customer = Customer::findOrFail($request->customer_id);
        if(!@$customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        }

        if(isset($id)){
            $product = Product::findOrFail($id);
            // if($product->sap_conection_id != $customer->real_sap_connection_id){
            //     return $response = ['status'=>false,'message'=>"Oops! Customer or Items can not be located in the DataBase."];
            // }

            $avl_qty = $product->quantity_on_stock - $product->quantity_ordered_by_customers;
            if($avl_qty < 1){
                return $response = ['status'=>false,'message'=>"The product quantity is not available."];
            }

            $price = get_product_customer_price(@$product->item_prices,@$customer->price_list_num, false, false, $customer);
            if($price < 1){
                return $response = ['status'=>false,'message'=>"The product price is not a valid."];
            }
        }

        if(isset($id)){
            $cart = Cart::updateOrCreate(
                ['customer_id' => $request->customer_id, 'product_id' => $id]
            );
            $cart->customer_id = $request->customer_id;
            $cart->product_id = $id;
            $cart->qty = 1;
            $cart->save();

            $count = Cart::where('customer_id', $request->customer_id)->count();

            return $response = ['status'=>true,'message'=>"Product added to cart successfully.", 'count' => $count];
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
            // if($avl_qty < ($cart->qty + 1)){
            //     return $response = ['status'=>false,'message'=>"The product quantity is not available."];
            // }

            $cart->qty = $cart->qty + 1;
            $cart->save();

            $customer_id = explode(',', $cart->customer_id);

            $total = 0;
            $data = Cart::with('product')->whereIn('customer_id', $customer_id)->get();

            foreach($data as $item){
                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no, false, false, $item->customer) * $item->qty;
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

            $customer_id = explode(',', $cart->customer_id);

            $total = 0;
            $data = Cart::with('product')->whereIn('customer_id', $customer_id)->get();

            foreach($data as $item){

                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no, false, false, $item->customer) * $item->qty;
                $total += $subTotal;
            }

            return $response = ['status'=>true,'message'=> $message, 'total' => number_format_value($total), 'count' => count($data)];
        }

        return $response = ['status'=>false,'message'=>"Something went wrong !"];
    }

    public function removeFromCart($id){
        if(isset($id)){
            $cart = Cart::where('id', $id)->firstOrFail();
            $customer_id = explode(',', $cart->customer_id);
            $cart->delete();

            $total = 0;
            $data = Cart::with('product')->whereIn('customer_id', $customer_id)->get();

            foreach($data as $item){

                $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$item->product->sap_connection_id];

                $subTotal = get_product_customer_price(@$item->product->item_prices,$customer_price_list_no, false, false, $item->customer) * $item->qty;
                $total += $subTotal;
            }

            return $response = ['status'=>true,'message'=>"Product removed from cart.", 'total' => number_format_value($total), 'count' => count($data)];
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

            $products = Cart::where('customer_id', $customer_id)->get();
            if( !empty($products) ){
                foreach($products as $value){
                    $product = Product::find(@$value['product_id']);

                    $price = get_product_customer_price(@$product->item_prices, @$customer->price_list_num, false, false, $value->customer);
                    if($price < 1){
                        return $response = ['status'=>false,'message'=>'The product "'.@$product->item_name.'" price is not a valid so please remove that product from cart for further process. '];
                    }
                }
            }

            $address = CustomerBpAddress::find($address_id);

            $order = new LocalOrder();
            if(!empty($customer) && !empty($address)){
                $order->customer_id = $customer_id;
                $order->address_id = $address_id;
                $order->due_date = date('Y-m-d',strtotime($due_date));
                $order->placed_by = "C";
                $order->confirmation_status = "P";
                $order->sap_connection_id = $customer->sap_connection_id;
                $order->save();

                $total = 0;
                $is_need_delete_order = false;

                $products = Cart::where('customer_id', $customer_id)->get();
                if( !empty($products) ){
                    foreach($products as $value){
                        $productData = Product::find(@$value['product_id']);

                        $avl_qty = $productData->quantity_on_stock - $productData->quantity_ordered_by_customers;
                        if($avl_qty >= @$value['qty']){
                            $item = new LocalOrderItem();
                            $item->local_order_id = $order->id;
                            $item->product_id = @$value['product_id'];
                            $item->quantity = @$value['qty'];

                            $item->price = get_product_customer_price(@$productData->item_prices,@$order->customer->price_list_num, false, false, $order->customer);
                            $item->total = $item->price * $item->quantity;
                            $item->save();

                            $total += $item->total;
                        }else{
                            $is_need_delete_order = true;
                        }

                    }
                }

                $order->total = $total;
                $order->save();

                if($is_need_delete_order && count($products) == 1){
                    $order->delete();
                }else{
                    try{
                        $sap_connection = SapConnection::find($customer->sap_connection_id);

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
        }


        Cart::where('customer_id', $customer_id)->delete();
        
        return $response = ['status' => true, 'message' => 'Order Placed Successfully!'];
    }

    public function saveToDraft(Request $request){
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

                $total = 0;
                $is_need_delete_order = false;

                $products = Cart::where('customer_id', $customer_id)->get();
                if( !empty($products) ){
                    foreach($products as $value){
                        $productData = Product::find(@$value['product_id']);

                        $avl_qty = $productData->quantity_on_stock - $productData->quantity_ordered_by_customers;
                        if($avl_qty >= @$value['qty']){
                            $item = new LocalOrderItem();
                            $item->local_order_id = $order->id;
                            $item->product_id = @$value['product_id'];
                            $item->quantity = @$value['qty'];

                            $item->price = get_product_customer_price(@$productData->item_prices,@$order->customer->price_list_num, false, false, $order->customer);
                            $item->total = $item->price * $item->quantity;
                            $item->save();

                            $total += $item->total;
                        }else{
                            $is_need_delete_order = true;
                        }
                    }
                }

                $order->total = $total;
                $order->save();
                
                if($is_need_delete_order && count($products) == 1){
                    $order->delete();
                }
            }

            Cart::where('customer_id', $customer_id)->delete();
            $response = ['status'=>true,'message'=> 'Order saved to customer draft Successfully.'];
        }

        return $response;
    }
}
