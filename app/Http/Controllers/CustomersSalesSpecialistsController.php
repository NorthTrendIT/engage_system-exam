<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomersSalesSpecialist;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\ProductItemLine;
use App\Models\ProductGroup;
use App\Models\ProductTiresCategory;
use App\Models\CustomerProductItemLine;
use App\Models\CustomerProductGroup;
use App\Models\CustomerProductTiresCategory;
use App\Models\SapConnection;
use App\Models\CustomerBpAddress;
use App\Jobs\ImportCustomerSalesSpecialistAssign;

use App\Imports\CustomerSalesSpecialistAssignImport;
use Excel;

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
        $company = SapConnection::all();
        return view('customers-sales-specialist.index', compact('company'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = SapConnection::all();
        return view('customers-sales-specialist.add', compact('company'));
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
        
        $sap_connection_id = $input['company_id'];
        if($input['company_id'] == 5){ //Solid Trend
            $sap_connection_id = 1;
        }

        $rules = array(

                    'company_id' => 'required|exists:sap_connections,id',
                    
                    'customer_selection' => 'required',

                    'customer_group_ids' => 'required',
                    'customer_group_ids.*' => 'required|exists:customer_groups,id,sap_connection_id,'.$input['company_id'],

                    // 'customer_ids' => 'required',
                    // 'customer_ids.*' => 'required|exists:customers,id,sap_connection_id,'.$input['company_id'],

                    'ss_ids' => 'required',
                    'ss_ids.*' => 'required|exists:users,id,sap_connection_id,'.$sap_connection_id,

                    'product_group_id' => 'nullable|array',
                    'product_group_id.*' => 'exists:product_groups,id,sap_connection_id,'.$sap_connection_id,

                    'product_tires_category_id' => 'nullable|array',
                    'product_tires_category_id.*' => 'exists:product_tires_categories,id,sap_connection_id,'.$sap_connection_id,

                    'product_item_line_id' => 'nullable|array',
                    'product_item_line_id.*' => 'exists:product_item_lines,id,sap_connection_id,'.$sap_connection_id,
                );

        if(!isset($input['id']) && @$request->customer_selection == "specific"){
            $rules['customer_ids'] = 'required';
            $rules['customer_ids.*'] = 'required|exists:customers,id,sap_connection_id,'.$input['company_id'];
        }

        if(isset($input['id'])){
            unset($rules['customer_selection']);
            unset($rules['customer_group_ids']);
            unset($rules['customer_group_ids.*']);
        }

        $message = array(
                        // 'customer_id.required' => 'Please select customer.',
                        'ss_ids.required' => 'Please select sales specialists.',
                    );

        $validator = Validator::make($input, $rules, $message);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            // Create Time
            // if(!isset($input['id'])){
            //     $count = CustomersSalesSpecialist::where('customer_id', $input['customer_id'])->count();

            //     if($count > 0){
            //         return $response = ['status' => false,'message' => "The selected customer is already used."];
            //     }
            // }


            $customer_ids = [];
            if(isset($input['id'])){
                $message = "Record updated successfully.";

                $customer_ids = array( $input['id'] );
                //save log
                add_log(42, $input);
            }else{
                $message = "Record created successfully.";

                //save log
                add_log(41, $input);
                
                if(@$request->customer_selection == "specific"){
                    $customer_ids = $input['customer_ids'];
                }else{
                    $customer_ids = Customer::doesnthave('sales_specialist')
                                            ->orderby('card_name','asc')
                                            ->where('sap_connection_id',$input['company_id'])
                                            ->where('is_active',1)
                                            ->whereHas('group', function($q) use ($input){
                                                $q->whereIn('id', $input['customer_group_ids']);
                                            })->pluck('id')->toArray();

                }
            }

            foreach($customer_ids as $key => $customer){
                CustomersSalesSpecialist::where('customer_id', $customer)->delete();
                if(isset($input['ss_ids']) && !empty($input['ss_ids'])){
                    foreach($input['ss_ids'] as $value){
                        $ss = new CustomersSalesSpecialist();
                        $ss->customer_id = $customer;
                        $ss->ss_id = $value;
                        $ss->save();
                    }
                }

                CustomerProductGroup::where('customer_id', $customer)->delete();
                if(isset($input['product_group_id']) && !empty($input['product_group_id'])){
                    foreach($input['product_group_id'] as $value){
                        $ss = new CustomerProductGroup();
                        $ss->customer_id = $customer;
                        $ss->product_group_id = $value;
                        $ss->save();
                    }
                }

                CustomerProductItemLine::where('customer_id', $customer)->delete();
                if(isset($input['product_item_line_id']) && !empty($input['product_item_line_id'])){
                    foreach($input['product_item_line_id'] as $value){
                        $ss = new CustomerProductItemLine();
                        $ss->customer_id = $customer;
                        $ss->product_item_line_id = $value;
                        $ss->save();
                    }
                }

                CustomerProductTiresCategory::where('customer_id', $customer)->delete();
                if(isset($input['product_tires_category_id']) && !empty($input['product_tires_category_id'])){
                    foreach($input['product_tires_category_id'] as $value){
                        $ss = new CustomerProductTiresCategory();
                        $ss->customer_id = $customer;
                        $ss->product_tires_category_id = $value;
                        $ss->save();
                    }
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
        $company = SapConnection::where('id', $edit->sap_connection_id)->get();

        return view('customers-sales-specialist.add',compact('edit', 'company'));
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

            //save log
            add_log(43, ['id'=>$id]);

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
                                $btn = '<a href="' . route('customers-sales-specialist.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
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
                                            ->orderBy('id', 'desc');

        if($request->filter_company != ""){

            $data->whereHas('customer', function($q) use ($request){
                $q->where('sap_connection_id',$request->filter_company);
            });
        }

        $data = $data->get()->groupBy('customer_id');

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
                            ->addColumn('company', function($row) {
                                return @$row->first()->customer->sap_connection->company_name;
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('customers-sales-specialist.edit',$row->first()->customer_id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mx-2">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= '<a href="javascript:void(0)" data-url="' . route('customers-sales-specialist.destroy',$row->first()->customer_id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete mx-2">
                                    <i class="fa fa-trash"></i>
                                  </a>';
                                $btn .= '<a href="' . route('customers-sales-specialist.show',$row->first()->customer_id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }

    public function importIndex(){
        return view('customers-sales-specialist.import');
    }

    public function importStore(Request $request){
        $input = $request->all();

        $rules = array(
                    'file' => 'required|mimes:xlsx,xls,csv',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{


            $log_id = add_sap_log([
                            'ip_address' => userip(),
                            'activity_id' => 37,
                            'user_id' => userid(),
                            'data' => null,
                            'type' => "O",
                            'status' => "in progress",
                            'sap_connection_id' => null,
                        ]);

            /*Upload Image*/
            if (request()->hasFile('file')) {
                $file = $request->file('file');
                $name = date("YmdHis") . $file->getClientOriginalName();
                request()->file('file')->move(public_path() . '/sitebucket/files/', $name);
            }

            ImportCustomerSalesSpecialistAssign::dispatch($name, $log_id);

            // Excel::import(new CustomerSalesSpecialistAssignImport,$request->file);

            $response = ['status'=>true,'message'=>"Excel file uploaded successfully !"];
        }

        return $response;
    }

    public function getSalseSpecialist(Request $request){

        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;
            
            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            $data = User::where('sap_connection_id', $sap_connection_id)
                            ->where('role_id',2)
                            ->where('is_active', 1)
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

    public function getCustomers(Request $request){
        $search = $request->search;

        $response = array();
        if($request->sap_connection_id){
            $data = Customer::doesnthave('sales_specialist')->orderby('card_name','asc')->where('sap_connection_id',$request->sap_connection_id)->where('is_active',1)->limit(50);
            if($search != ''){

                $data->where(function($q) use ($search){
                    $q->orwhere('card_name', 'like', '%' .$search . '%');
                    $q->orwhere('card_code', 'like', '%' .$search . '%');

                    $q->orwherehas('user', function($q1) use ($search){
                        $q1->where('email', 'like', '%' .$search . '%');
                    });
                });
            }

            if(isset($request->group_id) && !empty($request->group_id)){
                $data->whereHas('group', function($q) use ($request){
                    $q->whereIn('id', $request->group_id);
                });
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->card_name.' (Code: '.$value->card_code. (@$value->user->email ? ', Email: '.@$value->user->email : ""). ')',
                    // "sap_connection_id" => $value->sap_connection_id
                );
            }
        }

        return response()->json($response);
    }

    public function getCustomerGroups(Request $request){
        $search = $request->search;

        $response = array();
        if($request->sap_connection_id){
            $data = CustomerGroup::orderby('name','asc')->where('sap_connection_id',$request->sap_connection_id)->limit(50);

            if($search != ''){
                $data->where('name', 'like', '%' .$search . '%');
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->name,
                );
            }
        }

        return response()->json($response);
    }

    public function getProductBrand(Request $request){

        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            $data = ProductGroup::where('sap_connection_id',$sap_connection_id)
                                ->orderby('group_name','asc')
                                ->select('id','group_name')
                                ->where('is_active', true)
                                ->limit(50);

            if($search != ''){
                $data->where('group_name', 'like', '%' .$search . '%');
            }

            $data->whereNotIn('group_name', ['Items', 'MKTG. MATERIALS', 'OFFICIAL DOCUMENT']);
            
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

            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            $data = ProductItemLine::where('sap_connection_id',$sap_connection_id)
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
                    "text" => @$value->u_item_line_sap_value->value ?? @$value->u_item_line
                );
            }
        }

        return response()->json($response);
    }

    public function getProductCategory(Request $request){

        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            $sap_connection_id = $request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            $data = ProductTiresCategory::where('sap_connection_id',$sap_connection_id)
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
