<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalOrder;
use App\Models\LocalOrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CustomerBpAddress;
use App\Support\PostOrder;
use Validator;
use Auth;
use DataTables;

class DraftOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('draft-order.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('draft-order.add');
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
        $customer_id = Auth::user()->customer_id;
        $rules = array(
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

            $customer = Customer::find($customer_id);
            $address = CustomerBpAddress::find($input['address_id']);

            if(!empty($customer) && !empty($address)){
                $order->customer_id = $customer_id;
                $order->address_id = $input['address_id'];
                $order->due_date = date('Y-m-d',strtotime($input['due_date']));
                // $order->sales_specialist_id = Auth::id();
                $order->placed_by = "S";
                $order->confirmation_status = "P";
                $order->save();

                if( isset($input['products']) && !empty($input['products']) ){
                    $products = $input['products'];
                    LocalOrderItem::where('local_order_id', $order->id)->delete();
                    foreach($products as $value){
                        // dd($value['product_id']);
                        $item = new LocalOrderItem();
                        $item->local_order_id = $order->id;
                        $item->product_id = $value['product_id'];
                        $item->quantity = $value['quantity'];
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
        $edit = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items.product'])->where('id',$id)->firstOrFail();

        return view('draft-order.view',compact('edit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $customer_id = Auth::user()->customer_id;
        $data = LocalOrder::with('sales_specialist')->where('customer_id', $customer_id);
        // dd($data->get());

        // if($request->filter_search != ""){
        //     $data->where(function($q) use ($request) {
        //         $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
        //         $q->orwhere('doc_type','LIKE',"%".$request->filter_search."%");
        //     });
        // }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addColumn('sales_specialist_name', function($row) {
                                return $row->sales_specialist->sales_specialist_name;
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
                            ->addColumn('action', function($row) {
                                $btn = ' <a href="' . route('draft-order.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }

    function getCustomers(Request $request){
        $search = $request->search;

        if($search == ''){
            $data = Customer::where(['card_type' => 'cCustomer', 'is_active' => 1])->orderby('card_name','asc')->select('id','card_name')->limit(50)->get();
        }else{
            $data = Customer::where(['card_type' => 'cCustomer', 'is_active' => 1])->orderby('card_name','asc')->select('id','card_name')->where('card_name', 'like', '%' .$search . '%')->limit(50)->get();
        }

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

        if($search == ''){
            $data = Product::where('is_active', 1)->orderby('item_name','asc')->select('id','item_name')->limit(50)->get();
        }else{
            $data = Product::where('is_active', 1)->orderby('item_name','asc')->select('id','item_name')->where('item_name', 'like', '%' .$search . '%')->limit(50)->get();
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

        $response = array();
        // if(!empty($data)){
            foreach($data as $value){
                $response[] = array(
                    "id"=>$value->id,
                    "text"=>$value->address
                );
            }
        // }

        return response()->json($response);
    }

    public function placeOrder(Request $request){
        $data = $request->all();
        $id = $data['id'];
        $obj = array();

        $update = $this->store($request);
        if($update['message'] == 'Order details updated successfully.'){
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

            $post = new PostOrder('TEST-APBW', 'manager', 'test');

            $post = $post->pushOrder($obj);

            if(isset($post) && !empty($post['error'])){
                $order = LocalOrder::where('id', $id)->first();
                $order->confirmation_status = 'ERR';
                $order->message = $post['error']['message']['value'];
                $order->save();
            } else {
                $order = LocalOrder::where('id', $id)->first();
                $order->confirmation_status = 'C';
                $order->save();
            }

            $response = ['status' => true, 'message' => 'Order Placed successfully !'];
        } catch (\Exception $e) {
            dd($e);
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }
}
