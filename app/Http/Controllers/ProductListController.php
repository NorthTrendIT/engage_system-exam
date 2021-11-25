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

            if ($request->id > 0) {
                $products = Product::where('id', '<', $request->id)->where($where)->orderBy('id', 'DESC')->limit(12)->get();
            } else {
                $products = Product::where($where)->orderBy('id', 'DESC')->limit(12)->get();
            }

            $output = "";
            $button = "";
            $last_id = "";

            $last = Product::where($where)->select('id')->first();

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

            return response()->json(['output' => $output, 'button' => $button]);
        }
  	}
}
