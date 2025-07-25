<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotions;
use App\Models\PromotionTypes;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PromotionFor;
use App\Models\PromotionInterest;
use App\Models\CustomerPromotion;
use App\Models\SapConnectionApiFieldValue;
use App\Models\Territory;
use App\Models\Classes;
use App\Models\User;
use App\Models\SapConnection;
use App\Models\ProductGroup;
use Validator;
use DataTables;
use Auth;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PromotionExport;

class PromotionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('promotions.index', compact('company'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = SapConnection::all();
        return view('promotions.add', compact('company'));
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
        // dd($input);

        $rules = array(
                    'title' => 'required|max:185|unique:promotions,title,NULL,id,deleted_at,NULL',
                    'code' => 'required|max:185|regex:/(^([a-zA-Z0-9]+)(\d+)?$)/u|unique:promotions,code,NULL,id,deleted_at,NULL',
                    'promotion_type_id' => 'required|exists:promotion_types,id',
                    'sap_connection_id' => 'required|exists:sap_connections,id',
                    'promotion_scope' => 'required',
                    // 'customer_ids'=> 'required_if:promotion_scope,==,C',
                    // 'territories_ids'=> 'required_if:promotion_scope,==,T',
                    // 'class_ids'=> 'required_if:promotion_scope,==,CL',
                    // 'sales_specialist_ids'=> 'required_if:promotion_scope,==,SS',
                    // 'brand_ids'=> 'required_if:promotion_scope,==,B',
                    // 'market_sector_ids'=> 'required_if:promotion_scope,==,MS',
                    // 'promo_image'=> 'required|max:5000|mimes:jpeg,png,jpg,eps,bmp,tif,tiff,webp',
                );

        if(@$input['promotion_scope_selection'] == "specific" && @$input['promotion_scope'] != "A"){
            $rules['customer_ids'] = 'required_if:promotion_scope,==,C';
            $rules['territories_ids'] = 'required_if:promotion_scope,==,T';
            $rules['class_ids'] = 'required_if:promotion_scope,==,CL';
            $rules['sales_specialist_ids'] = 'required_if:promotion_scope,==,SS';
            $rules['brand_ids'] = 'required_if:promotion_scope,==,B';
            $rules['market_sector_ids'] = 'required_if:promotion_scope,==,MS';
        }

        if(isset($input['id'])){
            unset($rules['promo_image']);
            $rules['title'] = 'required|max:185|unique:promotions,title,'.$input['id'].',id,deleted_at,NULL';
            $rules['code'] = 'required|max:185|regex:/(^([a-zA-Z0-9]+)(\d+)?$)/u|unique:promotions,code,'.$input['id'].',id,deleted_at,NULL';
        }

        if(request()->hasFile('promo_image')){
            $rules['promo_image'] = "required|max:5000|mimes:jpeg,png,jpg,eps,bmp,tif,tiff,webp";
        }

        $message = array(
                        'code.regex' => 'Opps ! the promotion code must be alphanumeric.'
                    );

        $validator = Validator::make($input, $rules, $message);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){

                // check in promotions claimed or not
                $customer_promotions = CustomerPromotion::where('promotion_id',$input['id'])->count();

                if($customer_promotions > 0){
                    return $response = ['status'=>false,'message'=>"Oops! you can not make any updates to this promotions because its already claimed by the customer."];
                }

            }

            // Start - Check Same Type Promotion Used on Dates
            $check = Promotions::where('promotion_type_id', $input['promotion_type_id']);
            if(isset($input['id'])){
                $check->where('id','!=',$input['id']);
            }
            $check = $check->get();

            if(count($check) > 0){
                $s = date('Y-m-d',strtotime($input['promotion_start_date']));
                $e = date('Y-m-d',strtotime($input['promotion_end_date']));

                foreach ($check as $value) {
                    /*if($s > $value->promotion_start_date && $e < $value->promotion_end_date){

                        return $response = ['status'=>false,'message'=>'This promotion type can not be assigned to another promotion unless the end date of that promotion is over.'];

                    }elseif($s < $value->promotion_start_date && ($e > $value->promotion_start_date && $e < $value->promotion_end_date)){

                        return $response = ['status'=>false,'message'=>'This promotion type can not be assigned to another promotion unless the end date of that promotion is over.'];

                    }elseif($s < $value->promotion_end_date && ($e > $value->promotion_end_date)){

                        return $response = ['status'=>false,'message'=>'This promotion type can not be assigned to another promotion unless the end date of that promotion is over.'];
                    }*/


                    if( ($s > $value->promotion_start_date) && ($s < $value->promotion_end_date) ){

                        return $response = ['status'=>false,'message'=>'The selected Promotion Type is already assigned to another promotion. Please choose another Promotion Type OR you can choose the same promotion type once the other promotion ends.'];
                    }else if( ($e > $value->promotion_start_date) && ($e < $value->promotion_end_date) ){
                        return $response = ['status'=>false,'message'=>'The selected Promotion Type is already assigned to another promotion. Please choose another Promotion Type OR you can choose the same promotion type once the other promotion ends.'];
                    }
                }
            }
            // End - Check Same Type Promotion User on Dates


            if(isset($input['id'])){
                $promotion = Promotions::find($input['id']);
                $message = "Promotion updated successfully.";
            }else{
                $promotion = new Promotions();
                $message = "Promotion created successfully.";
            }

            $old_promo_image = file_exists(public_path('sitebucket/promotion/') . "/" . @$promotion->promo_image);
            if(request()->hasFile('promo_image') && @$promotion->promo_image && $old_promo_image){
                unlink(public_path('sitebucket/promotion/') . "/" . $promotion->promo_image);
                $promotion->promo_image = null;
            }

            /*Upload Image*/
            if (request()->hasFile('promo_image')) {
                $file = $request->file('promo_image');
                $name = date("YmdHis") . $file->getClientOriginalName();
                request()->file('promo_image')->move(public_path() . '/sitebucket/promotion/', $name);
                $promotion->promo_image = $name;
            }

            $promotion->promotion_type_id = $input['promotion_type_id'];
            $promotion->title = $input['title'];
            $promotion->code = strtoupper($input['code']);
            $promotion->description = $input['description'];
            $promotion->promotion_scope = $input['promotion_scope'];
            $promotion->promotion_scope_selection = @$input['promotion_scope_selection'] ?? 'all';
            $promotion->promotion_start_date = date('Y-m-d',strtotime($input['promotion_start_date']));
            $promotion->promotion_end_date = date('Y-m-d',strtotime($input['promotion_end_date']));
            $promotion->sap_connection_id = $input['sap_connection_id'];
            $promotion->customer_selection = @$input['select_class_customer'] ?? 'all';
            $promotion->save();

            PromotionFor::where('promotion_id', $promotion->id)->delete();

            $sap_connection_id = @$request->sap_connection_id;
            if($request->sap_connection_id == 5){ //Solid Trend
                $sap_connection_id = 1;
            }

            if($input['promotion_scope'] == 'C'){

                if(@$input['promotion_scope_selection'] != "specific"){
                    $c_ids = Customer::where('sap_connection_id', $sap_connection_id)->pluck('id')->toArray();
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'customer_id' => $value,
                                ]
                            );
                    }
                }else if(isset($input['customer_ids'])){
                    $c_ids = $input['customer_ids'];
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'customer_id' => $value,
                                ]
                            );
                    }
                }
            }

            if($input['promotion_scope'] == 'T'){
                if(@$input['promotion_scope_selection'] != "specific"){
                    $c_ids = Territory::pluck('id')->toArray();
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'territory_id' => $value,
                                ]
                            );
                    }
                }else if(isset($input['territories_ids'])){
                    $c_ids = $input['territories_ids'];
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'territory_id' => $value,
                                ]
                            );
                    }
                }
            }

            if($input['promotion_scope'] == 'CL'){
                if(@$input['promotion_scope_selection'] != "specific"){
                    $c_ids = Classes::pluck('id')->toArray();
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'class_id' => $value,
                                ]
                            );
                    }

                }else if(isset($input['class_ids'])){
                    $c_ids = $input['class_ids'];
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'class_id' => $value,
                                ]
                            );
                    }

                    if($promotion->customer_selection == "specific"){
                        $c_ids = $input['class_customer_ids'];
                        foreach($c_ids as $value){
                            $promotionFor = PromotionFor::create([
                                        'promotion_id' => $promotion->id,
                                        'customer_id' => $value,
                                    ]
                                );
                        }
                    }

                }
            }

            if($input['promotion_scope'] == 'SS'){
                if(@$input['promotion_scope_selection'] != "specific"){
                    $c_ids = User::where('role_id', 2)->where('sap_connection_id', $sap_connection_id)->where('is_active', true)->pluck('id')->toArray();
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'sales_specialist_id' => $value,
                                ]
                            );
                    }
                }else if(isset($input['sales_specialist_ids'])){
                    $c_ids = $input['sales_specialist_ids'];
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'sales_specialist_id' => $value,
                                ]
                            );
                    }
                }
            }

            if($input['promotion_scope'] == 'B'){

                if(@$input['promotion_scope_selection'] != "specific"){
                    $c_ids = ProductGroup::where('sap_connection_id', $sap_connection_id)->where('is_active', true)->pluck('id')->toArray();
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'brand_id' => $value,
                                ]
                            );
                    }
                }else if(isset($input['brand_ids'])){
                    $c_ids = $input['brand_ids'];
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'brand_id' => $value,
                                ]
                            );
                    }
                }
            }

            if($input['promotion_scope'] == 'MS'){
                if(@$input['promotion_scope_selection'] != "specific"){
                    $c_ids = Customer::whereNotNull('u_sector')->where('sap_connection_id', $sap_connection_id)->pluck('u_sector')->toArray();
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'market_sector' => $value,
                                ]
                            );
                    }
                }else if(isset($input['market_sector_ids'])){
                    $c_ids = $input['market_sector_ids'];
                    foreach($c_ids as $value){
                        $promotionFor = PromotionFor::create([
                                    'promotion_id' => $promotion->id,
                                    'market_sector' => $value,
                                ]
                            );
                    }
                }
            }

            if(isset($input['id'])){
                // Add Promotion Updated log.
                add_log(20, array('promotion_id' => $promotion->id));
            } else {
                // Add Promotion Created log.
                add_log(19, array('promotion_id' => $promotion->id));
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
        $data = Promotions::where('id',$id)->firstOrFail();

        $promotion_type = PromotionTypes::get();

        return view('promotions.view',compact('data', 'promotion_type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = Promotions::where('id',$id)->firstOrFail();

        $company = SapConnection::all();

        return view('promotions.add',compact('edit', 'company'));
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
        $data = Promotions::find($id);
        if(!is_null($data)){

            $where = array(
                            'promotion_id' => $id,
                        );

            $claim_count = CustomerPromotion::where($where)->count();
            if($claim_count == 0){
                // Add Promotion Deleted log.
                add_log(21, array('promotion_id' => $id));
                
                $data->delete();

                $response = ['status'=>true,'message'=>'Record deleted successfully !'];

            }else{
                $response = ['status'=>false,'message'=>'Oops! you can not delete this promotions because its already claimed by the customer.'];
            }
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function updateStatus($id)
    {
        $data = Promotions::find($id);
        if(!is_null($data)){
            $data->is_active = !$data->is_active;
            $data->save();
            $response = ['status'=>true,'message'=>'Status update successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Promotions::query();

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_scope != ""){
          $data->where('promotion_scope',$request->filter_scope);
        }

        if($request->filter_promotion_type != ""){
          $data->where('promotion_type_id',$request->filter_promotion_type);
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
                $q->orwhere('code','LIKE',"%".$request->filter_search."%");
                $q->orwhere('description','LIKE',"%".$request->filter_search."%");
            });
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('promotion_start_date', '>=' , $start);
            $data->whereDate('promotion_start_date', '<=' , $end);
        }


        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title', function($row) {
                    return $row->title;
                })
                ->addColumn('code', function($row) {
                    return @$row->code ?? "-";
                })
                ->addColumn('scope', function($row) {
                    $scope = "";
                    switch (@$row->promotion_scope) {
                        case "A":
                          $scope = "All";
                          break;
                        case "C":
                          $scope = "Customer";
                          break;
                        case "CL":
                          $scope = "Class";
                          break;
                        case "T":
                          $scope = "Territory";
                          break;
                        case "SS":
                          $scope = "Sales Specialist";
                          break;
                        case "B":
                          $scope = "Brand";
                          break;
                        case "MS":
                          $scope = "Market Sector";
                          break;
                    }
                    return $scope;
                })
                ->addColumn('start_date', function($row) {
                    return date('M d, Y',strtotime($row->promotion_start_date));
                })
                ->addColumn('end_date', function($row) {
                    return date('M d, Y',strtotime($row->promotion_end_date));
                })
                ->addColumn('company', function($row) {
                    return  @$row->sap_connection->company_name ?? "-";
                })
                ->addColumn('status', function($row) {
                    $btn = "";
                    if($row->is_active){
                        $btn .= '<div class="form-group">
                        <div class="col-3">
                         <span class="switch">
                          <label>
                           <input type="checkbox" checked="checked" name="status" class="status" data-url="' . route('promotion.status',$row->id) . '"/>
                           <span></span>
                          </label>
                         </span>
                        </div>';
                    }else{
                        $btn .= '<div class="form-group">
                        <div class="col-3">
                         <span class="switch">
                          <label>
                           <input type="checkbox" name="status" class="status" data-url="' . route('promotion.status',$row->id) . '"/>
                           <span></span>
                          </label>
                         </span>
                        </div>';
                    }
                    return $btn;
                })
                ->addColumn('action', function($row) {
                    $btn = '<a href="' . route('promotion.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                <i class="fa fa-pencil"></i>
                            </a>';

                    $btn .= ' <a href="javascript:void(0)" data-url="' . route('promotion.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete mr-10">
                                <i class="fa fa-trash"></i>
                              </a>';

                    $btn .= '<a href="' . route('promotion.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                              <i class="fa fa-eye"></i>
                          </a>';

                    return $btn;
                })
                ->addColumn('view', function($row) {
                    $btn = '<a href="' . route('promotion.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                <i class="fa fa-eye"></i>
                            </a>';

                    return $btn;
                })
                ->orderColumn('title', function ($query, $order) {
                    $query->orderBy('title', $order);
                })
                ->orderColumn('code', function ($query, $order) {
                    $query->orderBy('code', $order);
                })
                ->orderColumn('status', function ($query, $order) {
                    $query->orderBy('is_active', $order);
                })
                ->orderColumn('scope', function ($query, $order) {
                    $query->orderBy('promotion_scope', $order);
                })
                ->orderColumn('start_date', function ($query, $order) {
                    $query->orderBy('promotion_start_date', $order);
                })
                ->orderColumn('end_date', function ($query, $order) {
                    $query->orderBy('promotion_end_date', $order);
                })
                ->orderColumn('company', function ($query, $order) {
                    $query->join('sap_connections', 'promotions.sap_connection_id', '=', 'sap_connections.id')->orderBy('sap_connections.company_name', $order);
                })
                ->rawColumns(['view', 'status', 'action'])
                ->make(true);
    }

    function getCustomers(Request $request){
        $search = $request->search;

        $data = Customer::orderby('card_name','asc')->select('id','card_name');
        if($search  != ''){
            $data->where('card_name', 'like', '%' .$search . '%');
        }

        $data->where('sap_connection_id',@$request->sap_connection_id);

        $data = $data->limit(50)->get();

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
            $data = Product::where('is_active', true)->orderby('item_name','asc')->select('id','item_name')->limit(50);
        }else{
            $data = Product::where('is_active', true)->orderby('item_name','asc')->select('id','item_name')->where('item_name', 'like', '%' .$search . '%')->limit(50);
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

    public function getPromotionData(Request $request){
        $scope = $request->scope;
        // $promotion = PromotionFor::where('id', $request->id);

        $data = PromotionFor::where('promotion_id', $request->id);

        if($scope == 'CL'){
            $data->whereNotNull('class_id');
        }


        if($scope == 'MS'){
            $promotion = Promotions::find($request->id);

            $sap_market_sector = SapConnectionApiFieldValue::whereHas('sap_connection_api_field', function($q) use($promotion) {
                $q->where('field', 'sector')->where('sap_connection_id', $promotion->sap_connection_id);
            })->whereIn('key', $data->pluck('market_sector')->toArray())->get();


            return DataTables::of($sap_market_sector)
                ->addIndexColumn()
                ->addColumn('name', function($row) use ($scope) {
                    return $row->value;
                })
                ->make(true);
        }

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function($row) use ($scope) {
                    if($scope == 'C'){
                        return $row->customer->card_name;
                    }else if($scope == 'T'){
                        return $row->territory->description;
                    }else if($scope == 'CL'){
                        return @$row->class->name_sap_value->value ?? $row->class->name;
                    }else if($scope == 'SS'){
                        return $row->sales_specialist->sales_specialist_name;
                    }else if($scope == 'B'){
                        return $row->brand->group_name;
                    }else if($scope == 'MS'){
                        return $row->market_sector;
                    }
                })
                ->addColumn('is_interested', function($row) {
                    return "-";
                })
                ->make(true);
    }

    public function getPromotionClassCustomerData(Request $request){
        $data = PromotionFor::where('promotion_id', $request->id)->whereNull('class_id');

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function($row) {
                    return @$row->customer->card_name ?? "-";
                })
                ->make(true);
    }

    public function getTerritories(Request $request){
        $search = $request->search;

        $data = Territory::orderby('description','asc')->select('id','description');
        if($search != ''){
            $data->where('description', 'like', '%' .$search . '%');
        }

        // $data->where('sap_connection_id',@$request->sap_connection_id);

        $data = $data->limit(50)->get();

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->description
            );
        }

        return response()->json($response);
    }

    public function getClasses(Request $request){
        $search = $request->search;

        $data = Classes::query();

        if($search != ''){
            $data->whereHas('name_sap_value', function($q) use ($search) {
                $q->where('value','LIKE',"%".$search."%");
            });
        }

        $data->where('sap_connection_id',@$request->sap_connection_id);

        $data = $data->limit(50)->get();

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>@$value->name_sap_value->value ?? $value->name
            );
        }

        return response()->json($response);
    }


    public function getClassCustomer(Request $request){
        $search = $request->search;
        $sap_connection_id = $request->sap_connection_id;

        $response = array();
        if($sap_connection_id){
            if($sap_connection_id == 5){
                $sap_connection_id = 1;
            }

            $data = Customer::orderby('card_name','asc')->where('sap_connection_id', $sap_connection_id)->select('id','card_name')->limit(50)->whereIn('class_id', $request->class_id);
            if($search != ''){
                $data->where('card_name', 'like', '%' .$search . '%');
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id"=>$value->id,
                    "text"=>$value->card_name
                );
            }
        }

        return response()->json($response);
    }

    public function getSalesSpecialist(Request $request){
        $search = $request->search;

        $data = User::orderby('sales_specialist_name','asc')->where('is_active', 1)->where('role_id',2)->select('id','sales_specialist_name');
        if($search != ''){
            $data->where('sales_specialist_name', 'like', '%' .$search . '%');
        }

        $sap_connection_id = @$request->sap_connection_id;
        if($request->sap_connection_id == 5){ //Solid Trend
            $sap_connection_id = 1;
        }

        if(@$request->sap_connection_id != ''){
            $data->where('sap_connection_id',@$sap_connection_id);
        }

        $data = $data->limit(50)->get();

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->sales_specialist_name
            );
        }

        return response()->json($response);
    }

    public function getPromotionInterestData(Request $request){

        $data = PromotionInterest::where('promotion_id', $request->id)->latest()->get();

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer', function($row) {
                    return @$row->user->sales_specialist_name ?? "-";
                })
                ->addColumn('is_interested', function($row) {
                    return $row->is_interested ? "Yes" : "No";
                })
                ->make(true);
    }

    public function getPromotionClaimedData(Request $request){

        $data = CustomerPromotion::where('promotion_id', $request->id)->latest()->get();

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer', function($row) {
                    return @$row->user->sales_specialist_name ?? "-";
                })
                ->addColumn('date_time', function($row) {
                    return date('M d, Y',strtotime($row->created_at));
                })
                ->addColumn('action', function($row) {

                    $btn = '<a href="' . route('customer-promotion.order.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm" title="View" target="_blank">
                        <i class="fa fa-eye"></i>
                      </a>';

                    return $btn;
                })
                ->rawColumns(['action','date_time'])
                ->make(true);
    }

    public function getPromotionType(Request $request){
        $search = $request->search;

        $data = PromotionTypes::where('is_active', 1)->orderby('title','asc')->select('id','title');
        if($search != ''){
            $data->where('title', 'like', '%' .$search . '%');
        }

        if(isset($request->action) && $request->action == "add"){
            if($request->sap_connection_id != ''){
                $data->where('sap_connection_id',@$request->sap_connection_id);
            }else{
                return response()->json(collect());
            }
        }

        $data = $data->limit(50)->get();

        return response()->json($data);
    }

    public function getBrands(Request $request){
        $search = $request->search;

        $data = ProductGroup::orderby('group_name','asc')->select('id','group_name')->where('is_active', true);
        if($search != ''){
            $data->where('group_name', 'like', '%' .$search . '%');
        }

        $sap_connection_id = @$request->sap_connection_id;
        if($request->sap_connection_id == 5){ //Solid Trend
            $sap_connection_id = 1;
        }

        if(@$request->sap_connection_id != ''){
            $data->where('sap_connection_id',@$sap_connection_id);
        }

        $data->whereNotIn('group_name', ['Items', 'MKTG. MATERIALS', 'OFFICIAL DOCUMENT']);
        
        $data = $data->limit(50)->get();

        return response()->json($data);
    }

    public function getMarketSectors(Request $request){
        $response = array();

        $search = $request->search;

        $data = Customer::orderby('u_sector','asc')->whereNotNull('u_sector')->select('id','u_sector');
        if($search != ''){
            $data->where('u_sector', 'like', '%' .$search . '%');
        }

        $data->where('sap_connection_id',@$request->sap_connection_id);

        // $data = $data->get()->unique('u_sector');

        $data = $data->limit(50)->groupBy('u_sector')->get();

        foreach($data as $value){
            $response[] = array(
                "id" => $value->u_sector,
                "text" => @$value->u_sector_sap_value->value ?? $value->u_sector
            );
        }

        return response()->json($response);
    }

    public function checkTitle(Request $request){
        // dd($request->all());
        if(!empty(@$request->title)){
            $input = $request->all();

            $rules = array(
                        'title' => 'required|max:185|unique:promotions,title,NULL,id,deleted_at,NULL',
                    );

            if(isset($input['id'])){
                $rules['title'] = 'required|max:185|unique:promotions,title,'.$input['id'].',id,deleted_at,NULL';
            }

            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                $response = ['status'=>false,'message'=> 'Title already exists, please choose another title'];
            }else{
                $response = ['status'=>true,'message'=>'Title is unique!'];
            }

        }else{
            $response = ['status'=>false,'message'=>'Something went wrong!'];
        }
        return $response;
    }


    public function export(Request $request){
        $filter = collect();
        if(@$request->data){
          $filter = json_decode(base64_decode($request->data));
        }

        $data = Promotions::orderBy('created_at', 'desc');

        if(@$filter->filter_status != ""){
            $data->where('is_active',$filter->filter_status);
        }

        if(@$filter->filter_scope != ""){
          $data->where('promotion_scope',$filter->filter_scope);
        }

        if(@$filter->filter_promotion_type != ""){
          $data->where('promotion_type_id',$filter->filter_promotion_type);
        }

        if(@$filter->filter_company != ""){
            $data->where('sap_connection_id',$filter->filter_company);
        }

        if(@$filter->filter_search != ""){
            $data->where(function($q) use ($filter) {
                $q->orwhere('title','LIKE',"%".$filter->filter_search."%");
                $q->orwhere('description','LIKE',"%".$filter->filter_search."%");
            });
        }

        if(@$filter->filter_date_range != ""){
            $date = explode(" - ", $filter->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('promotion_start_date', '>=' , $start);
            $data->whereDate('promotion_start_date', '<=' , $end);
        }

        $data = $data->get();

        $records = array();
        foreach($data as $key => $value){

            $scope = "-";
            switch (@$value->promotion_scope) {
                case "C":
                    $scope = "Customer";
                    break;
                case "CL":
                    $scope = "Class";
                    break;
                case "T":
                    $scope = "Territory";
                    break;
                case "SS":
                    $scope = "Sales Specialist";
                    break;
                case "B":
                    $scope = "Brand";
                    break;
                case "MS":
                    $scope = "Market Sector";
                    break;
            }


            $records[] = array(
                            'no' => $key + 1,
                            'company' => @$value->sap_connection->company_name ?? "-",
                            'title' => $value->title ?? "-",
                            'code' => $value->code ?? "-",
                            'customer_group' => $scope ?? "-",
                            'start_date' => date('M d, Y',strtotime($value->promotion_start_date)),
                            'end_date' => date('M d, Y',strtotime($value->promotion_end_date)),
                            'status' => $value->is_active ? "Active" : "Inctive",
                          );
        }
        if(count($records)){
            $title = 'Promotion Report '.date('dmY').'.xlsx';
            return Excel::download(new PromotionExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }
}
