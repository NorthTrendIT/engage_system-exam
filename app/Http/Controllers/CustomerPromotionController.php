<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Promotions;
use App\Models\PromotionTypes;
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

            $last = Promotions::where($where)->where('promotion_start_date','<=',$now)->where('promotion_end_date','>=',$now)->select('id')->first();

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
}
