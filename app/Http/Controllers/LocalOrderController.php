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
use App\Support\PostOrder;
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
        if(!@Auth::user()->customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in DataBase."];
        }

        $input = $request->all();
        // dd($input);
        $rules = array(
                'customer_id' => 'required',
                'address_id' => 'required|string|max:185',
                'due_date' => 'required|date',
            );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            // dd($input);
            if(isset($input['id'])){
                $order = LocalOrder::find($input['id']);
                $message = "Order details updated successfully.";
            }else{
                $order = new LocalOrder();
                $message = "Order created successfully.";
            }

            $customer = Customer::find($input['customer_id']);
            $address = CustomerBpAddress::find($input['address_id']);

            if(!empty($customer) && !empty($address)){
                $order->customer_id = $input['customer_id'];
                $order->address_id = $input['address_id'];
                $order->due_date = date('Y-m-d',strtotime($input['due_date']));
                $order->sales_specialist_id = Auth::id();
                $order->placed_by = "S";
                $order->confirmation_status = "P";
                $order->save();

                if( isset($input['products']) && !empty($input['products']) ){
                    $products = $input['products'];
                    LocalOrderItem::where('local_order_id', $order->id)->delete();
                    foreach($products as $value){
                        // dd($value);
                        $item = new LocalOrderItem();
                        $item->local_order_id = $order->id;
                        $item->product_id = @$value['product_id'];
                        $item->quantity = @$value['quantity'];
                        $item->save();
                    }
                }
            } else {
                $message = "Something went wrong! Please try again later.";
            }

            return $response = ['status'=>true,'message'=>$message];
        }
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
        $total = 0;
        $edit = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items.product'])->where('id',$id)->firstOrFail();
        $customer = Customer::findOrFail($edit->customer->id);
        $customer_price_list_no = @$customer->price_list_num;

        foreach($edit->items as $value){
            $total += get_product_customer_price(@$value->product->item_prices, $customer_price_list_no) * $value->quantity;
        }
        return view('local-order.add',compact(['edit', 'customer_price_list_no', 'total']));
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
        // dd($data);

        $data->whereHas(
            'customer', function($q){
                $q->whereHas('sales_specialist', function ($query) use ($q){
                    $query->where('ss_id', @Auth::user()->id);
                });
            }
        );

        // dd($data->get());

        if($request->filter_search != ""){
            $data->whereHas('customer', function($q) use ($request) {
                $q->where('card_name','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                        ->addColumn('customer_name', function($row) {
                            return $row->customer->card_name;
                        })
                        ->addColumn('confirmation_status', function($row) {
                            if($row->confirmation_status == 'P'){
                                return "Pending";
                            }
                            if($row->confirmation_status == 'C'){
                                return "Confirm";
                            }
                        })
                        ->addColumn('due_date', function($row) {
                            return date('M d, Y',strtotime($row->due_date));
                        })
                        ->orderColumn('due_date', function ($query, $order) {
                            $query->orderBy('due_date', $order);
                        })
                        ->orderColumn('confirmation_status', function ($query, $order) {
                            $query->orderBy('confirmation_status', $order);
                        })
                        ->addColumn('action', function($row) {
                            $btn = '<a href="' . route('sales-specialist-orders.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                <i class="fa fa-pencil"></i>
                                </a>';

                            return $btn;
                        })
                        ->rawColumns(['action'])
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

    function getProducts(Request $request){
        $search = $request->search;
        if(isset($request->customer_id)){
            $customer_id = $request->customer_id;
            $customer = Customer::findOrFail($customer_id);
            if($search == ''){
                $data = Product::where('is_active', 1)->orderby('item_name','asc')->select('id','item_name')->limit(50)->get();
            }else{
                $data = Product::where('is_active', 1)->orderby('item_name','asc')->select('id','item_name')->where('item_name', 'like', '%' .$search . '%')->limit(50)->get();
            }
        } else {
            $data = Product::where('is_active', 1)->orderby('item_name','asc')->select('id','item_name')->limit(50)->get();
        }

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->item_name
            );
        }

        return response()->json($response);
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
        $id = $data['id'];
        $obj = array();

        $update = $this->store($request);
        if($update['status']){
            $order = LocalOrder::where('id', $id)->with(['sales_specialist', 'customer', 'address', 'items.product'])->first();

            $obj['CardCode'] = $order->customer->card_code;
            $obj['DocDueDate'] = $order->due_date;

            $products = array();
            foreach($order->items as $item){
                $products[] = array(
                    'ItemCode' => $item->product->item_code,
                    'Quantity' => $item->quantity,
                    'TaxCode' => $order->address->tax_code,
                    'UnitPrice' => '30',
                );

            }
            $obj['DocumentLines'] = $products;

            $address = array();
            $address['ShipToStreet'] = $order->address->street;
            $address['ShipToZipCode'] = $order->address->zip_code;
            $address['ShipToCity'] = $order->address->city;
            $address['ShipToCountry'] = $order->address->country;
            $address['ShipToState'] = $order->address->state;
            $address['BillToAddressType'] = $order->address->address_type;

            $obj['AddressExtension'] = $address;
        }
        try {
            $message = "";
            $post = new PostOrder('TEST-APBW', 'manager', 'test');

            $post = $post->pushOrder($obj);

            $order = LocalOrder::where('id', $order->id)->first();
            if($post['status']){
                $order->confirmation_status = 'C';
                $message = 'Order Placed successfully !';
            } else {
                $order->confirmation_status = 'ERR';
                $order->message = $post['message'];
                $message = $post['massage'];
            }
            $order->save();

            $response = ['status' => true, 'message' => $message];
        } catch (\Exception $e) {
            dd($e);
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    function getPrice(Request $request){
        $input = $request->all();
        if($input['customer_id'] && $input['product_id']){
            $customer = Customer::findOrFail($input['customer_id']);
            // dd($customer);
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
