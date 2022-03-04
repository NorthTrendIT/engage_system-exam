<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CustomerProductGroup;
use App\Models\CustomerProductItemLine;
use App\Models\CustomerProductTiresCategory;
use App\Models\CustomersSalesSpecialist;
use App\Models\User;
use App\Models\Customer;
use App\Models\LocalOrderItem;
use Auth;
use DataTables;

class ProductListController extends Controller
{
    public function index(){

        $c_product_groups = $c_product_line = $c_product_category = collect();

        $customer_id = null;

        if(userrole() == 4){

            $customer_id = array( @Auth::user()->customer_id );

        }elseif(userrole() == 2){

            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();

        }elseif (!is_null(@Auth::user()->created_by)) {

            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                $customer_id = array( @$customer->customer_id );
            }
        }

        // Is Customer
        if($customer_id){

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



            $brand_product = Product::where('is_active', true)->whereIn('items_group_code', $product_groups)->get()->toArray();
            $c_product_line = array_unique(
                                        array_filter(
                                                array_merge($c_product_line,
                                                    array_column($brand_product, 'u_item_line')
                                                )
                                            )
                                    );
            asort($c_product_line);

            $c_product_category = array_unique(
                                        array_filter(
                                                array_merge($c_product_category,
                                                    array_column($brand_product, 'u_tires')
                                                )
                                            )
                                    );
            asort($c_product_category);

        }

