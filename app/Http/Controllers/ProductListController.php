<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CustomerProductGroup;
use App\Models\CustomerProductItemLine;
use App\Models\CustomerProductTiresCategory;
use App\Models\User;
use Auth;

class ProductListController extends Controller
{
    public function index(){
      	return view('product-list.index');
  	}

  	public function show($id){
  		$product = Product::where('is_active',1)->where('id',$id)->firstOrFail();

        $customer = collect();
  		if(userrole() == 4){
            $customer = @Auth::user()->customer;
        }elseif (!is_null(@Auth::user()->created_by)) {
            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                $customer = @$customer->customer;
            }
        }

      	return view('product-list.view',compact('product','customer'));
  	}


  	public function getAll(Request $request){
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
}
