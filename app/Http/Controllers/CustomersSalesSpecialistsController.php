<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomersSalesSpecialist;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Territory;
use App\Models\ProductItemLine;
use App\Models\ProductGroup;
use App\Models\ProductTiresCategory;
use App\Models\CustomerProductItemLine;
use App\Models\CustomerProductGroup;
use App\Models\CustomerProductTiresCategory;
use App\Models\SapConnection;
use App\Models\CustomerBpAddress;
use App\Jobs\ImportCustomerSalesSpecialistAssign;
use App\Models\TerritorySalesSpecialist;

use App\Imports\CustomerSalesSpecialistAssignImport;
use Excel;

use Validator;
use DataTables;
use App\Models\salesAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                    'assignment_name' => 'required',
                    'company_id' => 'required|exists:sap_connections,id',
                    
                    // 'customer_selection' => 'required',

                    'customer_territory_ids' => 'required',
                    // 'customer_group_ids' => 'required',
                    // 'customer_group_ids.*' => 'required|exists:customer_groups,id,sap_connection_id,'.$input['company_id'],

                    // 'customer_ids' => 'required',
                    // 'customer_ids.*' => 'required|exists:customers,id,sap_connection_id,'.$input['company_id'],

                    'ss_ids' => 'required',
                    'ss_ids.*' => 'required|exists:users,id',

                    'product_group_id' => 'nullable|array',
                    'product_group_id.*' => 'exists:product_groups,id,sap_connection_id,'.$sap_connection_id,

                    'product_tires_category_id' => 'nullable|array',
                    'product_tires_category_id.*' => 'exists:product_tires_categories,id,sap_connection_id,'.$sap_connection_id,

                    'product_item_line_id' => 'nullable|array',
                    'product_item_line_id.*' => 'exists:product_item_lines,id,sap_connection_id,'.$sap_connection_id,
                );

        if(!isset($input['id']) && @$request->customer_selection == "specific"){
            $rules['customer_ids'] = 'required';
            // $rules['customer_ids.*'] = 'required|exists:customers,id,sap_connection_id,'.$input['company_id'];
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

            $customer_ids = [];
            if(isset($input['id'])){
                $assignment = salesAssignment::find($input['id']);
                $assignment->assignment_name =  $input['assignment_name'];
                $assignment->brand_ids = $request->product_group_id; 
                $assignment->line_ids = $request->product_item_line_id;     
                $assignment->category_ids = $request->product_tires_category_id;
                $assignment->save();
                // $extra_ids = CustomersSalesSpecialist::where('assignment_id',$assignment->id)->groupBy('customer_id')->pluck('customer_id')->toArray();
                // $customer_ids = $input['customer_ids'];

                $customer_ids = Customer::orderby('card_name','asc')
                                            ->where('real_sap_connection_id',$input['company_id'])
                                            ->where('is_active',1)
                                            ->whereHas('territories', function($q) use ($input){
                                                $q->whereIn('id', $input['customer_territory_ids']);
                                            })->pluck('id')->toArray();

                #

                $message = "Record updated successfully.";

                //$customer_ids = array( $input['id'] );
                //save log
                add_log(42, $input);
            }
            else{                
                //============= override previous code =======================
                
                $message = "Record created successfully.";

                //save log
                add_log(41, $input);

                $assignment = new salesAssignment();
                $assignment->assignment_name = $request->assignment_name;
                $assignment->brand_ids = $request->product_group_id; 
                $assignment->line_ids = $request->product_item_line_id;     
                $assignment->category_ids = $request->product_tires_category_id;
                $assignment->save();
                
                // if(@$request->customer_selection == "specific"){
                //     $customer_ids = $input['customer_ids'];
                //     $extra_ids = $customer_ids;
                // }else{
                    // $customer_ids = Customer::doesnthave('sales_specialist')
                    //                         ->orderby('card_name','asc')
                    //                         ->where('real_sap_connection_id',$input['company_id'])
                    //                         //->where('is_active',1)
                    //                         ->whereHas('group', function($q) use ($input){
                    //                             $q->whereIn('id', $input['customer_group_ids']);
                    //                         })->pluck('id')->toArray();

                    // if(isset($input['customer_ids'])){
                    //     $extra_ids = $input['customer_ids'];
                    // }else{
                    //     $extra_ids = $customer_ids;
                    // }

                // }

                $customer_ids = Customer::orderby('card_name','asc')
                                            ->where('real_sap_connection_id',$input['company_id'])
                                            ->where('is_active',1)
                                            ->whereHas('territories', function($q) use ($input){
                                                $q->whereIn('id', $input['customer_territory_ids']);
                                            })->pluck('id')->toArray();
            }

            if(isset($input['ss_ids']) && !empty($input['ss_ids'])){
                foreach ($input['ss_ids'] as $ss) {
                    $user = User::find($ss);  // Find the user by ID
                    $user->territories()->detach();  // Detach all existing territories first
                
                    // Prepare pivot data for each territory
                    $pivotData = [];
                    foreach ($input['customer_territory_ids'] as $tr) {
                        // Each territory is attached with the pivot data
                        $pivotData[$tr] = [
                            'assignment_id' => $assignment->id,
                            'sap_connection_id' => $input['company_id'],
                        ];
                    }
             
                    // Attach the new territories with the pivot data
                    $user->territories()->attach($pivotData);
                }
            }

            CustomersSalesSpecialist::where('assignment_id', $assignment->id)->delete();
            CustomerProductGroup::where('assignment_id', $assignment->id)->delete();
            CustomerProductItemLine::where('assignment_id', $assignment->id)->delete();
            CustomerProductTiresCategory::where('assignment_id', $assignment->id)->delete();
            //print_r($customer_ids);exit();

            $dataToInsertSalesSpecialist = [];
            $dataToInsertProductGroup = [];
            $dataToInsertItemLine = [];
            $dataToInsertTiresCategory = [];
            foreach($customer_ids as $key => $customer){
                $inputData = [
                                'assignment_id' => $assignment->id,
                                'customer_id' => $customer,
                             ];
                $inputDataTiresCategory = $inputDataItemLine = $inputDataProductGroup = $inputDataSalesSpecialist = $inputData;

                if(isset($input['ss_ids']) && !empty($input['ss_ids'])){
                    foreach($input['ss_ids'] as $value){
                        // $ss = new CustomersSalesSpecialist();
                        // $ss->assignment_id = $assignment->id;
                        // $ss->customer_id = $customer;
                        // $ss->ss_id = $value;
                        // $ss->save();
                        $inputDataSalesSpecialist['ss_id'] = $value;
                        $dataToInsertSalesSpecialist[] = $inputDataSalesSpecialist;
                    }
                }

                if(isset($input['product_group_id']) && !empty($input['product_group_id'])){
                    foreach($input['product_group_id'] as $value){
                        // $ss = new CustomerProductGroup();
                        // $ss->customer_id = $customer;
                        // $ss->assignment_id = $assignment->id;
                        // $ss->product_group_id = $value;
                        // $ss->save();
                        $inputDataProductGroup['product_group_id'] = $value;
                        $dataToInsertProductGroup[] = $inputDataProductGroup;
                    }
                }

                if(isset($input['product_item_line_id']) && !empty($input['product_item_line_id'])){
                    foreach($input['product_item_line_id'] as $value){
                        // $ss = new CustomerProductItemLine();
                        // $ss->customer_id = $customer;
                        // $ss->assignment_id = $assignment->id;
                        // $ss->product_item_line_id = $value;
                        // $ss->save();
                        $inputDataItemLine['product_item_line_id'] = $value;
                        $dataToInsertItemLine[] = $inputDataItemLine;
                    }
                }

                if(isset($input['product_tires_category_id']) && !empty($input['product_tires_category_id'])){
                    foreach($input['product_tires_category_id'] as $value){
                        // $ss = new CustomerProductTiresCategory();
                        // $ss->customer_id = $customer;
                        // $ss->assignment_id = $assignment->id;
                        // $ss->product_tires_category_id = $value;
                        // $ss->save();
                        $inputDataTiresCategory['product_tires_category_id'] = $value;
                        $dataToInsertTiresCategory[] = $inputDataTiresCategory;
                    }
                }

            }

            if(isset($input['ss_ids']) && !empty($input['ss_ids'])){
                CustomersSalesSpecialist::insert($dataToInsertSalesSpecialist);
            }

            if(isset($input['product_group_id']) && !empty($input['product_group_id'])){
                CustomerProductGroup::insert($dataToInsertProductGroup);
            }

            if(isset($input['product_item_line_id']) && !empty($input['product_item_line_id'])){
                CustomerProductItemLine::insert($dataToInsertItemLine);
            }

            if(isset($input['product_tires_category_id']) && !empty($input['product_tires_category_id'])){
                CustomerProductTiresCategory::insert($dataToInsertTiresCategory);
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
        //$data = Customer::has('sales_specialist')->findOrFail($id);
        $data = salesAssignment::with(['assignment' => function($query){
            $query->groupBy('customer_id');
        }])->findOrFail($id);
        // echo "<pre>";
        // print_r($data);exit();
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
        $edit = salesAssignment::with(['assignment' => function($query){
            $query->groupBy('customer_id');
        }])->findOrFail($id);
        $brand = salesAssignment::with(['brand' => function($query){
            $query->groupBy('product_group_id');
        }])->findOrFail($id);

        $item = salesAssignment::with(['item' => function($query){
            $query->groupBy('product_item_line_id');
        }])->findOrFail($id);

        $category = salesAssignment::with(['category' => function($query){
            $query->groupBy('product_tires_category_id');
        }])->findOrFail($id);
        
        $company = SapConnection::all();
        $ss_ids = salesAssignment::with(['assignment' => function($query){
            $query->groupBy('ss_id');
        }])->findOrFail($id);

        $groups = [];
        $territories = [];
        foreach($edit->assignment as $key => $val){

            $res = array_search(@$val->customer->group->id, array_column($groups, 'id'));
            if($res == ''){
                $ar = array(
                    'id'=>@$val->customer->group->id,
                    'name'=>@$val->customer->group->name,
                );
                if(!in_array(@$val->customer->group->name, array_column($groups,'name'))){
                    array_push($groups,$ar); 
                }
            }
            
            $res = array_search(@$val->customer->territories->id, array_column($territories, 'id'));
            if($res == ''){
                $ar = array(
                    'id'=>@$val->customer->territories->id,
                    'description'=>@$val->customer->territories->description,
                );
                if(!in_array(@$val->customer->territories->description, array_column($territories,'name'))){
                    array_push($territories,$ar); 
                }
            } 
        }   
        // dd($edit->assignment);           
        return view('customers-sales-specialist.add',compact('edit', 'company','ss_ids','brand','item','category','groups', 'territories'));
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
        $del = salesAssignment::find($id);
      
        if($del){

            //save log
            add_log(43, ['id'=>$id]);
            salesAssignment::where('id',$id)->delete();
            TerritorySalesSpecialist::where('assignment_id', $id)->delete();
            CustomersSalesSpecialist::where('assignment_id', $id)->delete();
            CustomerProductGroup::where('assignment_id', $id)->delete();
            CustomerProductItemLine::where('assignment_id', $id)->delete();
            CustomerProductTiresCategory::where('assignment_id', $id)->delete();

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

        $data = salesAssignment::
                                // with(['assignment','assignment.customer','assignment.sales_person', 'assignment.customer.group'])
                                // ->has('assignment.customer')
                                has('assignment')
                                // ->has('assignment.sales_person')
                                ->orderBy('id', 'desc');

        if($request->filter_company != ""){
            $data->whereHas('assignment.customer', function($q) use ($request){
                $q->where('real_sap_connection_id',$request->filter_company);
            });
        }

        if($request->filter_group != ""){
            $data->whereHas('assignment.customer.group', function($q) use ($request){
                $q->where('code',$request->filter_group);
            });
        }

        if($request->filter_search != ""){
            $data->where('assignment_name','LIKE',"%".$request->filter_search."%");

            // $data->orWhereHas('assignment.customer', function($q) use ($request){
            //     $q->where('card_name','LIKE',"%".$request->filter_search."%");
            // });

            $data->orWhereHas('assignment.sales_person', function($q) use ($request){
                $q->where('first_name','LIKE',"%".$request->filter_search."%");
                $q->orWhere('last_name','LIKE',"%".$request->filter_search."%");
                $q->orWhere('email','LIKE',"%".$request->filter_search."%");
            });

            // $data->whereHas('assignment.customer.group', function($q) use ($request){
            //     $q->where('name','LIKE',"%".$request->filter_search."%");
            // });

        }

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('territory', function($row){
                                $branch = '';
                                $count = 0;
                                foreach($row->assignment->take(2) as $value){
                                    $comma = ($count > 0) ? ', ' : '';
                                    if(strpos($branch, @$value->customer->territories->description) === false){
                                        $branch .= $comma.$value->customer->territories->description;
                                    }
                                    $count ++;
                                }
                                return $branch;
                            })
                            ->addColumn('customer', function($row){
                                $customer = '';
                                // $count = 0;
                                // foreach($row->assignment->take(5) as $value){
                                //     $comma = ($count > 0) ? ', ' : '';
                                //     if(strpos($customer, @$value->customer->card_name) === false){
                                //         $customer .= $comma.@$value->customer->card_name;
                                //     }
                                //     $count ++;
                                // }
                                return $customer;
                            })
                            ->addColumn('sales_personnel', function($row){
                                $sales_person = '';
                                $count = 0;
                                foreach($row->assignment->take(2) as $value){
                                    $comma = ($count > 0) ? ', ' : '';
                                    if(strpos($sales_person, @$value->sales_person->email) === false){
                                        $sales_person .= $comma.@$value->sales_person->email;
                                    }
                                    $count ++;
                                }
                                return $sales_person;
                            })
                            ->addColumn('assignment_name', function($row) {
                                return $row->assignment_name;
                            })
                            ->addColumn('company', function($row) {
                                return @$row->assignment->first()->customer->sap_connection->company_name;
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('customers-sales-specialist.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= '<a href="javascript:void(0)" data-url="' . route('customers-sales-specialist.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete mx-2">
                                    <i class="fa fa-trash"></i>
                                  </a>';
                                $btn .= '<a href="' . route('customers-sales-specialist.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';



                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }

    public function getTerritorySalesPerson(Request $request){
        $data = SalesAssignment::orderBy('id', 'desc');

        if($request->filter_company != ""){
            $data->whereHas('assignmentTerritory', function($q) use ($request){
                $q->where('sap_connection_id',$request->filter_company);
            });
        }

        if($request->filter_group != ""){
            $data->whereHas('assignmentTerritory.territory', function($q) use ($request){
                $q->where('territory_id',$request->filter_group);
            });
        }

        if($request->filter_search != ""){
            $data->where('assignment_name','LIKE',"%".$request->filter_search."%");

            $data->orWhereHas('assignmentTerritory.user', function($q) use ($request){
                $q->where('first_name','LIKE',"%".$request->filter_search."%");
                $q->orWhere('last_name','LIKE',"%".$request->filter_search."%");
                $q->orWhere('email','LIKE',"%".$request->filter_search."%");
            });
        }

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('territory', function($row){
                                $branch = '';
                                $count = 0;
                                foreach($row->assignmentTerritory->take(2) as $value){
                                    $comma = ($count > 0) ? ', ' : '';
                                    if(strpos($branch, @$value->territory->description) === false){
                                        $branch .= $comma.@$value->territory->description;
                                    }
                                    $count ++;
                                }
                                return $branch;
                            })
                            ->addColumn('customer', function($row){
                                $customer = '';
                                
                                return $customer;
                            })
                            ->addColumn('sales_personnel', function($row){
                                $sales_person = '';
                                $count = 0;
                                foreach($row->assignmentTerritory->take(2) as $value){
                                    $comma = ($count > 0) ? ', ' : '';
                                    if(strpos($sales_person, @$value->user->email) === false){
                                        $sales_person .= $comma.@$value->user->email;
                                    }
                                    $count ++;
                                }
                                return $sales_person;
                            })
                            ->addColumn('assignment_name', function($row) {
                                return $row->assignment_name;
                            })
                            ->addColumn('company', function($row) {
                                return @$row->assignmentTerritory->first()->sap_connection->company_name;
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('customers-sales-specialist.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= '<a href="javascript:void(0)" data-url="' . route('customers-sales-specialist.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete mx-2">
                                    <i class="fa fa-trash"></i>
                                  </a>';
                                $btn .= '<a href="' . route('customers-sales-specialist.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
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

            $data = User::where('is_active', 1);
            // if(@$request->territories){
            //     $data->whereDoesntHave('sales_specialist_customers.customer', function($q) use($request, $sap_connection_id){
            //                  $q->where('sap_connection_id', $sap_connection_id);
            //                  $q->whereHas('territories', function($y) use($request){
            //                      $y->whereIn('id', $request->territories);
            //                  });
            //              });
            //  }
                            
                $data->whereHas('role',function($q){
                    $q->where('name','like','%Sales Personnel%');
                })
                ->orderby('sales_specialist_name','asc')
                ->select('id','sales_specialist_name','email')
                ->limit(50);

            if($search != ''){
                $data->where(function($q) use ($search){
                    $q->orwhere('sales_specialist_name', 'like', '%' .$search . '%');
                    $q->orwhere('email', 'like', '%' .$search . '%');
                });
            }

            $data = $data->get();

            foreach($data as $value){
                $disabled = false;
                if(@$request->territories){
                    $res = CustomersSalesSpecialist::where('ss_id', $value->id)->whereHas('customer', function($q) use($request, $sap_connection_id){
                                $q->where('sap_connection_id', $sap_connection_id);
                                $q->whereHas('territories', function($y) use($request){
                                    $y->whereIn('id', $request->territories);
                                });
                            })->count();
                    $disabled = ($res === 0) ? false : true;
                }

                $response[] = array(
                    "id"=>$value->id,
                    "text"=>$value->sales_specialist_name."(Email: ".$value->email.")",
                    "disabled" => $disabled
                );
            }
        }

        return response()->json($response);
    }

    public function getCustomers(Request $request){
        $search = $request->search;

        $response = array();
        if($request->sap_connection_id){
            // $data = Customer::doesnthave('sales_specialist')->orderby('card_name','asc')->where('sap_connection_id',$request->sap_connection_id)
            $data = Customer::orderby('card_name','asc')->where('real_sap_connection_id',$request->sap_connection_id)
                //->where('is_active',1)
                ->limit(50);
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
            $data = CustomerGroup::orderby('name','asc')->where('real_sap_connection_id',$request->sap_connection_id)
            ->whereHas('customer',function ($query){
                // $query->doesnthave('sales_specialist')
                        //->where('is_active',1)
                // ->limit(50);
            })
            ->limit(50);
            

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

    public function getCustomerTerritories(Request $request){
        $search = $request->search;

        $response = array();
        if($request->sap_connection_id){
            $data = Territory::orderby('description','asc')
                                ->whereHas('customer',function ($query) use($request) {
                                    $query->where('real_sap_connection_id',$request->sap_connection_id);
                                })->limit(50);

            if($search != ''){
                $data->where('description', 'like', '%' .$search . '%');
            }

            $data = $data->get();
            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->description,
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

            if(userrole() == 4){
                $customer_id = explode(',', Auth::user()->multi_customer_id);
                if(count($customer_id) === 1){
                    $request->customer_id = Auth::user()->customer->id;
                }
            }

            $product_groups = [-1];
            if(!empty($request->customer_id)){
                // Product Group
                $c_product_groups = CustomerProductGroup::with('product_group')->where('customer_id', $request->customer_id)->get()->unique('product_group_id');
                $c_product_group = array_column( $c_product_groups->toArray(), 'product_group_id' );

                $product_groups = array_map( function ( $ar ) {
                    return $ar['number'];
                }, array_column( $c_product_groups->toArray(), 'product_group' ) );
            }

            $data = ProductGroup::where('sap_connection_id',$sap_connection_id)
                                ->orderby('group_name','asc')
                                ->select('id','group_name', 'number')
                                ->where('is_active', true)
                                ->limit(50);

            if($product_groups[0] !== -1){
                $data->whereIn('number', $product_groups);
            }

            if($search != ''){
                $data->where('group_name', 'like', '%' .$search . '%');
            }

            $data->whereNotIn('group_name', ['Items', 'MKTG. MATERIALS', 'OFFICIAL DOCUMENT']);
            
            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->group_name,
                    "code" => $value->number
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
                    "text" => $value->u_tires,
                    "code" => $value->u_tires //text was stored in products
                );
            }
        }

        return response()->json($response);
    }


    public function getAssignedCustomerList(Request $request){
        $response = array();
        $status = false;
        $html = "Not found !";

        if($request->sap_connection_id){
            $data = Customer::has('sales_specialist')->orderby('card_name','asc')->where('sap_connection_id',$request->sap_connection_id)->where('is_active',1);
            
            if(isset($request->group_id) && !empty($request->group_id)){
                $data->whereHas('group', function($q) use ($request){
                    $q->whereIn('id', $request->group_id);
                });
            }

            $data = $data->get();

            $status = true;

            if(count($data)){
                $html = "";
                foreach($data as $value){

                    $html .= '<div class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                              <div class="d-flex align-items-center">
                                <div class="">
                                  <span class="d-flex align-items-center fs-5 fw-bolder text-dark text-hover-primary">'.$value->card_name.' (Code: '.$value->card_code.')</span>
                                </div>
                              </div>
                              <div class="d-flex">
                                <div class="text-end">
                                  <div class="fs-7 text-muted">'.@$value->user->email.'</div>
                                </div>
                              </div>
                            </div>';
                }
            }


        }
        $response = ['html'=>$html, 'status'=>$status];

        return response()->json($response);
    }

}
