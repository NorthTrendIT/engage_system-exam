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
use App\Models\Territory;
use App\Models\Classes;
use App\Models\User;
use Validator;
use DataTables;
use Auth;

class PromotionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('promotions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $promotion_type = PromotionTypes::where('is_active',1)->get();
        return view('promotions.add', compact('promotion_type'));
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
                    'promotion_type_id' => 'required',
                    'title' => 'required|string|max:185',
                    // 'discount_percentage' => 'required',
                    'customer_ids'=> 'required_if:promotion_scope,==,C',
                    'territories_ids'=> 'required_if:promotion_scope,==,T',
                    'class_ids'=> 'required_if:promotion_scope,==,CL',
                    'sales_specialist_ids'=> 'required_if:promotion_scope,==,SS',
                    'promo_image'=> 'required|max:5000|mimes:jpeg,png,jpg,eps,bmp,tif,tiff,webp',
                );

        if($input['promotion_for'] == "All"){
            $input['promotion_scope'] = null;
        }

        if(isset($input['id'])){
            unset($rules['promo_image']);
        }

        if(request()->hasFile('promo_image')){
            $rules['promo_image'] = "required|max:5000|mimes:jpeg,png,jpg,eps,bmp,tif,tiff,webp";
        }

        $validator = Validator::make($input, $rules);

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
            $promotion->description = $input['description'];
            // $promotion->discount_percentage = $input['discount_percentage'];
            $promotion->promotion_for = $input['promotion_for'];
            $promotion->promotion_scope = $input['promotion_scope'];
            // $promotion->promo_image = !empty($input['promo_image']) && $input['promo_image'] ? $input['promo_image'] : null;
            $promotion->promotion_start_date = date('Y-m-d',strtotime($input['promotion_start_date']));
            $promotion->promotion_end_date = date('Y-m-d',strtotime($input['promotion_end_date']));
            $promotion->save();

            if($input['promotion_scope'] == 'C' && isset($input['customer_ids']) ){
                $c_ids = $input['customer_ids'];
                PromotionFor::where('promotion_id', $promotion->id)->delete();
                foreach($c_ids as $value){
                    $promotionFor = PromotionFor::updateOrCreate([
                                'promotion_id' => $promotion->id,
                                'customer_id' => $value,
                            ]
                        );
                }
            }

            if($input['promotion_scope'] == 'T' && isset($input['territories_ids']) ){
                $c_ids = $input['territories_ids'];
                PromotionFor::where('promotion_id', $promotion->id)->delete();
                foreach($c_ids as $value){
                    $promotionFor = PromotionFor::updateOrCreate([
                                'promotion_id' => $promotion->id,
                                'territory_id' => $value,
                            ]
                        );
                }
            }

            if($input['promotion_scope'] == 'CL' && isset($input['class_ids']) ){
                $c_ids = $input['class_ids'];
                PromotionFor::where('promotion_id', $promotion->id)->delete();
                foreach($c_ids as $value){
                    $promotionFor = PromotionFor::updateOrCreate([
                                'promotion_id' => $promotion->id,
                                'class_id' => $value,
                            ]
                        );
                }
            }

            if($input['promotion_scope'] == 'SS' && isset($input['sales_specialist_ids']) ){
                $c_ids = $input['sales_specialist_ids'];
                PromotionFor::where('promotion_id', $promotion->id)->delete();
                foreach($c_ids as $value){
                    $promotionFor = PromotionFor::updateOrCreate([
                                'promotion_id' => $promotion->id,
                                'sales_specialist_id' => $value,
                            ]
                        );
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

        $promotion_type = PromotionTypes::get();

        return view('promotions.add',compact('edit', 'promotion_type'));
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
            $data->delete();

            // Add Promotion Deleted log.
            add_log(21, array('promotion_id' => $data));

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
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

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
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
                ->addColumn('promotion_for', function($row) {
                    return $row->promotion_for;
                })
                ->addColumn('scope', function($row) {
                  $scope = "";
                  switch (@$row->promotion_scope) {
                    case "C":
                      $scope = "Customer";
                      break;
                    case "CL":
                      $scope = "Class";
                      break;
                    case "T":
                      $scope = "Territories";
                      break;
                    case "P":
                      $scope = "Products";
                      break;
                    case "SS":
                      $scope = "Sales Specialists";
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
                ->addColumn('status', function($row) {
                    $btn = "";
                    if($row->is_active){
                        $btn .= '<a href="javascript:"  data-url="' . route('promotion.status',$row->id) . '" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                    }else{
                        $btn .= '<a href="javascript:"  data-url="' . route('promotion.status',$row->id) . '" class="btn btn-sm btn-light-danger btn-inline status">Inctive</a>';
                    }
                    return $btn;
                })
                ->addColumn('action', function($row) {
                    $btn = '<a href="' . route('promotion.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-color-success mr-10">
                                <i class="fa fa-pencil"></i>
                            </a>';

                    $btn .= ' <a href="javascript:void(0)" data-url="' . route('promotion.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-color-danger delete mr-10">
                                <i class="fa fa-trash"></i>
                              </a>';

                    $btn .= '<a href="' . route('promotion.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-color-primary">
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
                ->orderColumn('promotion_for', function ($query, $order) {
                    $query->orderBy('promotion_for', $order);
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
                ->rawColumns(['view', 'status', 'action'])
                ->make(true);
    }

    function getCustomers(Request $request){
        $search = $request->search;

        if($search == ''){
            $data = Customer::orderby('card_name','asc')->select('id','card_name')->limit(50)->get();
        }else{
            $data = Customer::orderby('card_name','asc')->select('id','card_name')->where('card_name', 'like', '%' .$search . '%')->limit(50)->get();
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
            $data = Product::orderby('item_name','asc')->select('id','item_name')->limit(50)->get();
        }else{
            $data = Product::orderby('item_name','asc')->select('id','item_name')->where('item_name', 'like', '%' .$search . '%')->limit(50)->get();
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

    public function getPromotionData(Request $request){
        $scope = $request->scope;
        // $data = PromotionFor::where('id', $request->id);

        $data = PromotionFor::where('promotion_id', $request->id)->get();

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function($row) use ($scope) {
                    if($scope == 'C'){
                        return $row->customer->card_name;
                    }else if($scope == 'P'){
                        return $row->product->item_name;
                    }else if($scope == 'T'){
                        return $row->territory->description;
                    }else if($scope == 'CL'){
                        return $row->class->name;
                    }else if($scope == 'SS'){
                        return $row->sales_specialist->sales_specialist_name;
                    }
                })
                ->addColumn('is_interested', function($row) {
                    return "-";
                })
                ->make(true);
    }

    public function getTerritories(Request $request){
        $search = $request->search;

        if($search == ''){
            $data = Territory::orderby('description','asc')->select('id','description')->limit(50)->get();
        }else{
            $data = Territory::orderby('description','asc')->select('id','description')->where('description', 'like', '%' .$search . '%')->limit(50)->get();
        }

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

        if($search == ''){
            $data = Classes::orderby('name','asc')->select('id','name')->limit(50)->get();
        }else{
            $data = Classes::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->limit(50)->get();
        }

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->name
            );
        }

        return response()->json($response);
    }

    public function getSalesSpecialist(Request $request){
        $search = $request->search;

        $data = User::orderby('sales_specialist_name','asc')->where('role_id',2)->select('id','sales_specialist_name')->limit(50);
        if($search != ''){
            $data->where('sales_specialist_name', 'like', '%' .$search . '%');
        }

        $data = $data->get();

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

    public function getPromotionType(Request $request){
        $search = $request->search;

        $data = PromotionTypes::orderby('title','asc')->select('id','title')->limit(50);
        if($search != ''){
            $data->where('title', 'like', '%' .$search . '%');
        }

        $data = $data->get();

        return response()->json($data);
    }
}
