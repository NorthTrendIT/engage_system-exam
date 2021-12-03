<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PromotionTypes;
use App\Models\Product;
use App\Models\PromotionTypeProduct;
use DataTables;
use Validator;
use Auth;

class PromotionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('promotion-type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('promotion-type.add');
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
                        'fixed_quantity' => 'nullable|integer',
                        'number_of_delivery' => 'required|integer',
                        'max_percentage' => 'required_if:scope,R',
                        'min_percentage' => 'required_if:scope,R',
                        'percentage' => 'required_if:scope,P,U',
                        'fixed_price' => 'required_if:scope,U',
                        'products' => 'required_if:scope,P,U|array',
                        'products.*' => 'required_if:scope,P,U|exists:products,id',
                  );

        if(isset($input['id'])){
            $rules['title'] = 'required|max:185|unique:promotion_types,title,'.$input['id'].',id,deleted_at,NULL';
        }


        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

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

                if(in_array($input['scope'], ['P','U'])){

                    if(@$input['products']){

                        foreach ($input['products'] as $key => $value) {
                            $product_ids[] = $value;

                            PromotionTypeProduct::updateOrCreate(
                                        [
                                            'promotion_type_id' => $obj->id,
                                            'product_id' => $value,
                                        ],
                                        [
                                            'promotion_type_id' => $obj->id,
                                            'product_id' => $value,
                                            'discount_percentage' => NULL,
                                        ],
                                    );
                        }

                        $removeProduct = PromotionTypeProduct::where('promotion_type_id',$obj->id);
                        $removeProduct->whereNotIn('product_id',$product_ids);
                        $removeProduct->delete();

                    }
                }elseif(in_array($input['scope'], ['R'])){

                    if(@$input['product_list']){

                        foreach ($input['product_list'] as $key => $value) {
                            $product_ids[] = $value['product_id'];

                            PromotionTypeProduct::updateOrCreate(
                                        [
                                            'promotion_type_id' => $obj->id,
                                            'product_id' => $value['product_id'],
                                        ],
                                        [
                                            'promotion_type_id' => $obj->id,
                                            'product_id' => $value['product_id'],
                                            'discount_percentage' => $value['discount_percentage'],
                                        ],
                                    );
                        }

                        $removeProduct = PromotionTypeProduct::where('promotion_type_id',$obj->id);
                        $removeProduct->whereNotIn('product_id',$product_ids);
                        $removeProduct->delete();

                    }
                }else{
                    $removeProduct = PromotionTypeProduct::where('promotion_type_id',$obj->id);
                    $removeProduct->delete();
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
        $edit = PromotionTypes::findOrFail($id);

        return view('promotion-type.add',compact('edit'));
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

    public function getProducts(Request $request)
    {
        $search = $request->search;

        $data = Product::orderby('item_name','asc')->where('is_active',true);
        
        if($search != ''){
            $data->where('item_name', 'like', '%' .$search . '%');
        }

        $data = $data->limit(50)->get();

        return $data;
    }


    public function getAll(Request $request){

        $data = PromotionTypes::query();

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('promotion-type.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('promotion-type.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                // $btn .= ' <a href="' . route('user.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                //     <i class="fa fa-eye"></i>
                                //   </a>';

                                return $btn;
                            })
                            ->addColumn('title', function($row) {
                                return @$row->title ?? "-";
                            })
                            ->addColumn('status', function($row) {

                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<a href="javascript:"  data-url="' . route('promotion-type.status',$row->id) . '" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                                }else{
                                    $btn .= '<a href="javascript:"  data-url="' . route('promotion-type.status',$row->id) . '" class="btn btn-sm btn-light-danger btn-inline status">Inctive</a>';
                                }

                                return $btn;
                            })
                            ->orderColumn('title', function ($query, $order) {
                                $query->orderBy('title', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->rawColumns(['action','status'])
                            ->make(true);
    }
}
