<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomersSalesSpecialist;
use App\Models\User;
use App\Models\Customer;
use App\Models\ProductItemLine;
use App\Models\ProductGroup;
use App\Models\ProductTiresCategory;
use App\Models\CustomerProductItemLine;
use App\Models\CustomerProductGroup;
use App\Models\CustomerProductTiresCategory;
use Validator;
use DataTables;

class CustomersSalesSpecialistsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('customers-sales-specialist.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers-sales-specialist.add');
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

        $rules = array(
                    'customer_id' => 'required|exists:customers,id',
                    
                    'ss_ids' => 'required',
                    'ss_ids.*' => 'required|exists:users,id',

                    'product_group_id' => 'nullable|array',
                    'product_group_id.*' => 'exists:product_groups,id',

                    'product_tires_category_id' => 'nullable|array',
                    'product_tires_category_id.*' => 'exists:product_tires_categories,id',

                    'product_item_line_id' => 'nullable|array',
                    'product_item_line_id.*' => 'exists:product_item_lines,id',
                );

        $message = array(
                        'customer_id.required' => 'Please select customer.',
                        'ss_ids.required' => 'Please select sales specialists.',
                    );

        $validator = Validator::make($input, $rules, $message);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            // Create Time
            if(!isset($input['id'])){
                $count = CustomersSalesSpecialist::where('customer_id', $input['customer_id'])->count();

                if($count > 0){
                    return $response = ['status' => false,'message' => "The selected customer is already used."];
                }
            }


            if(isset($input['id'])){
                $message = "Details updated successfully.";
            }else{
                $message = "Created successfully.";
            }

            CustomersSalesSpecialist::where('customer_id', $input['customer_id'])->delete();
            if(isset($input['ss_ids']) && !empty($input['ss_ids'])){
                foreach($input['ss_ids'] as $value){
                    $ss = new CustomersSalesSpecialist();
                    $ss->customer_id = $input['customer_id'];
                    $ss->ss_id = $value;
                    $ss->save();
                }
            }

            CustomerProductGroup::where('customer_id', $input['customer_id'])->delete();
            if(isset($input['product_group_id']) && !empty($input['product_group_id'])){
                foreach($input['product_group_id'] as $value){
                    $ss = new CustomerProductGroup();
                    $ss->customer_id = $input['customer_id'];
                    $ss->product_group_id = $value;
                    $ss->save();
                }
            }

            CustomerProductItemLine::where('customer_id', $input['customer_id'])->delete();
            if(isset($input['product_item_line_id']) && !empty($input['product_item_line_id'])){
                foreach($input['product_item_line_id'] as $value){
                    $ss = new CustomerProductItemLine();
                    $ss->customer_id = $input['customer_id'];
                    $ss->product_item_line_id = $value;
                    $ss->save();
                }
            }

            CustomerProductTiresCategory::where('customer_id', $input['customer_id'])->delete();
            if(isset($input['product_tires_category_id']) && !empty($input['product_tires_category_id'])){
                foreach($input['product_tires_category_id'] as $value){
                    $ss = new CustomerProductTiresCategory();
                    $ss->customer_id = $input['customer_id'];
                    $ss->product_tires_category_id = $value;
                    $ss->save();
                }
            }

            $response = ['status'=>true,'message'=>$message];
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
        $data = Customer::has('sales_specialist')->findOrFail($id);

        return view('customers-sales-specialist.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = Customer::has('sales_specialist')->findOrFail($id);

        return view('customers-sales-specialist.add',compact('edit'));
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
        $data = CustomersSalesSpecialist::where('customer_id', $id)->count();
        if($data > 0){
            CustomersSalesSpecialist::where('customer_id', $id)->delete();
            CustomerProductGroup::where('customer_id', $id)->delete();
            CustomerProductItemLine::where('customer_id', $id)->delete();
            CustomerProductTiresCategory::where('customer_id', $id)->delete();

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll1(Request $request){

        $data = Customer::whereHas('sales_specialist');

        if($request->filter_search != ""){
            $data->with('sales_specialist.sales_person')->where(function($q) use ($request) {
                $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
            });
        } else {
            $data->with('sales_specialist.sales_person');
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('card_name', 'ASC');
        });

        return DataTables::of($data)
                            ->addColumn('customer', function($row) {
                                return $row->card_name;
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('customers-sales-specialist.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('customers-sales-specialist.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }


    public function getAll(Request $request){

        $data = CustomersSalesSpecialist::with(['customer','sales_person'])
                                            ->has('customer')
                                            ->has('sales_person')
                                            ->select('ss_id','customer_id')
                                            ->orderBy('id', 'desc')
                                            ->get()
                                            ->groupBy('customer_id');


        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('sales_specialist', function($row) {

                                $descriptions = array_map( function ( $t ) {
                                               return $t['sales_specialist_name'];
                                            }, array_column( $row->toArray(), 'sales_person' ) );

                                return implode(" | ", $descriptions);
                            })
                            ->addColumn('customer', function($row) {
                                return @$row->first()->customer->card_name ?? "-";
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('customers-sales-specialist.edit',$row->first()->customer_id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';

                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('customers-sales-specialist.destroy',$row->first()->customer_id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                $btn .= ' <a href="' . route('customers-sales-specialist.show',$row->first()->customer_id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }


    public function getSalseSpecialist(Request $request){

        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = User::where('sap_connection_id',$request->sap_connection_id)
                            ->where('role_id',2)
                            ->where('is_active',true)
                            ->orderby('sales_specialist_name','asc')
                            ->select('id','sales_specialist_name')
                            ->limit(50);

            if($search != ''){
                $data->where('sales_specialist_name', 'like', '%' .$search . '%');
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id"=>$value->id,
                    "text"=>$value->sales_specialist_name
                );
            }
        }

        return response()->json($response);
    }

    function getCustomers(Request $request){
        $search = $request->search;

        if($search == ''){
            $data = Customer::orderby('card_name','asc')->limit(50)->get();
        }else{
            $data = Customer::orderby('card_name','asc')->where('card_name', 'like', '%' .$search . '%')->limit(50)->get();
        }

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id" => $value->id,
                "text" => $value->card_name,
                "sap_connection_id" => $value->sap_connection_id
            );
        }

        return response()->json($response);
    }


    public function getProductBrand(Request $request){

        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = ProductGroup::where('sap_connection_id',$request->sap_connection_id)
                                ->orderby('group_name','asc')
                                ->select('id','group_name')
                                ->limit(50);

            if($search != ''){
                $data->where('group_name', 'like', '%' .$search . '%');
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->group_name
                );
            }
        }

        return response()->json($response);
    }

    public function getProductLine(Request $request){

        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = ProductItemLine::where('sap_connection_id',$request->sap_connection_id)
                                ->orderby('u_item_line','asc')
                                ->select('id','u_item_line')
                                ->limit(50);

            if($search != ''){
                $data->where('u_item_line', 'like', '%' .$search . '%');
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->u_item_line
                );
            }
        }

        return response()->json($response);
    }

    public function getProductCategory(Request $request){

        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $data = ProductTiresCategory::where('sap_connection_id',$request->sap_connection_id)
                                        ->orderby('u_tires','asc')
                                        ->select('id','u_tires')
                                        ->limit(50);

            if($search != ''){
                $data->where('u_tires', 'like', '%' .$search . '%');
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->u_tires
                );
            }
        }

        return response()->json($response);
    }
}
