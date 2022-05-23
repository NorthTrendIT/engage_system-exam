<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalOrder;
use App\Models\LocalOrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CustomerBpAddress;
use App\Models\SapConnection;
use App\Models\Quotation;
use App\Support\SAPOrderPost;
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

        $customer = Customer::find($input['customer_id']);
        if(!$customer->sap_connection_id){
            return $response = ['status'=>false,'message'=>"Oops! Customer not found in our database."];
        }
        
        $customer_id = $customer->id;
        $sap_connection_id = $customer->sap_connection_id;
        if($sap_connection_id == 5){ //Solid Trend
            $sap_connection_id = 1;
        }

        $rules = array(
                'address_id' => 'required|string|max:185',
                'due_date' => 'required',
                'products.*.product_id' => 'distinct|exists:products,id,sap_connection_id,'.$sap_connection_id,
            );

        $messages = array(
                'products.*.product_id.exists' => "Oops! Customer or Items can not be located in the DataBase.",
            );

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            // dd($input);
            if(isset($input['id'])){
                $order = LocalOrder::find($input['id']);
                $message = "Order details updated successfully.";
            }else{
                $order = new LocalOrder();
                $message = "Order created successfully.";
            }

            $address = CustomerBpAddress::find($input['address_id']);

            if(!empty($customer) && !empty($address)){
                $due_date = strtr($input['due_date'], '/', '-');
                $order->customer_id = $customer_id;
                $order->address_id = $input['address_id'];
                $order->due_date = date('Y-m-d',strtotime($due_date));
                $order->placed_by = "S";
                $order->confirmation_status = "P";
                $order->sap_connection_id = $customer->sap_connection_id;
                $order->save();

                $total = 0;
                if( isset($input['products']) && !empty($input['products']) ){
                    $products = $input['products'];
                    LocalOrderItem::where('local_order_id', $order->id)->delete();
                    foreach($products as $value){
                        $item = new LocalOrderItem();
                        $item->local_order_id = $order->id;
                        $item->product_id = $value['product_id'];
                        $item->quantity = $value['quantity'];

                        $productData = Product::find(@$value['product_id']);
                        $item->price = get_product_customer_price(@$productData->item_prices,@$order->customer->price_list_num);
                        $item->total = $item->price * $item->quantity;
                        $item->save();
                        $total += $item->total;
                    }
                }
                $order->total = $total;
                $order->save();
            } else {
                $message = "Something went wrong!";
            }

        }

        return $response = ['status'=>true,'message'=>$message];
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
            return view('draft-order.pending_order_view', compact('data', 'total'));
        } else {
            if(userrole() == 4){
                $customers = Auth::user()->get_multi_customer_details();
                $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
            }elseif(userrole() == 2){
                $data->where('sales_person_code', @Auth::user()->sales_employee_code);
            }elseif(!is_null(Auth::user()->created_by)){
                $customers = Auth::user()->created_by_user->get_multi_customer_details();
                $data->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                $data->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
            }elseif(userrole() != 1){
                return abort(404);
            }
        

            $data = $data->firstOrFail();

            return view('draft-order.view', compact('data'));
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
        $total = 0;
        $edit = LocalOrder::with(['sales_specialist', 'customer', 'address', 'items.product'])->where('id',$id)->firstOrFail();
        $customer = Customer::findOrFail($edit->customer->id);
        $customer_price_list_no = @$customer->price_list_num;

        foreach($edit->items as $value){
            $total += get_product_customer_price(@$value->product->item_prices, $customer_price_list_no) * $value->quantity;
        }
        return view('draft-order.add',compact(['edit', 'customer_price_list_no', 'total']));
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
        $customer_id = explode(',', @Auth::user()->multi_customer_id);

        $data = LocalOrder::whereIn('customer_id', $customer_id);

        if($request->filter_search != ""){
            $data->whereHas('sales_specialist', function($q) use ($request) {
                $q->where('sales_specialist_name','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('sales_specialist_name', function($row) {
                                if(!empty($row->sales_specialist_id)){
                                    return $row->sales_specialist->sales_specialist_name ?? '-';
                                } else {
                                    return "Self";
                                }
                            })
                            ->addColumn('confirmation_status', function($row) {
                                if($row->confirmation_status == 'P' || $row->confirmation_status == 'ERR'){
                                    return getOrderStatusBtnHtml("Pending");
                                }
                                if($row->confirmation_status == 'C'){
                                    return getOrderStatusBtnHtml("Confirmed");
                                }
                            })
                            ->addColumn('order_status', function($row) {
                                if(!empty($row->doc_entry)){
                                    return getOrderStatusBtnHtml(getOrderStatusByQuotation(@$row->quotation));
                                } else {
                                    return "<b>-</b>";
                                }
                            })
                            ->addColumn('due_date', function($row) {
                                return date('M d, Y',strtotime($row->due_date));
                            })
                            ->addColumn('total', function($row) {
                                return "<b>₱ ".number_format_value($row->items->sum('total'))."</b>";
                            })
                            ->orderColumn('due_date', function ($query, $order) {
                                $query->orderBy('due_date', $order);
                            })
                            ->orderColumn('confirmation_status', function ($query, $order) {
                                $query->orderBy('confirmation_status', $order);
                            })
                            ->addColumn('action', function($row) {
                                $btn = ' <a href="' . route('draft-order.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                </a>';

                                if($row->confirmation_status == "P" && empty($row->doc_entry)){
                                    $btn .= ' <a href="' . route('draft-order.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                        <i class="fa fa-pen"></i>
                                    </a>';
                                }

                                return $btn;
                            })
                            ->rawColumns(['action', 'total', 'confirmation_status', 'order_status'])
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
    public function getProducts_OLD(Request $request){
        $search = $request->search;

        $sap_connection_id = @Auth::user()->customer->sap_connection_id;
        if($sap_connection_id == 5){ //Solid Trend
            $sap_connection_id = 1;
        }

        if($search == ''){
            $data = Product::where(['is_active' => 1, 'sap_connection_id' => $sap_connection_id])->orderby('item_name','asc')->select('id','item_name')->limit(50);
        }else{
            $data = Product::where(['is_active' => 1, 'sap_connection_id' => $sap_connection_id])->orderby('item_name','asc')->select('id','item_name')->where('item_name', 'like', '%' .$search . '%')->limit(50);
        }

        $data->whereHas('group', function($q){
            $q->where('is_active', true);
        });

        $data = $data->get();

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->item_name
            );
        }

        return response()->json($response);
    }

    public function getAddress(Request $request){
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

    public function getPrice(Request $request){
        $input = $request->all();
        if($input['product_id']){
            $product = Product::findOrFail($input['product_id']);

            $customer_id = explode(',', @Auth::user()->multi_customer_id);
            $customer_price_list_no = get_customer_price_list_no_arr($customer_id);

            $price = get_product_customer_price(@$product->item_prices, @$customer_price_list_no[@$product->sap_connection_id]);
            return $response = ['status' => true, 'price' => $price];
        }
        return $response = ['status' => false, 'message' => "Something went wrong!"];
    }

    public function placeOrder(Request $request){
        $data = $request->all();
        $id = $data['id'];
        $obj = array();

        $update = $this->store($request);
        if($update['status']){
            $order = LocalOrder::where('id', $id)->with(['sales_specialist', 'customer', 'address', 'items.product'])->first();

            try{
                $sap_connection = SapConnection::find($order->customer->sap_connection_id);

                if(!is_null($sap_connection)){
                    $sap = new SAPOrderPost($sap_connection->db_name, $sap_connection->user_name , $sap_connection->password, $sap_connection->id);

                    if($id){
                        $sap->pushOrder($id);
                    }
                }
                $response = ['status' => true, 'message' => 'Order Placed Successfuly'];
            } catch (\Exception $e) {
                // dd($e);
                $response = ['status' => false, 'message' => 'Order Placed Successfuly'];
            }
        } else {
            return $update;
        }

        return $response;
    }
}
