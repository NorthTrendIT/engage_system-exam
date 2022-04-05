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
use Auth;
use DataTables;

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
        return view('local-order.add');
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

        if(@$request->total_amount < 1){
            unset($input['total_amount']);
            return $response = ['status'=>false,'message'=>"Oops! The amount is not valid."];
        }
        
        $customer = Customer::find($input['customer_id']);
        $address = CustomerBpAddress::find($input['address_id']);

        if(!$customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in our database."];
        }

        $sap_connection_id = @$customer->sap_connection_id;
        if($sap_connection_id == 5){ //Solid Trend
            $sap_connection_id = 1;
        }

        $rules = array(
                'customer_id' => 'required',
                'address_id' => 'required|string|max:185',
                'due_date' => 'required',
                'products.*.product_id' => 'distinct|exists:products,id,sap_connection_id,'.$sap_connection_id,
            );

        $messages = array(
                'products.*.product_id.exists' => "Oops! Customer or Items can not be located in the DataBase.",
                'customer_id.exists' => "Oops! Customer not found.",
            );

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){
                $order = LocalOrder::find($input['id']);
                $message = "Order details updated successfully.";
            }else{
                $order = new LocalOrder();
                $message = "Order created successfully.";
            }

            $due_date = strtr($input['due_date'], '/', '-');

            if(!empty($customer) && !empty($address)){
                $customer = Customer::findOrFail($input['customer_id']);
                $order->customer_id = $input['customer_id'];
                $order->address_id = $input['address_id'];
                $order->due_date = date('Y-m-d',strtotime($due_date));
                $order->sales_specialist_id = Auth::id();
                $order->placed_by = "S";
                $order->confirmation_status = "P";
                $order->sap_connection_id = $customer->sap_connection_id;
                $order->save();

                $total = 0;
                if( isset($input['products']) && !empty($input['products']) ){
                    $products = $input['products'];
                    LocalOrderItem::where('local_order_id', $order->id)->delete();
                    foreach($products as $value){
                        // dd($value);
                        $item = new LocalOrderItem();
                        $item->local_order_id = $order->id;
                        $item->product_id = @$value['product_id'];
                        $item->quantity = @$value['quantity'];

                        $productData = Product::find(@$value['product_id']);
                        $item->price = get_product_customer_price(@$productData->item_prices,@$order->customer->price_list_num);
                        $item->total = $item->price * $item->quantity;
                        $item->save();
                        $total += $item->total;
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
        // dd($local_order);
        if(empty($local_order->doc_entry)){
            $total = 0;
            $data = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items.product'])->where('id', $id)->firstOrFail();
            return view('local-order.pending_order_view', compact('data', 'total'));
        } else {
            // dd($id);
            $data = Quotation::with(['items.product', 'customer'])->where('doc_entry', $local_order->doc_entry);
            // if(userrole() == 4){
            //     $data->where('card_code', @Auth::user()->customer->card_code);
            // }elseif(userrole() == 2){
            //     $data->where('sales_person_code', @Auth::user()->sales_employee_code);
            // }elseif(userrole() != 1){
            //     return abort(404);
            // }
            $data = $data->firstOrFail();

            return view('local-order.view', compact('data'));
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
        $edit = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items.product'])->where('id',$id)->firstOrFail();

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
        //
    }

    public function getAll(Request $request){

        $data = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items']);

        $data->whereHas(
            'customer', function($q){
                $q->whereHas('sales_specialist', function ($query) use ($q){
                    $query->where('ss_id', @Auth::user()->id);
                });
            }
        );

        if($request->filter_search != ""){
            $data->whereHas('customer', function($q) use ($request) {
                $q->where('card_name','LIKE',"%".$request->filter_search."%");
            });
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
                                return getOrderStatusBtnHtml(getOrderStatusByQuotation(@$row->quotation));
                            } else {
                                return "<b>-</b>";
                            }
                        })
                        ->addColumn('total', function($row) {
                            return '<b>₱ '. number_format_value($row->total).'</b>';
                        })
                        ->addColumn('due_date', function($row) {
                            return date('M d, Y',strtotime($row->due_date));
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
                            $btn = '<a href="' . route('sales-specialist-orders.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                    <i class="fa fa-eye"></i>
                                </a>';

                            if($row->confirmation_status == 'P' && empty($row->doc_entry)){
                                $btn .= '<a href="' . route('sales-specialist-orders.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                    </a>';
                            }
                            return $btn;
                        })
                        ->rawColumns(['action', 'confirmation_status', 'total', 'order_status'])
                        ->make(true);
    }

    function getCustomers(Request $request){
        $search = $request->search;
        // return @Auth::user()->id;
        $data = Customer::select('id','card_name')->whereHas('sales_specialist', function($q){
            $q->where('ss_id', @Auth::user()->id);
        });

        // dd($data);

        if($search != ''){
            $data = $data->where('card_name', 'like', '%' .$search . '%');
        }

        $data = $data->orderby('card_name','asc')->limit(50)->get();

        // dd($data);

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->card_name
            );
        }

        return response()->json($response);
    }

    public function getProducts(Request $request)
    {
        $data = app(ProductListController::class)->getProductData($request);
        $products = $data['products'];

        if(isset($data['products'])){
            if(isset($request->product_ids) && count($request->product_ids)){
                $products->whereNotIn('id', $request->product_ids);
            }

            $products = $products->get();
        }else{
            $products = collect();
        }

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
            $address = $value->address;
            if(!empty($value->street)){
                $address .= ', '.$value->street;
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
                    }
                }
            } catch (\Exception $e) {
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
            $price = get_product_customer_price(@$product->item_prices, @$customer->price_list_num);
            return $response = ['status' => true, 'price' => $price];
        }
        return $response = ['status' => false, 'message' => "Something went wrong!"];
    }

    function getCustomerSchedule(Request $request){
        $customer_id = $request->customer_id;

        $user = User::where('customer_id', $customer_id)->first();
        $dates = CustomerDeliverySchedule::where('user_id', $user->id)->where('date','>',date("Y-m-d"))->get();
        // dd($dates);

        if(count($dates)){
            $dates = array_map( function ( $t ) {
                    return date('d/m/Y',strtotime($t));
                }, array_column( $dates->toArray(), 'date' ) );

            return $response = ['status' => true, 'dates' => json_encode($dates)];
        }
        return $response = ['status' => false, 'dates' => []];
    }
}
