<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PromotionTypes;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\PromotionTypeProduct;
use App\Models\CustomerPromotion;
use App\Models\Promotions;
use App\Models\SapConnection;
use DataTables;
use Validator;
use Auth;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PromotionTypeExport;

class PromotionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('promotion-type.index', compact('company'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = SapConnection::all();
        return view('promotion-type.add', compact('company'));
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
                        'title' => 'required|max:185|unique:promotion_types,title,NULL,id,deleted_at,NULL',
                        'scope' => 'required',
                        //'fixed_quantity' => 'nullable|integer',
                        'is_fixed_quantity' => 'required',
                        'number_of_delivery' => 'required|nullable|integer',
                        'max_percentage' => 'required_if:scope,R',
                        'min_percentage' => 'required_if:scope,R',
                        'percentage' => 'required_if:scope,P,U',
                        'fixed_price' => 'required_if:scope,U',

                        'is_total_fixed_quantity' => 'required_if:is_fixed_quantity,1',
                        'total_fixed_quantity' => 'required_if:is_total_fixed_quantity,1',

                        'sap_connection_id' => 'required|exists:sap_connections,id',

                        'product_list' => 'required|array',
                        'product_list.*.product_id' => 'distinct|exists:products,id,sap_connection_id,'.$input['sap_connection_id'],

                        'product_list.*.brand_id' => 'required|exists:product_groups,id,sap_connection_id,'.$input['sap_connection_id'],

                  );

        if(isset($input['id'])){
            $rules['title'] = 'required|max:185|unique:promotion_types,title,'.$input['id'].',id,deleted_at,NULL';
        }

        $msg = array(
                            'product_list.*.product_id.distinct'=>"The products are must be unique."
                        );

        $validator = Validator::make($input, $rules,$msg);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){

                // check in promotions claimed or not
                $customer_promotions = CustomerPromotion::whereHas('promotion',function($q) use ($input){
                    $q->where('promotion_type_id',$input['id']);
                })->count();

                if($customer_promotions > 0){
                    return $response = ['status'=>false,'message'=>"Oops! you can not make any updates to this promotion type because its already claimed by the customer."];
                }


                // Check Promotions is working at the moment or not
                $promotions = Promotions::where('promotion_type_id', $input['id'])->get();

                $now = date("Y-m-d");
                foreach ($promotions as $key => $promotion) {
                    if($now >= $promotion->promotion_start_date && $now <= $promotion->promotion_end_date){
                        return $response = ['status'=>false,'message'=>"Oops! This promotion type is in use, can not make any changes to this promotion type at this time."];
                    }
                }

            }


            if(!in_array($input['scope'],['R'])){
                $input['min_percentage'] = $input['max_percentage'] = NULL;
            }

            if($input['is_total_fixed_quantity'] == false){
                $input['total_fixed_quantity'] = NULL;
            }

            if($input['is_fixed_quantity'] == false){
                $input['is_total_fixed_quantity'] = false;
                $input['total_fixed_quantity'] = NULL;
            }

            if(isset($input['id'])){
                $obj = PromotionTypes::find($input['id']);
                $message = "Promotion Type details updated successfully.";
            }else{
                $obj = new PromotionTypes();
                $message = "New Promotion Type created successfully.";
            }

            $obj->fill($input)->save();

            if(@$obj->id){

                $product_ids = [];

                if(@$input['product_list']){

                    foreach ($input['product_list'] as $key => $value) {
                        $product_ids[] = $value['product_id'];

                        $insert = [
                                        'promotion_type_id' => $obj->id,
                                        'brand_id' => $value['brand_id'],
                                        'product_id' => $value['product_id'],
                                        'fixed_quantity' => $value['fixed_quantity'],
                                        'discount_percentage' => $value['discount_percentage'],
                                    ];

                        if(in_array($input['scope'], ['P','U'])){
                           $insert['discount_percentage'] = NULL;
                        }

                        if($input['is_fixed_quantity'] == "0" || $input['is_total_fixed_quantity'] == "1"){
                           $insert['fixed_quantity'] = NULL;
                        }

                        PromotionTypeProduct::updateOrCreate(
                                    [
                                        'promotion_type_id' => $obj->id,
                                        'product_id' => $value['product_id'],
                                    ],
                                    $insert,
                                );
                    }

                    $removeProduct = PromotionTypeProduct::where('promotion_type_id',$obj->id);
                    $removeProduct->whereNotIn('product_id',$product_ids);
                    $removeProduct->delete();

                }else{
                    $removeProduct = PromotionTypeProduct::where('promotion_type_id',$obj->id);
                    $removeProduct->delete();
                }
            }

            if(isset($input['id'])){
                // Add Updated log.
                add_log(24, array('id' => $obj->id));
            } else {
                // Add Created log.
                add_log(23, array('id' => $obj->id));
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
        $data = PromotionTypes::findOrFail($id);

        return view('promotion-type.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = PromotionTypes::findOrFail($id);
        $company = SapConnection::all();

        return view('promotion-type.add',compact('edit', 'company'));
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
        $data = PromotionTypes::find($id);
        if(!is_null($data)){

            // Add Log
            add_log(25, array('id' => $id));

            $data->delete();

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function updateStatus($id)
    {
        $data = PromotionTypes::find($id);
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

        $data = PromotionTypes::query();

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_fixed_quantity != ""){
            $data->where('is_fixed_quantity',$request->filter_fixed_quantity);
        }

        if($request->filter_criteria != ""){
            $data->where('scope',$request->filter_criteria);
        }

        if($request->filter_company != ""){
            $data->where('sap_connection_id',$request->filter_company);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
                $q->orwhere('description','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('promotion-type.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('promotion-type.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete mr-10">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                $btn .= ' <a href="' . route('promotion-type.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                return $btn;
                            })
                            ->addColumn('title', function($row) {
                                return @$row->title ?? "-";
                            })
                            ->addColumn('is_fixed_quantity', function($row) {
                                return @$row->is_fixed_quantity ? "Yes" : "No";
                            })
                            ->addColumn('scope', function($row) {
                                return get_promotion_type_criteria($row->scope);
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
                                       <input type="checkbox" checked="checked" name="status" class="status" data-url="' . route('promotion-type.status',$row->id) . '"/>
                                       <span></span>
                                      </label>
                                     </span>
                                    </div>';
                                }else{
                                    $btn .= '<div class="form-group">
                                    <div class="col-3">
                                     <span class="switch">
                                      <label>
                                       <input type="checkbox" name="status" class="status" data-url="' . route('promotion-type.status',$row->id) . '"/>
                                       <span></span>
                                      </label>
                                     </span>
                                    </div>';
                                }
                                return $btn;
                            })
                            ->orderColumn('title', function ($query, $order) {
                                $query->orderBy('title', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->orderColumn('scope', function ($query, $order) {
                                $query->orderBy('scope', $order);
                            })
                            ->orderColumn('is_fixed_quantity', function ($query, $order) {
                                $query->orderBy('is_fixed_quantity', $order);
                            })
                            ->orderColumn('company', function ($query, $order) {
                                $query->join('sap_connections', 'promotion_types.sap_connection_id', '=', 'sap_connections.id')->orderBy('sap_connections.company_name', $order);
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
    }

    public function getProducts(Request $request)
    {
        $search = $request->search;

        $data = collect();

        $brand = ProductGroup::find($request->brand_id);

        if(@$request->sap_connection_id && @$brand){
            $where = array(
                            'sap_connection_id' => $request->sap_connection_id,
                            'items_group_code' => $brand->number,
                            'is_active' => true,
                        );

            $data = Product::orderby('item_name','asc')->where($where);

            if($search != ''){
                $data->where('item_name', 'like', '%' .$search . '%');
            }

            if(isset($request->product_ids) && count($request->product_ids)){
                $data->whereNotIn('id', $request->product_ids);
            }

            $data = $data->limit(50)->get();
        }

        return $data;
    }

    public function getBrands(Request $request)
    {
        $search = $request->search;

        $data = collect();

        if(@$request->sap_connection_id){
            $data = ProductGroup::orderby('group_name','asc')->where('sap_connection_id',$request->sap_connection_id);

            if($search != ''){
                $data->where('group_name', 'like', '%' .$search . '%');
            }

            $data = $data->limit(50)->get();
        }

        return $data;
    }


    public function export(Request $request){
        $filter = collect();
        if(@$request->data){
          $filter = json_decode(base64_decode($request->data));
        }

        $data = PromotionTypes::orderBy('created_at', 'desc');

        if(@$filter->filter_status != ""){
            $data->where('is_active',$filter->filter_status);
        }

        if(@$filter->filter_fixed_quantity != ""){
            $data->where('is_fixed_quantity',$filter->filter_fixed_quantity);
        }

        if(@$filter->filter_criteria != ""){
            $data->where('scope',$filter->filter_criteria);
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

        $data = $data->get();

        $records = array();
        foreach($data as $key => $value){
            $records[] = array(
                            'no' => $key + 1,
                            'company' => @$value->sap_connection->company_name ?? "-",
                            'title' => $value->title ?? "-",
                            'criteria' => get_promotion_type_criteria($value->scope),
                            'fixed_quantity' => @$value->is_fixed_quantity ? "Yes" : "No",
                            'status' => $value->is_active ? "Active" : "Inctive",
                          );
        }
        if(count($records)){
            $title = 'Promotion Type Report '.date('dmY').'.xlsx';
            return Excel::download(new PromotionTypeExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }
}
