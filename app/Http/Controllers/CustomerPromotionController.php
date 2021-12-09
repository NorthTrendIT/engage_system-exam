<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Promotions;
use App\Models\PromotionTypes;
use App\Models\PromotionTypeProduct;
use App\Models\PromotionInterest;
use App\Models\Product;
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

                    if(!is_null($product)){
                        $output .= view('customer-promotion.ajax.product',compact('product','promotion_type_product','promotion_id'))->render();
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

            $response = ['status'=>true,'message'=>$message];
        }

        return $response;
    }

    public function productDetail($id,$promotion_id){
        $data = PromotionTypeProduct::where('id',$id)->firstOrFail();
        
        $product = $data->product;

        $promotion = Promotions::findOrFail($promotion_id);

        return view('customer-promotion.product-view',compact('product','data','promotion'));
    }

    public function orderIndex($id){

        $promotion = Promotions::findOrFail($id);

        return view('customer-promotion.order',compact('promotion'));
    }
}
