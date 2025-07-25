<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalOrder;
use App\Models\LocalOrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CustomerBpAddress;
use App\Models\User;
use App\Models\CustomerDeliverySchedule;
use App\Support\SAPOrderPost;
use App\Models\SapConnection;
use App\Models\Quotation;
use Validator;
use Illuminate\Support\Facades\Auth;
use DataTables;
use App\Models\TerritorySalesSpecialist;

class LocalOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('local-order.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $api_conn = SapConnection::where('id', '!=', 5)->whereNull('deleted_at')->firstOrFail();
        return view('local-order.add', compact('api_conn'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        // if(@$request->total_amount < 1){
        //     // unset($input['total_amount']);
        //     return $response = ['status'=>false,'message'=>"Oops! The amount is not valid."];
        // }

        $customer = Customer::find($input['customer_id']);
        $address = CustomerBpAddress::find($input['address_id']);

        if (!array_key_exists('products', $input)) {
            return $response = ['status'=>false,'message'=>"Oops! There's no product found."];
        }

        if(!$customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in our database."];
        }

        if($customer->vat_group === null){
            return ['status'=>false, 'message' => "VatGroup for this Customer is emtpy, please contact CMD."];  
        }

        $sap_connection_id = @$customer->sap_connection_id;
        if($sap_connection_id == 5){ //Solid Trend
            $sap_connection_id = 1;
        }

        $rules = array(
                'customer_id' => 'required',
                'address_id' => 'required|string|max:185',
                'due_date' => 'required',
                'products.*.product_id' =>'required_without:promos.*.product_id|exists:products,id,sap_connection_id,'.$sap_connection_id.' |nullable',
                'products.*.quantity' => 'required_with:products.*.product_id|min:1|nullable',
                'promos.*.product_id' => 'nullable|exists:products,id,sap_connection_id,'.$sap_connection_id,
                'promos.*.quantity' => 'required_with:promos.*.product_id',
                'promos.*.promo_remarks' => 'required_with:promos.*.product_id',
                'remark' => 'nullable|max:254'
            );

        $messages = array(
                'products.*.product_id.required' => "Oops! Product items cannot be duplicated.",
                'products.*.product_id.exists' => "Product cannot be located in the DataBase.",
                // 'products.*.product_id.distinct' => "Oops! Product items cannot be duplicated.",
                'products.*.product_id.required_without' => "Product is required.",
                'promos.*.product_id.exists' => "Product cannot be located in the DataBase.",
                // 'promos.*.product_id.distinct' => "Oops! Promo items cannot be duplicated.",
                'promos.*.quantity.required_with' => "Promo item quantity is required.",
                'promos.*.promo_remarks.required_with' => "Promo remarks is required.",
                'customer_id.exists' => "Oops! Customer not found.",
            );

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if( isset($input['products']) && !empty($input['products']) && $input['products'][0]['product_id'] !== null){
                foreach($input['products'] as $value){
                    if($value['product_id'] !== null){
                    $product = Product::find(@$value['product_id']);
                    
                    // $avl_qty = $product->quantity_on_stock - $product->quantity_ordered_by_customers;
                    // if($avl_qty == 0){
                    //     return $response = ['status'=>false, 'message'=> 'The product "'.$product->item_name.'" quantity is not available at the moment please remove from order.'];
                    // }else if($avl_qty < @$value['quantity']){
                    //     return $response = ['status'=>false, 'message'=> 'The product "'.$product->item_name.'" quantity value must be less then '.$avl_qty.'.'];
                    // }

                    $price = get_product_customer_price(@$product->item_prices, @$customer->price_list_num, false, false, $customer);
                    if($price <= 0 && $product->items_group_code != 107){ //exept for mktg. items.
                        return $response = ['status'=>false,'message'=>'The product "'.@$product->item_name.'" price is not a valid so please remove that product from cart for further process. '];
                    }
                }
                }
            }

            // if( isset($input['promos']) && !empty($input['promos']) && $input['promos'][0]['product_id'] !== null ){
            //     foreach($input['promos'] as $value){
            //         $product = Product::find(@$value['product_id']);
                    
            //         $avl_qty = $product->quantity_on_stock - $product->quantity_ordered_by_customers;
            //         // if($avl_qty == 0){
            //         //     return $response = ['status'=>false, 'message'=> 'The product "'.$product->item_name.'" quantity is not available at the moment please remove from order.'];
            //         // }else if($avl_qty < @$value['quantity']){
            //         //     return $response = ['status'=>false, 'message'=> 'The product "'.$product->item_name.'" quantity value must be less then '.$avl_qty.'.'];
            //         // }

            //         $price = get_product_customer_price(@$product->item_prices, @$customer->price_list_num, false, false, $customer);
            //         if($price <= 0 && $product->items_group_code != 107){ //exept for mktg. items.
            //             return $response = ['status'=>false,'message'=>'The product "'.@$product->item_name.'" price is not a valid so please remove that product from cart for further process. '];
            //         }
            //     }
            // }

            if(isset($input['id'])){
                $order = LocalOrder::find($input['id']);
                $message = "Order details updated successfully.";
            }else{
                $order = new LocalOrder();
                $message = "Order created successfully.";
            }

            $due_date = strtr($input['due_date'], '/', '-');
            $due_date_new = \Carbon\Carbon::createFromFormat('m-d-Y', $due_date)->format('Y-m-d');
            if(!empty($customer) && !empty($address)){
                $customer = Customer::findOrFail($input['customer_id']);
                $order->customer_id = $input['customer_id'];
                $order->address_id = $input['address_id'];
                $order->due_date = date('Y-m-d',strtotime($due_date_new));
                $order->sales_specialist_id = Auth::id();
                $order->placed_by = "S";
                $order->confirmation_status = "P";
                $order->sap_connection_id = $customer->sap_connection_id;
                $order->total = $input['total_amount'];
                $order->remarks = $input['remark'];
                $order->approval = 'Pending';
                $order->save();

                $total = 0;
                if( isset($input['products']) && !empty($input['products']) || isset($input['promos']) && !empty($input['promos']) ){
                    $products = $input['products'];
                    LocalOrderItem::where('local_order_id', $order->id)->delete();
                    foreach($products as $value){
                        if($value['product_id'] !== null){
                        $item = new LocalOrderItem();
                        $item->local_order_id = $order->id;
                        $item->product_id = @$value['product_id'];
                        $item->quantity = @$value['quantity'];

                        $productData = Product::find(@$value['product_id']);
                        $item->price = get_product_customer_price(@$productData->item_prices,@$order->customer->price_list_num, false, false, $order->customer);
                        $item->total = $item->price * $item->quantity;
                        $item->type =  'product';
                        $item->save();
                        $total += $item->total;
                        }
                    }

                    if( isset($input['promos']) && !empty($input['promos']) && $input['promos'][0]['product_id'] !== null ){
                        $products = $input['promos'];
                        // LocalOrderItem::where('local_order_id', $order->id)->delete();
                        foreach($products as $value){
                            if($value['product_id'] !== null){
                            $item = new LocalOrderItem();
                            $item->local_order_id = $order->id;
                            $item->product_id = @$value['product_id'];
                            $item->quantity = @$value['quantity'];
    
                            $productData = Product::find(@$value['product_id']);
                            $item->price = get_product_customer_price(@$productData->item_prices,@$order->customer->price_list_num, false, false, $order->customer);
                            $item->total = $item->price * $item->quantity;
                            $item->type =  'promo';
                            $item->line_remarks = @$value['promo_remarks'];
                            $item->save();
                            $total += $item->total;
                            }
                        }
                    }
                }
                $order->total = $total;
                $order->save();

                $response = ['status'=>true,'message'=>$message, 'id' => $order->id];
            } else {
                $message = "Something went wrong! Please try again later.";
                $response = ['status'=>false,'message'=>$message];
            }

        }

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $local_order = LocalOrder::where('id',$id)->firstOrFail();
        if(empty($local_order->doc_entry)){
            $total = 0;
            $data = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items.product'])->where('id', $id)->firstOrFail();
            return view('local-order.pending_order_view', compact('data', 'total'));
        } else {

            $data = $local_order->quotation;
            // if(userrole() == 4){
            //     $data->where('card_code', @Auth::user()->customer->card_code);
            // }elseif(userrole() == 14){
            //     $data->where('sales_person_code', @Auth::user()->sales_employee_code);
            // }elseif(userrole() != 1){
            //     return abort(404);
            // }           

            return view('local-order.view', compact('data','local_order'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items.product'])->doesntHave('quotation')->where('id',$id)->firstOrFail();

        return view('local-order.add',compact(['edit']));
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
        $ss_id = @Auth::user()->id;
        $data = LocalOrder::where('id', $id)->where('sales_specialist_id', $ss_id)->firstOrFail();
        $response = ['status' => false, 'message' => 'You are not authorize to delete this order!'];
        if($data->confirmation_status == 'ERR' && !$data->quotation && $data->sales_specialist_id === $ss_id){
            $data->items()->delete();
            $data->delete();
            $response = ['status' => true, 'message' => 'Order has been successfully deleted.'];
        }

        return $response;
    }

    public function getAll(Request $request){
        $ss_id = @Auth::user()->id;
        $data = LocalOrder::where('sales_specialist_id', $ss_id);

        $status = @$request->filter_status;
        if(!empty($status) && $status[0] !== null){
            $data->whereHas('quotation', function($query) use ($status){
                $selected_status = [];

                if($status === "PN"){
                    array_push($selected_status, 'Pending');
                }
                if($status === "OP"){
                    array_push($selected_status, 'On Process');
                }
                if($status === "CL"){
                    array_push($selected_status, 'Cancelled');
                }

                if($status === "CM"){
                    array_push($selected_status, 'Completed');
                }

                if($status === "PS"){
                    array_push($selected_status, 'Partially Served');
                }

                $query->whereIn('status', $selected_status);

                if($status === "DL"){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', 'DL');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }

                if($status === "IN"){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', 'IN');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }
                if($status === "CF"){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', 'CF');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }
                if($status === "FD"){
                    $query->orWhere(function($q){
                        $q->whereHas('order.invoice',function($q1){
                            $q1->where('cancelled', 'No')->where('u_sostat', '!=','CM')->where('u_sostat', '!=','DL');
                        })->where('cancelled','No');

                        $q->whereHas('order',function($q1){
                            $q1->where('cancelled','No');
                        });
                    });
                }
                
            });
        }

        if($request->filter_search != ""){
            $data->whereHas('customer', function($q) use ($request) {
                $q->where('card_name','LIKE',"%".$request->filter_search."%");
            });
        }

        if($request->filter_confirmation_status != ""){
            $data->where('confirmation_status', $request->filter_confirmation_status);
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('created_at', '>=' , $start);
            $data->whereDate('created_at', '<=' , $end);
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('customer_name', function($row) {
                            return $row->customer->card_name;
                        })
                        ->addColumn('confirmation_status', function($row) {
                            if($row->confirmation_status == 'P'){
                                return getOrderStatusBtnHtml("Pending");
                            }
                            if($row->confirmation_status == 'C'){
                                return getOrderStatusBtnHtml("Confirmed");
                            }
                            if($row->confirmation_status == 'ERR'){
                                return getOrderStatusBtnHtml("Error");
                            }
                        })
                        ->addColumn('order_status', function($row) {
                            if(!empty($row->doc_entry)){
                                return getOrderStatusBtnHtml(@$row->quotation->status);
                            } else {
                                return "<b>-</b>";
                            }
                        })
                        ->addColumn('total', function($row) {
                            $amount = @$row->quotation->doc_total ?? @$row->total ?? 0.00;
                            return '<b>₱ '. number_format_value($amount).'</b>';
                        })
                        ->addColumn('total_ltr', function($row) {
                            return @$row->items->sum('quantity');
                        })
                        ->addColumn('due_date', function($row) {
                            return date('M d, Y',strtotime($row->due_date));
                        })
                        ->addColumn('created_at', function($row) {
                            return date('M d, Y',strtotime($row->created_at));
                        })

                        ->orderColumn('created_at', function ($query, $order) {
                            $query->orderBy('created_at', $order);
                        })
                        ->orderColumn('due_date', function ($query, $order) {
                            $query->orderBy('due_date', $order);
                        })

                        ->orderColumn('total', function ($query, $order) {
                            $query->orderBy('total', $order);
                        })
                        ->orderColumn('confirmation_status', function ($query, $order) {
                            $query->orderBy('confirmation_status', $order);
                        })
                        ->addColumn('action', function($row) {
                            // $status = '';
                            // if(!empty(@$row->quotation) && $row->quotation->cancelled == 'Yes'){
                            //     $status = getOrderStatusArray('CL');
                            // }elseif(!empty(@$row->quotation->order)){
                            //     if($row->quotation->order->u_omsno != ""){
                            //         $status = getOrderStatusArray("OP");
                            //     }
                            // }else{
                            //     $status = getOrderStatusArray("PN");
                            // }
                            $btn = '<a href="' . route('sales-specialist-orders.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                    <i class="fa fa-eye"></i>
                                </a>';

                            if( (!$row->quotation && $row->sales_specialist_id === Auth::user()->id) ){
                                $btn .= '<a href="' . route('sales-specialist-orders.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                    <i class="fa fa-pencil"></i>
                                    </a>';
                            }
                            if($row->confirmation_status == 'ERR' && !$row->quotation && $row->sales_specialist_id === Auth::user()->id){
                                $btn .= '<a href="#" load-url="' . route('sales-specialist-orders.delete',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10 deleteOrder">
                                    <i class="fa fa-trash"></i>
                                    </a>';
                            }
                            return $btn;
                        })
                        ->rawColumns(['action', 'confirmation_status', 'total', 'order_status','total_ltr'])
                        ->make(true);
    }

    function getCustomers(Request $request){
        $search = $request->search;
        $territory = TerritorySalesSpecialist::where('user_id', userid())->with('territory:id,territory_id')->get();
        $sapConnections = TerritorySalesSpecialist::where('user_id', userid())->groupBy('sap_connection_id')->pluck('sap_connection_id')->toArray();
        $territoryIds= [];
        foreach($territory as $id){
            $territoryIds[] = $id->territory->territory_id;
        }

        $territoryIds = (@$territoryIds)? $territoryIds : [-3];
        $sapConnections = (@$sapConnections)? $sapConnections : [-3];
        $data = Customer::select('id', 'card_code', 'card_name', 'sap_connection_id')
                        ->where('is_active', true)
                        ->whereIn('real_sap_connection_id', $sapConnections)
                        ->whereIn('territory', $territoryIds);

                        // ->whereHas('sales_specialist', function($q){
                        //     $q->where('ss_id', @Auth::user()->id);
                        // });

        if($search != ''){
            $data = $data->where('card_name', 'like', '%' .$search . '%');
        }

        $data = $data->orderby('card_name','asc')->limit(50)->get();

        // dd($data[0]->sap_connection->company_name);

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->card_name. ' -'.$value->card_code.' ('.@$value->sap_connection->company_name.')'
            );
        }

        return response()->json($response);
    }

    public function getProducts(Request $request)
    {
        $products = collect();
        if(isset($request->customer_id))
        {
            // $products = app(ProductListController::class)->getCustomerProducts($request);
            $data = app(ProductListController::class)->getProductData($request);
            $products = $data['products'];
            if($products->count() > 0){
                if(isset($request->product_ids) && count($request->product_ids)){
                    $products->whereNotIn('id', $request->product_ids);
                }

                $products = $products->get();
            }else{
                $products = collect();
            }
        }
        /*echo "<pre>";
        print_r($products);exit();*/
        return $products;
    }

    function getAddress(Request $request){
        $customer_id = $request->customer_id;

        if(!empty($customer_id)){
            $data = CustomerBpAddress::where('customer_id', '=', $customer_id)->orderby('id','asc')->get();
        }

        // street, zip, city,country, state
        $response = array();
        foreach($data as $value){
            $address = '';
            if(!empty($value->street)){
                $address .= $value->street;
            }
            if(!empty($value->zip_code)){
                $address .= ', '.$value->zip_code;
            }
            if(!empty($value->city)){
                $address .= ', '.$value->city;
            }
            if(!empty($value->state)){
                $address .= ', '.$value->state;
            }
            if(!empty($value->country)){
                $address .= ', '.$value->country;
            }
            $response[] = array(
                "id"=>$value->id,
                "text"=>$address,
            );
        }

        return response()->json($response);
    }

    public function placeOrder(Request $request){
        $data = $request->all();
        $obj = array();
        $response = null;

        $update = $this->store($request);

        if($update['status']){
            $order = LocalOrder::where('id', $update['id'])->with(['sales_specialist', 'customer', 'address', 'items.product'])->first();
            
            try{
                $sap_connection = SapConnection::find(@$order->customer->sap_connection_id);                
                if(!is_null($sap_connection)){                   
                    $sap = new SAPOrderPost($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $sap_connection->id);
                    if($update['id']){                        
                        $sap_response = $sap->pushOrder($order->id);
                        if($sap_response['status']){
                            $response = ['status' => true, 'message' => 'Order placed successfully!'];
                        }
                        $response = ['status' => true, 'message' => 'Order placed successfully!'];
                    }
                }
            } catch (\Exception $e) {
                report($e);
                $response = ['status' => false, 'message' => 'Something went wrong !'];
            }

        } else {
            return $update;
        }
        return $response;
    }

    function getPrice(Request $request){
        $input = $request->all();
        if($input['customer_id'] && $input['product_id']){
            $customer = Customer::findOrFail($input['customer_id']);
            $product = Product::findOrFail($input['product_id']);
            
            $currency_symbol = get_product_customer_currency($product->item_prices, $customer->price_list_num);
            $price = get_product_customer_price(@$product->item_prices, @$customer->price_list_num);

            return $response = ['status' => true, 'price' => $price, 'currency_symbol' => $currency_symbol];
        }
        return $response = ['status' => false, 'message' => "Something went wrong!"];
    }

    function getCustomerSchedule(Request $request){
        $customer_id = $request->customer_id;

        $customer = Customer::where('id', $customer_id)->first();
        $dates = CustomerDeliverySchedule::where('user_id', @$customer->user->id)->where('date','>',date("Y-m-d"))->get();

        if(count($dates)){
            $dates = array_map( function ( $t ) {
                    return date('d/m/Y',strtotime($t));
                }, array_column( $dates->toArray(), 'date' ) );

            return $response = ['status' => true, 'dates' => json_encode($dates)];
        }
        return $response = ['status' => false, 'dates' => []];
    }
}
