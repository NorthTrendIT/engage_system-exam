<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SyncProducts;
use App\Models\Product;
use App\Models\ProductFeatures;
use App\Models\ProductSellSheets;
use App\Models\ProductBenefits;
use App\Models\ProductImage;
use DataTables;
use Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                    'id' => 'required|exists:products,id',
                    'product_features_id' => 'nullable|exists:product_features,id',
                    'product_benefits_id' => 'nullable|exists:product_benefits,id',
                    'product_sell_sheets_id' => 'nullable|exists:product_sell_sheets,id',
                );


        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            $product = Product::findOrFail($input['id']);
            $message = "Product details updated successfully.";

            $product->fill($input)->save();

            // Start Product Images
            $product_images_ids = array();
            if(isset($input['product_images'])){
              foreach ($input['product_images'] as $key => $value) {
                $value['product_id'] = $product->id;

                if(isset($value['image']) && is_object($value['image'])){
                  $file = $value['image'];

                  if(!in_array($file->extension(),['jpeg','jpg','png','eps','bmp','tif','tiff','webp'])){
                    continue;
                  }

                  if($file->getSize() <= 10 * 1024 * 1024){ //10MB
                    $name = date("YmdHis") . $file->getClientOriginalName() ;
                    $file->move(public_path() . '/sitebucket/products/', $name);
                    $value['image'] = $name;
                  }
                }

                if($value['image']){
                  if(isset($value['id'])){
                    $product_image_obj = ProductImage::find($value['id']);
                  }else{
                    $product_image_obj = New ProductImage();
                  }

                  $product_image_obj->fill($value)->save();

                  if(@$product_image_obj->id){
                    array_push($product_images_ids, $product_image_obj->id);
                  }
                }
              }
            }

            if(!isset($input['product_images'])){
              $removeProductImage = ProductImage::where('product_id',$product->id);
              $removeProductImage->delete();
            }elseif(!empty($product_images_ids)){
              $removeProductImage = ProductImage::where('product_id',$product->id);
              $removeProductImage->whereNotIn('id',$product_images_ids);
              $removeProductImage->delete();
            }
            // End Product Images

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
      $data = Product::findOrFail($id);
      return view('product.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $edit = Product::findOrFail($id);
      $product_features = ProductFeatures::all();
      $product_benefits = ProductBenefits::all();
      $product_sell_sheets = ProductSellSheets::all();

      return view('product.add',compact('edit','product_features','product_benefits','product_sell_sheets'));

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

    public function syncProducts(){
      try {
        // Save Data of Product in database
        SyncProducts::dispatch('TEST-APBW', 'manager', 'test');

        $response = ['status' => true, 'message' => 'Sync Product successfully !'];
      } catch (\Exception $e) {
        $response = ['status' => false, 'message' => 'Something went wrong !'];
      }
      return $response;
    }

    public function getAll(Request $request){

        $data = Product::query();

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('item_code','LIKE',"%".$request->filter_search."%");
                $q->orwhere('item_name','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('item_name', function($row) {
                                return @$row->item_name ?? "";
                            })
                            ->addColumn('item_code', function($row) {
                                return @$row->item_code ?? "";
                            })
                            ->addColumn('status', function($row) {
                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                                }else{
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-danger btn-inline status">Inctive</a>';
                                }

                                return $btn;
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('product.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-color-success mr-10">
                                    <i class="fa fa-edit"></i>
                                  </a>';

                                $btn .= '<a href="' . route('product.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm btn-color-warning">
                                    <i class="fa fa-file"></i>
                                  </a>';
                                
                                return $btn;
                            })
                            ->addColumn('class', function($row) {
                                $html = "";
                                return $html;
                            })
                            ->addColumn('created_date', function($row) {
                                return date('M d, Y',strtotime($row->created_date));
                            })
                            ->orderColumn('item_name', function ($query, $order) {
                                $query->orderBy('item_name', $order);
                            })
                            ->orderColumn('item_code', function ($query, $order) {
                                $query->orderBy('item_code', $order);
                            })
                            ->orderColumn('created_date', function ($query, $order) {
                                $query->orderBy('created_date', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->rawColumns(['status','action'])
                            ->make(true);
    }
}