      	return view('product-list.index',compact('c_product_groups','c_product_line','c_product_category'));
  	}

  	public function show($id, $customer_id = false){
  		$product = Product::where('is_active',1)->where('id',$id)->firstOrFail();

        $customer = null;
  		if(userrole() == 4){
            $customer = @Auth::user()->customer;
        }elseif (!is_null(@Auth::user()->created_by)) {
            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                $customer = @$customer->customer;
            }
        }elseif($customer_id){
            $customer = Customer::findOrFail($customer_id);
        }
      	return view('product-list.view',compact('product','customer'));
  	}


  	public function _getAll(Request $request){
  		if ($request->ajax()) {

            $c_product_tires_category = $c_product_item_line = $c_product_group = array();

            $output = "<div class='text-center mt-5'><h2>Result Not Found !</h2></div>";
            $button = "";
            $last_id = "";

            $where = array('is_active' => 1);

            $products = Product::where($where)->orderBy('id', 'DESC')->limit(12);
            if ($request->id > 0) {
                $products->where('id', '<', $request->id);
            }

            if($request->filter_search != ""){
                $products->where(function($q) use ($request) {
                    $q->orwhere('item_name','LIKE',"%".$request->filter_search."%");
                });
            }


            $customer_id = null;
            $customer = collect();
            $sap_connection_id = null;

            if(userrole() == 4){
                $customer_id = @Auth::user()->customer_id;
                $customer = @Auth::user()->customer;
                $sap_connection_id = @Auth::user()->sap_connection_id;
            }elseif (!is_null(@Auth::user()->created_by)) {
                $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
                if(!is_null($customer)){
                    $customer_id = @$customer->customer_id;
                    $customer = @$customer->customer;
                    $sap_connection_id = @$customer->sap_connection_id;
                }
            }

            // Is Customer
            if($customer_id){

                // Product Group
                $c_product_group = CustomerProductGroup::with('product_group')->where('customer_id', $customer_id)->get();

                $c_product_group = array_map( function ( $ar ) {
                   return $ar['number'];
                }, array_column( $c_product_group->toArray(), 'product_group' ) );


                // Product Item Line
                $c_product_item_line = CustomerProductItemLine::with('product_item_line')->where('customer_id', $customer_id)->get();

                $c_product_item_line = array_map( function ( $ar ) {
                   return $ar['u_item_line'];
                }, array_column( $c_product_item_line->toArray(), 'product_item_line' ) );


                // Product Tires Category
                $c_product_tires_category = CustomerProductTiresCategory::with('product_tires_category')->where('customer_id', $customer_id)->get();

                $c_product_tires_category = array_map( function ( $ar ) {
                   return $ar['u_tires'];
                }, array_column( $c_product_tires_category->toArray(), 'product_tires_category' ) );
            }


            if($customer_id && empty($c_product_group) && empty($c_product_tires_category) && empty($c_product_item_line)){
                return response()->json(['output' => $output, 'button' => $button]);
            }


            $products->where(function($q) use ($request, $c_product_tires_category, $c_product_item_line, $c_product_group) {

                if(!empty($c_product_group)){
                    $q->orWhereIn('items_group_code', $c_product_group);
                }

                if(!empty($c_product_tires_category)){
                    $q->orWhereIn('u_tires', $c_product_tires_category);
                }

                if(!empty($c_product_item_line)){
                    $q->orWhereIn('u_item_line', $c_product_item_line);
                }
            });

            $products->where('sap_connection_id', $sap_connection_id);

            $products = $products->get();

            $last = Product::where($where)->select('id');
            if($request->filter_search != ""){
                $last->where(function($q) use ($request) {
                    $q->orwhere('item_name','LIKE',"%".$request->filter_search."%");
                });
            }

            $last->where(function($q) use ($request, $c_product_tires_category, $c_product_item_line, $c_product_group) {

                if(!empty($c_product_group)){
                    $q->orWhereIn('items_group_code', $c_product_group);
                }

                if(!empty($c_product_tires_category)){
                    $q->orWhereIn('u_tires', $c_product_tires_category);
                }

                if(!empty($c_product_item_line)){
                    $q->orWhereIn('u_item_line', $c_product_item_line);
                }
            });

            $last->where('sap_connection_id', $sap_connection_id);

            $last = $last->first();

            if (!$products->isEmpty()) {
                $output = "";
                foreach ($products as $product) {
                    // $output .= view('product-list.ajax.product',compact('product','customer'))->render();
                    $output .= view('product-list.ajax.product-list-view',compact('product','customer'))->render();
                }

                $last_id = $products->last()->id;

                if ($last_id != $last->id) {
                    $button = '<a href="javascript:" class="btn btn-primary" data-id="' . $last_id . '" id="view_more_btn">View More Products</a>';
                }

            } else {

                $button = '';

            }

            // if($output == $button){
            //     $output = "<div class='text-center mt-5'><h2>Result Not Found !</h2></div>";
            // }


            return response()->json(['output' => $output, 'button' => $button]);
        }
  	}

    public function getAll(Request $request){
        $c_product_tires_category = $c_product_item_line = $c_product_group = array();

        $where = array('is_active' => 1);

        $products = Product::where($where);

        if($request->filter_search != ""){
            $products->where(function($q) use ($request) {
                $q->orwhere('item_name','LIKE',"%".$request->filter_search."%");
            });
        }

        $customer_id = null;
        $customer = collect();
        $sap_connection_id = null;
        $customer_price_list_no = null;

        if(userrole() == 4){

            $customer_id = array( @Auth::user()->customer_id );
            $customer = @Auth::user()->customer;
            $sap_connection_id = @Auth::user()->sap_connection_id;
            $customer_price_list_no = @Auth::user()->customer->price_list_num;

        }elseif (!is_null(@Auth::user()->created_by)) {

            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                $customer_id = array( @$customer->customer_id );
                $customer = @$customer->customer;
                $sap_connection_id = @$customer->sap_connection_id;
                $customer_price_list_no = @$customer->price_list_num;
            }
        }elseif(userrole() == 2){

            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
            $sap_connection_id = @Auth::user()->sap_connection_id;

        }

        // Is Customer
        if($customer_id){

            // Product Group
            $c_product_group = CustomerProductGroup::with('product_group')->whereIn('customer_id', $customer_id)->get();

            $c_product_group = array_map( function ( $ar ) {
                return $ar['number'];
            }, array_column( $c_product_group->toArray(), 'product_group' ) );


            // Product Item Line
            $c_product_item_line = CustomerProductItemLine::with('product_item_line')->whereIn('customer_id', $customer_id)->get();

            $c_product_item_line = array_map( function ( $ar ) {
                return $ar['u_item_line'];
            }, array_column( $c_product_item_line->toArray(), 'product_item_line' ) );


            // Product Tires Category
            $c_product_tires_category = CustomerProductTiresCategory::with('product_tires_category')->whereIn('customer_id', $customer_id)->get();

            $c_product_tires_category = array_map( function ( $ar ) {
                return $ar['u_tires'];
            }, array_column( $c_product_tires_category->toArray(), 'product_tires_category' ) );
        }

        $products->where(function($q) use ($request, $c_product_tires_category, $c_product_item_line, $c_product_group) {

            if(!empty($c_product_group)){
                $q->orWhereIn('items_group_code', $c_product_group);
            }

            if(!empty($c_product_tires_category)){
                $q->orWhereIn('u_tires', $c_product_tires_category);
            }

            if(!empty($c_product_item_line)){
                $q->orWhereIn('u_item_line', $c_product_item_line);
            }
        });

        $products->where('products.sap_connection_id', $sap_connection_id);

        if($customer_id && empty($c_product_group) && empty($c_product_tires_category) && empty($c_product_item_line)){
            $products = collect([]);
            return DataTables::of($products)->make(true);
        }

        $products->when(!isset($request->order), function ($q) {
          $q->orderBy('item_name', 'asc');
        });

        if($request->filter_brand != ""){
          $products->where('items_group_code',$request->filter_brand);
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
                              return @$row->item_name ?? "";
                          })
                          ->addColumn('item_code', function($row) {
                              return @$row->item_code ?? "";
                          })
                          ->addColumn('brand', function($row) {
                              return @$row->group->group_name ?? "";
                          })
                          ->addColumn('u_item_line', function($row) {
                              return @$row->u_item_line ?? "-";
                          })
                          ->addColumn('u_tires', function($row) {
                              return @$row->u_tires ?? "-";
                          })
                          ->addColumn('price', function($row) use ($customer_price_list_no) {
                              return "₱ ".number_format(get_product_customer_price(@$row->item_prices,$customer_price_list_no),2);
                          })
                          ->addColumn('action', function($row) {
                            $btn = null;
                            if(@Auth::user()->role_id == 4){
                                if(is_in_cart(@$row->id) == 1){
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
                              // $query->join('product_groups', 'products.items_group_code', '=', 'product_groups.number')
                              //       ->orderBy('product_groups.group_name', $order);

                              $query->join("product_groups",function($join){
                                  $join->on("products.items_group_code","=","product_groups.number")
                                      ->on("products.sap_connection_id","=","product_groups.sap_connection_id");
                              })->orderBy('product_groups.group_name', $order);
                          })
                          ->rawColumns(['status','action'])
                          ->make(true);
    }

    public function getProducts(Request $request)
    {
        $data = $this->getProductData($request);
        $products = $data['products']->get();

        return response()->json($products);
    }

    public function getProductData($request){
        $c_product_tires_category = $c_product_item_line = $c_product_group = array();

        $where = array('is_active' => true);

        $products = Product::where($where)->limit(50);

        if($request->filter_search != ""){
            $products->where(function($q) use ($request) {
                $q->orwhere('item_name','LIKE',"%".$request->filter_search."%");
            });
        }

        $customer_id = null;
        $customer = collect();
        $sap_connection_id = null;
        $customer_price_list_no = null;

        if(userrole() == 4){

            $customer_id = array( @Auth::user()->customer_id );
            $customer = @Auth::user()->customer;
            $sap_connection_id = @Auth::user()->sap_connection_id;
            $customer_price_list_no = @Auth::user()->customer->price_list_num;

        }elseif (!is_null(@Auth::user()->created_by) || isset($request->customer_id)) {

            if(@$request->customer_id){
                $where = array(
                            'customer_id' => @$request->customer_id,
                        );
            }else{
                $where = array(
                            'id' => @Auth::user()->created_by,
                        );
            }

            $customer = User::where('role_id', 4)->where($where)->first();
            if(!is_null($customer)){
                $customer_id = array( @$customer->customer_id );
                $customer = @$customer->customer;
                $sap_connection_id = @$customer->sap_connection_id;
                $customer_price_list_no = @$customer->price_list_num;
            }

        }elseif(userrole() == 2){

            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
            $sap_connection_id = @Auth::user()->sap_connection_id;

        }

        // Is Customer
        if($customer_id){

            // Product Group
            $c_product_group = CustomerProductGroup::with('product_group')->whereIn('customer_id', $customer_id)->get();

            $c_product_group = array_map( function ( $ar ) {
                return $ar['number'];
            }, array_column( $c_product_group->toArray(), 'product_group' ) );


            // Product Item Line
            $c_product_item_line = CustomerProductItemLine::with('product_item_line')->whereIn('customer_id', $customer_id)->get();

            $c_product_item_line = array_map( function ( $ar ) {
                return $ar['u_item_line'];
            }, array_column( $c_product_item_line->toArray(), 'product_item_line' ) );


            // Product Tires Category
            $c_product_tires_category = CustomerProductTiresCategory::with('product_tires_category')->whereIn('customer_id', $customer_id)->get();

            $c_product_tires_category = array_map( function ( $ar ) {
                return $ar['u_tires'];
            }, array_column( $c_product_tires_category->toArray(), 'product_tires_category' ) );
        }

        $products->where(function($q) use ($request, $c_product_tires_category, $c_product_item_line, $c_product_group) {

            if(!empty($c_product_group)){
                $q->orWhereIn('items_group_code', $c_product_group);
            }

            if(!empty($c_product_tires_category)){
                $q->orWhereIn('u_tires', $c_product_tires_category);
            }

            if(!empty($c_product_item_line)){
                $q->orWhereIn('u_item_line', $c_product_item_line);
            }
        });

        $products->where('sap_connection_id', $sap_connection_id);

        if($customer_id && empty($c_product_group) && empty($c_product_tires_category) && empty($c_product_item_line)){
            $products = collect([]);
        }else{
            $products->when(!isset($request->order), function ($q) {
              $q->orderBy('item_name', 'asc');
            });
        }


        return [ 'products' => $products, 'customer_price_list_no' => $customer_price_list_no];
    }
}
