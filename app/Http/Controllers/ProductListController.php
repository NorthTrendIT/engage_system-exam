<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductListController extends Controller
{
    public function index(){
      	return view('product-list.index');
  	}

  	public function show($id){
  		$product = Product::where('is_active',true)->where('id',$id)->firstOrFail();
  		
      	return view('product-list.view',compact('product'));
  	}


  	public function getAll(Request $request){
  		if ($request->ajax()) {
            
            $where = array('is_active' => true);

            $products = Product::where($where)->orderBy('id', 'DESC')->limit(12);
            if ($request->id > 0) {
                $products->where('id', '<', $request->id);
            }

            if($request->filter_search != ""){
                $products->where(function($q) use ($request) {
                    $q->orwhere('item_name','LIKE',"%".$request->filter_search."%");
                });
            }

            $products = $products->get();

            $output = "";
            $button = "";
            $last_id = "";

            $last = Product::where($where)->select('id');
            if($request->filter_search != ""){
                $last->where(function($q) use ($request) {
                    $q->orwhere('item_name','LIKE',"%".$request->filter_search."%");
                });
            }

            $last = $last->first();

            if (!$products->isEmpty()) {

                foreach ($products as $product) {
                    $output .= view('product-list.ajax.product',compact('product'))->render();
                }

                $last_id = $products->last()->id;

                if ($last_id != $last->id) {
                    $button = '<a href="javascript:" class="btn btn-primary" data-id="' . $last_id . '" id="view_more_btn">View More Products</a>';
                }

            } else {

                $button = '';

            }

            if($output == $button){
                $output = "<div class='text-center mt-5'><h2>Result Not Found !</h2></div>";
            }

            return response()->json(['output' => $output, 'button' => $button]);
        }
  	}
}
