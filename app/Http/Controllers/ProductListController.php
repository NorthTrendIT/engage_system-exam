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
use App\Models\ProductGroup;
use DB;
use App\Support\SAPVatGroup;
use App\Models\SapConnection;

class ProductListController extends Controller
{
    public function index(){
        $c_product_groups = $c_product_line = $c_product_category = collect();

        $customer_id = $sap_connection_id = [];

        if(userrole() == 4){

            // $customer_id = array( @Auth::user()->customer_id );
            // $sap_connection_id = @Auth::user()->sap_connection_id;

            $customer_id = explode(',', Auth::user()->multi_customer_id);
            $sap_connection_id = explode(',', Auth::user()->multi_real_sap_connection_id);

        }elseif(userrole() == 2){

            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
            $sap_connection_id = array( @Auth::user()->sap_connection_id );

        }elseif (!is_null(@Auth::user()->created_by)) {

            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                // $customer_id = array( @$customer->customer_id );
                // $sap_connection_id = @$customer->sap_connection_id;

                $customer_id = explode(',', @$customer->multi_customer_id);
                $sap_connection_id = explode(',', @$customer->multi_real_sap_connection_id);
            }

        }

        // Is Customer
        if(!empty($customer_id)){

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



            $brand_product = Product::where('is_active', true)->whereIn('items_group_code', $product_groups);

            $brand_product->whereHas('group', function($q){
                $q->where('is_active', true);
            });   
            
            // if($sap_connection_id == 5){ //Solid Trend
            //     $sap_connection_id = 1;
            // }

            if(in_array(5, $sap_connection_id)){
                array_push($sap_connection_id, '5');
            }

            $brand_product->whereIn('sap_connection_id', $sap_connection_id);

            $c_product_line = $brand_product->groupBy('u_item_line')->get();
            

            $brand_product = $brand_product->get()->toArray();


            // $c_product_line = array_unique(
            //                             array_filter(
            //                                     array_merge($c_product_line,
            //                                         array_column($brand_product, 'u_item_line')
            //                                     )
            //                                 )
            //                         );
            // asort($c_product_line);

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
            //$customer = @Auth::user()->customer;

            $customer_id = @get_sap_customer_arr(Auth::user())[$product->sap_connection_id];
            $customer = Customer::findOrFail($customer_id);
        }elseif (!is_null(@Auth::user()->created_by)) {
            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                //$customer = @$customer->customer;

                $customer_id = @get_sap_customer_arr($customer)[$product->sap_connection_id];
                $customer = Customer::findOrFail($customer_id);
            }
        }elseif($customer_id){
            $customer = Customer::findOrFail($customer_id);
        }
      	return view('product-list.view',compact('product','customer'));
  	}


    public function getAll(Request $request){


        // $customer_id = explode(',', Auth::user()->multi_customer_id);
        // dd(get_customer_price_list_no_arr($customer_id));
        
        $c_product_tires_category = $c_product_item_line = $c_product_group = array();

        $customer_id = [];
        $customer = collect();
        $sap_connection_id = [];
        $customer_price_list_no = [];

        if(userrole() == 4){
            // $customer_id = array( @Auth::user()->customer_id );
            // $customer = @Auth::user()->customer;
            // $sap_connection_id = @Auth::user()->sap_connection_id;
            // $customer_price_list_no = @Auth::user()->customer->price_list_num;

            $customer_id = explode(',', Auth::user()->multi_customer_id); //Auth::user()->customer_id
            $sap_connection_id = explode(',', Auth::user()->multi_real_sap_connection_id);
            $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
            $customer_vat  = Customer::whereIn('id', $customer_id)->get();

        }elseif (!is_null(@Auth::user()->created_by)) {

            $customer = User::where('role_id', 4)->where('id', @Auth::user()->created_by)->first();
            if(!is_null($customer)){
                // $customer_id = array( @$customer->customer_id );
                // $customer = @$customer->customer;
                // $sap_connection_id = @$customer->sap_connection_id;
                // $customer_price_list_no = @$customer->price_list_num;

                $customer_id = explode(',', @$customer->multi_customer_id);
                $sap_connection_id = explode(',', @$customer->multi_real_sap_connection_id);
                $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
            }
        }elseif(userrole() == 2){

            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
            $sap_connection_id = array( @Auth::user()->sap_connection_id );
        }

        // if($sap_connection_id == 5){ //Solid Trend
        //     $sap_connection_id = 1;
        // }

        if(in_array(5, $sap_connection_id)){
            array_push($sap_connection_id, '5');
        }

        // Is Customer
        if(!empty($customer_id)){
            // Product Group
            $c_product_group = CustomerProductGroup::with('product_group')->whereIn('customer_id', $customer_id)->get();
            $c_product_group = array_column( $c_product_group->toArray(), 'product_group_id' );
            // $c_product_group = array_map( function ( $ar ) {
            //     return $ar['number'];
            // }, array_column( $c_product_group->toArray(), 'product_group' ) );


            // Product Item Line
            $c_product_item_line = CustomerProductItemLine::with('product_item_line')->whereIn('customer_id', $customer_id)->get();
            $c_product_item_line = array_column( $c_product_item_line->toArray(), 'product_item_line_id' );
            // $c_product_item_line = array_map( function ( $ar ) {
            //     return $ar['u_item_line'];
            // }, array_column( $c_product_item_line->toArray(), 'product_item_line' ) );


            // Product Tires Category
            $c_product_tires_category = CustomerProductTiresCategory::with('product_tires_category')->whereIn('customer_id', $customer_id)->get();
            $c_product_tires_category = array_column( $c_product_tires_category->toArray(), 'product_tires_category_id' );
            // $c_product_tires_category = array_map( function ( $ar ) {
            //     return $ar['u_tires'];
            // }, array_column( $c_product_tires_category->toArray(), 'product_tires_category' ) );
        }

        if(empty($c_product_group) && empty($c_product_tires_category) && empty($c_product_item_line)){
            $products = collect([]);
            return DataTables::of($products)->make(true);
        }

        $where = array('products.is_active' => 1);

        $products = Product::where($where)
                ->whereRaw('last_sync_at > "2023-03-27 09:39:36"');

        $products->whereHas('group', function($q){
            $q->where('is_active', true);
        });
        
        if($request->filter_search != ""){
            $products->where('item_name','LIKE',"%".$request->filter_search."%");
        }

        if($request->filter_search1 != ""){
          $products->where(function($q) use ($request) {
            $q->orwhere('products.item_code','LIKE',"%".$request->filter_search1."%");
            $q->orwhere('products.item_name','LIKE',"%".$request->filter_search1."%");
          });
        }

        $products->where(function($q) use ($request, $c_product_tires_category, $c_product_item_line, $c_product_group) {

            /*if(!empty($c_product_group)){
                $q->orWhereIn('items_group_code', $c_product_group);
            }

            if(!empty($c_product_tires_category)){
                $q->orWhereIn('u_tires', $c_product_tires_category);
            }

            if(!empty($c_product_item_line)){
                $q->orWhereIn('u_item_line', $c_product_item_line);
            }*/

            if(!empty($c_product_group)){
                $q->orwhereHas('group', function($q1) use ($c_product_group){
                    $q1->whereIn('id', $c_product_group);
                });
            }

            if(!empty($c_product_tires_category)){
                $q->orwhereHas('product_tires_category', function($q1) use ($c_product_tires_category){
                    $q1->whereIn('id', $c_product_tires_category);
                });
            }

            if(!empty($c_product_item_line)){
                $q->orwhereHas('product_item_line', function($q1) use ($c_product_item_line){
                    $q1->whereIn('id', $c_product_item_line);
                });
            }
        });

        $products->whereIn('products.sap_connection_id', $sap_connection_id);

        $products->when(!isset($request->order), function ($q) {
            $q->orderBy('item_name', 'asc');
        });

        if($request->filter_brand != ""){
            // $products->where('items_group_code', $request->filter_brand);

            $products->whereHas('group', function($q) use ($request){
                $q->where('group_name', $request->filter_brand);
            });
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
                                // if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                //     return '<span class="text-muted" title="Not Available">'.(@$row->item_name ?? "").'</span>';
                                // }else{
                                //     return @$row->item_name ?? "";
                                // }
                            $html = "";

                                $html .= '<div class="d-flex align-items-center">
                                            
                                            <div class="d-flex justify-content-start flex-column">';
                                                

                                $html .= @$row->item_name ?? " ";

                                $html .= '<span class="text-muted fw-bold text-muted d-block fs-7">';

                                $html .= "Code: ".$row->item_code;
                                return $html;
                            })
                            ->addColumn('item_code', function($row) {
                                if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.(@$row->item_code ?? "").'</span>';
                                }else{
                                    return @$row->item_code ?? "";
                                }
                            })
                            ->addColumn('brand', function($row) {
                                if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.(@$row->group->group_name ?? "").'</span>';
                                }else{
                                    return @$row->group->group_name ?? "";
                                }
                            })
                            ->addColumn('u_item_line', function($row) {
                                if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.(@$row->u_item_line_sap_value->value ?? @$row->u_item_line ?? "-").'</span>';
                                }else{
                                    return @$row->u_item_line_sap_value->value ?? @$row->u_item_line ?? "-";
                                }
                            })
                            ->addColumn('u_tires', function($row) {
                                if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">'.(@$row->u_tires ?? "").'</span>';
                                }else{
                                    return @$row->u_tires ?? "-";
                                }
                            })
                            ->addColumn('qty', function($row) {

                                if(is_in_cart1(@$row->id) == 1){
                                    $qty = is_in_cart_qty($row->id);
                                }else{
                                    $qty = '1';
                                }
                                $html= '<div class="button-wrap">
                                                            <div class="counter">
                                                                <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyMinus">
                                                                    <i class="fas fa-minus"></i>
                                                                </a>

                                                                <input class="form-control qty text-end" type="number" min="1" value="'.$qty.'" id="qty_'.$row->id.'">

                                                                <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyPlus">
                                                                    <i class="fas fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        </div>';
                                return $html;
                            })
                            ->addColumn('price', function($row) use ($customer_price_list_no, $customer_vat) {
                                
                                $sap_connection_id = $row->sap_connection_id;

                                $vat = 0;
                                foreach($customer_vat as $cust){
                                    if($sap_connection_id === $cust->real_sap_connection_id){
                                      $vat = get_vat_rate($cust);
                                    }
                                }

                                $price = get_product_customer_price(@$row->item_prices,@$customer_price_list_no[$sap_connection_id]);
                                // $customer_vat = $vat->getVat(Auth::user()->customer->vat_group);
                                if($customer_vat !== 0){
                                    $price = $price / $customer_vat;
                                }

                                if(round($row->quantity_on_stock - $row->quantity_ordered_by_customers) < 1){
                                    return '<span class="" title="Not Available">₱ '.number_format_value($price).'</span>';
                                }else{
                                    // print_r($row->item_prices);
                                    // echo "===";
                                    // print_r($customer_price_list_no);
                                    // echo "===";
                                    // print_r($sap_connection_id);
                                    // echo "===";
                                    // print_r($customer_price_list_no[$sap_connection_id]);exit();
                                    return "₱ ".number_format_value($price);

                                }
                            })
                            ->addColumn('action', function($row) {
                                $btn = "";
                                if(@Auth::user()->role_id == 4){
                                    if(is_in_cart1(@$row->id) == 1){
                                        $btn = '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" href="'.route('cart.index').'" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
                                    }else{

                                        // if($row->quantity_on_stock - $row->quantity_ordered_by_customers < 1){
                                        //     $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" title="Not Available"><i class="fa fa-cart-arrow-down"></i></a>';
                                        // }else{
                                            $btn .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm addToCart" data-url="'.route('cart.add',@$row->id).'" title="Add to Cart"><i class="fa fa-cart-arrow-down"></i></a>';
                                        //}

                                        $btn .= '<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm goToCart" href="'.route('cart.index').'" style="display:none" title="Go to cart"><i class="fa fa-shopping-cart"></i></a>';
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
                          ->rawColumns(['status','action','item_name', 'item_code','brand','u_tires','u_item_line','price','qty'])
                          ->make(true);
    }

    public function getProducts(Request $request)
    {
        $data = $this->getProductData($request);
        if(count($data['products']->get())){
            $products = $data['products']->get();
        }else{
            $products = collect();
        }

        return response()->json($products);
    }

    public function getProductData($request){
        $c_product_tires_category = $c_product_item_line = $c_product_group = array();
        $customer_id = [];
        $customer = collect();
        $sap_connection_id = [];
        $customer_price_list_no = [];

        if (!is_null(@Auth::user()->created_by) || (isset($request->customer_id) && !empty($request->customer_id)) ) {

            if(@$request->customer_id){
                $where = array(
                            'id' => @$request->customer_id,
                        );
                $customer = Customer::where($where)->first();
                $customer->sap_connection_id = (@$customer->sap_connection_id == '5') ? 1 : @$customer->sap_connection_id;
                if(!is_null($customer)){
                    $customer_id = array( @$customer->id );
                    $sap_connection_id = array( @$customer->sap_connection_id);
                    $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
                }


            }else{
                $where = array(
                            'id' => @Auth::user()->created_by,
                        );
                $customer = User::where('role_id', 4)->where($where)->first();
                if(!is_null($customer)){
                    $customer_id = explode(',', @$customer->multi_customer_id);
                    $sap_connection_id = explode(',', @$customer->multi_real_sap_connection_id);
                    $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
                }
            }


        }elseif(userrole() == 4){

            // $customer_id = array( @Auth::user()->customer_id );
            // $customer = @Auth::user()->customer;
            // $sap_connection_id = @Auth::user()->sap_connection_id;
            // $customer_price_list_no = @Auth::user()->customer->price_list_num;

            $customer_id = explode(',', Auth::user()->multi_customer_id);
            $sap_connection_id = explode(',', Auth::user()->multi_real_sap_connection_id);
            $customer_price_list_no = get_customer_price_list_no_arr($customer_id);

        }elseif(userrole() == 2){

            $customer_id = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
            $sap_connection_id = array( @Auth::user()->sap_connection_id );

        }

        // if($sap_connection_id == 5){ //Solid Trend
        //     $sap_connection_id = 1;
        // }

        if(in_array(5, $sap_connection_id)){
            array_push($sap_connection_id, '5');
        }
        
        // Is Customer
        if(!empty($customer_id)){

            // Product Group
           $user_role =  User::with(['role'])->whereHas('role', function($q){
                                    $q->where('id', Auth::user()->role_id);
                         })->first();

           if(strtolower($user_role->role->name) == "sales personnel"){
                $result = CustomersSalesSpecialist::with(['product_group.product_group'])->where(['ss_id' => userid(), 'customer_id' => $customer_id[0]])->has('product_group')->get()->toArray();
                $c_product_group = [];
                foreach($result as $data){
                    foreach($data['product_group'] as $x => $gr){
                        $c_product_group[$x] = $gr['product_group']['id']; 
                    }
                }
           }else{
                $c_product_group = CustomerProductGroup::with('product_group')->whereIn('customer_id', $customer_id)->get();
                $c_product_group = array_column( $c_product_group->toArray(), 'product_group_id' );
           }
            
            // $c_product_group = array_map( function ( $ar ) {
            //     return $ar['number'];
            // }, array_column( $c_product_group->toArray(), 'product_group' ) );


            // Product Item Line
            $c_product_item_line = CustomerProductItemLine::with('product_item_line')->whereIn('customer_id', $customer_id)->get();
            $c_product_item_line = array_column( $c_product_item_line->toArray(), 'product_item_line_id' );
            
            // $c_product_item_line = array_map( function ( $ar ) {
            //     return $ar['u_item_line'];
            // }, array_column( $c_product_item_line->toArray(), 'product_item_line' ) );


            // Product Tires Category
            $c_product_tires_category = CustomerProductTiresCategory::with('product_tires_category')->whereIn('customer_id', $customer_id)->get();
            $c_product_tires_category = array_column( $c_product_tires_category->toArray(), 'product_tires_category_id' );
            
            // $c_product_tires_category = array_map( function ( $ar ) {
            //     return $ar['u_tires'];
            // }, array_column( $c_product_tires_category->toArray(), 'product_tires_category' ) );
        }

        if(empty($c_product_group) && empty($c_product_tires_category) && empty($c_product_item_line)){
            $products = collect([]);
            return [ 'products' => $products, 'customer_price_list_no' => $customer_price_list_no];
        }

        $where = array('is_active' => true);

        $products = Product::whereRaw('last_sync_at > "2023-03-27 09:39:36"')->where($where)->limit(50);

        $products->whereHas('group', function($q){
            $q->where('is_active', true);
        });

        if($request->filter_search != ""){
            $products->where(function($q) use ($request){
                $q->where('item_name','LIKE',"%".$request->filter_search."%")
                       ->orWhere('item_code','LIKE',"%".$request->filter_search."%");
            });
            
        }

        $products->where(function($q) use ($request, $c_product_tires_category, $c_product_item_line, $c_product_group) {

            /*if(!empty($c_product_group)){
                $q->orWhereIn('items_group_code', $c_product_group);
            }

            if(!empty($c_product_tires_category)){
                $q->orWhereIn('u_tires', $c_product_tires_category);
            }

            if(!empty($c_product_item_line)){
                $q->orWhereIn('u_item_line', $c_product_item_line);
            }*/
            if(!empty($c_product_group)){
                
                $q->orwhereHas('group', function($q1) use ($c_product_group){
                    $num = ProductGroup::whereIn('id',$c_product_group)->pluck('number')->toArray();
                    $q1->whereIn('number', $num);
                });
            }

            if(!empty($c_product_tires_category)){
                $q->orwhereHas('product_tires_category', function($q1) use ($c_product_tires_category){
                    $q1->whereIn('id', $c_product_tires_category);
                });
            }

            if(!empty($c_product_item_line)){
                $q->orwhereHas('product_item_line', function($q1) use ($c_product_item_line){
                    $q1->whereIn('id', $c_product_item_line);
                });
            }
        });

        $products->whereIn('sap_connection_id', $sap_connection_id);

        $products->when(!isset($request->order), function ($q) {
          $q->orderBy('item_name', 'asc');
        });

        // if(userrole() != 1){
        //     $products->havingRaw('quantity_on_stock - quantity_ordered_by_customers > 0');
        // }

        return [ 'products' => $products, 'customer_price_list_no' => $customer_price_list_no];
    }

    public function getProductDetails(Request $request){

        
        if($request->data != ""){
            $products = Product::where('item_code','LIKE',"%".$request->data."%")
                            ->orwhere('item_name','LIKE',"%".$request->data."%")->first();
            return $response = ['status'=>true,'data'=>$products];
        }else{
            return $response = ['status'=>false,'message'=>'No data'];
        }
        
    }


    public function getCustomerProducts(Request $request)
    {
       $data =  DB::table('customer_product_groups as cu')
                    ->select('pr.id', 'pr.item_code', 'pr.item_name', 'pr.items_group_code')
                    ->join('product_groups as pg', 'cu.product_group_id', '=', 'pg.id')
                    ->join('products as pr', 'pg.number', '=', 'pr.items_group_code')
                    ->where([
                            'cu.customer_id' => $request->customer_id,
                            'pg.is_active' => '1',
                            'pr.is_active' => '1'
                    ]);

                    if($request->filter_search != ""){
                        $data->where('pr.item_code','LIKE',"%".$request->filter_search."%")
                             ->orwhere('pr.item_name','LIKE',"%".$request->filter_search."%")->first();
                    }

        return $data->get();
    }


}
