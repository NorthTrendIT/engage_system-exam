<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SyncProducts;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SapConnection;
use App\Models\ProductGroup;
use App\Models\ProductItemLine;
use App\Models\ProductTiresCategory;
use DataTables;
use Validator;
use Auth;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $product_groups = ProductGroup::all();
    $product_line = ProductItemLine::get()->groupBy('u_item_line');
    $product_category = ProductTiresCategory::get()->groupBy('u_tires');
    $company = SapConnection::all();

    return view('product.index',compact('product_groups','product_line','product_category','company'));
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
                  'product_features' => 'nullable',
                  'product_benefits' => 'nullable',
                  'product_sell_sheets' => 'nullable',
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
    return view('product.add',compact('edit'));
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

      // // Add sync Product data log.
      // add_log(18, null);

      // // Save Data of Product in database
      // SyncProducts::dispatch('TEST-APBW', 'manager', 'test');

      $sap_connections = SapConnection::all();
      foreach ($sap_connections as $value) {

        $log_id = add_sap_log([
                                'ip_address' => userip(),
                                'activity_id' => 18,
                                'user_id' => userid(),
                                'data' => null,
                                'type' => "S",
                                'status' => "in progress",
                                'sap_connection_id' => $value->id,
                            ]);

        // Save Data of Product in database
        SyncProducts::dispatch($value->db_name, $value->user_name , $value->password, $log_id);
      }

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

    if($request->filter_company != ""){
      $data->where('sap_connection_id',$request->filter_company);
    }

    if($request->filter_brand != ""){
      $data->where('items_group_code',$request->filter_brand);
    }

    if($request->filter_product_category != ""){
      $data->where('u_tires',$request->filter_product_category);
    }

    if($request->filter_product_line != ""){
      $data->where('u_item_line',$request->filter_product_line);
    }

    if($request->filter_search != ""){
      $data->where(function($q) use ($request) {
        $q->orwhere('item_code','LIKE',"%".$request->filter_search."%");
        $q->orwhere('item_name','LIKE',"%".$request->filter_search."%");
      });
    }

    if($request->filter_date_range != ""){
      $date = explode(" - ", $request->filter_date_range);
      $start = date("Y-m-d", strtotime($date[0]));
      $end = date("Y-m-d", strtotime($date[1]));

      $data->whereDate('created_date', '>=' , $start);
      $data->whereDate('created_date', '<=' , $end);
    }


    if(userrole() != 1){
      $data->where('is_active', true);

      if(@Auth::user()->sap_connection_id){
        $data->where('sap_connection_id', @Auth::user()->sap_connection_id);
      }
    }

    $data->when(!isset($request->order), function ($q) {
      $q->orderBy('created_date', 'desc');
    });

    return DataTables::of($data)
                          ->addIndexColumn()
                          ->addColumn('item_name', function($row) {
                              return @$row->item_name ?? "";
                          })
                          ->addColumn('item_code', function($row) {
                              return @$row->item_code ?? "";
                          })
                          ->addColumn('brand', function($row) {
                              return @$row->group->group_name ?? "";
                          })
                          ->addColumn('u_item_line', function($row) {
                              return @$row->u_item_line ?? "-";
                          })
                          ->addColumn('u_tires', function($row) {
                              return @$row->u_tires ?? "-";
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
                              $btn = '<a href="' . route('product.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                  <i class="fa fa-pencil"></i>
                                </a>';

                              $btn .= '<a href="' . route('product.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm" title="View Details">
                                  <i class="fa fa-eye"></i>
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
                          ->addColumn('company', function($row) {
                              return  @$row->sap_connection->company_name ?? "-";
                          })
                          ->orderColumn('item_name', function ($query, $order) {
                              $query->orderBy('item_name', $order);
                          })
                          ->orderColumn('item_code', function ($query, $order) {
                              $query->orderBy('item_code', $order);
                          })
                          ->orderColumn('u_tires', function ($query, $order) {
                              $query->orderBy('u_tires', $order);
                          })
                          ->orderColumn('u_item_line', function ($query, $order) {
                              $query->orderBy('u_item_line', $order);
                          })
                          ->orderColumn('created_date', function ($query, $order) {
                              $query->orderBy('created_date', $order);
                          })
                          ->orderColumn('status', function ($query, $order) {
                              $query->orderBy('is_active', $order);
                          })
                          ->orderColumn('brand', function ($query, $order) {
                              // $query->join('product_groups', 'products.items_group_code', '=', 'product_groups.number')
                              //       ->orderBy('product_groups.group_name', $order);

                              $query->join("product_groups",function($join){
                                  $join->on("products.items_group_code","=","product_groups.number")
                                      ->on("products.sap_connection_id","=","product_groups.sap_connection_id");
                              })->orderBy('product_groups.group_name', $order);
                          })
                          ->orderColumn('company', function ($query, $order) {
                              $query->join('sap_connections', 'promotion_types.sap_connection_id', '=', 'sap_connections.id')->orderBy('sap_connections.company_name', $order);
                          })
                          ->rawColumns(['status','action'])
                          ->make(true);
  }
}
