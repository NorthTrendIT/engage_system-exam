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

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use App\Exports\ProductTaggingExport;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // $product_groups = ProductGroup::all();
    // $product_line = ProductItemLine::get()->groupBy('u_item_line');
    // $product_category = ProductTiresCategory::get()->groupBy('u_tires');
    $company = SapConnection::all();

    return view('product.index',compact('company'));
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

      $sap_connections = SapConnection::where('id', '!=', 5)->get();
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
      $filter_company = $request->filter_company;
      if($request->filter_company == 5){ //Solid Trend
        $filter_company = 1;
      }
      $data->where('sap_connection_id',$filter_company);
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

    if($request->filter_product_class != ""){
      $data->where('item_class',$request->filter_product_class);
    }

    if($request->filter_product_type != ""){
      $data->where('u_item_type',$request->filter_product_type);
    }

    if($request->filter_product_application != ""){
      $data->where('u_item_application',$request->filter_product_application);
    }

    if($request->filter_product_pattern != ""){
      $data->where('u_pattern2',$request->filter_product_pattern);
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

      $data->whereHas('group', function($q){
        $q->whereNotIn('is_active', true);
      });

    }

    $data->when(!isset($request->order), function ($q) {
      $q->orderBy('created_date', 'desc');
    });

    

    return DataTables::of($data)
                          ->addIndexColumn()
                          ->addColumn('item_name', function($row) {
                            return @$row->item_name ?? "-";
                          })
                          ->addColumn('item_code', function($row) {
                            return @$row->item_code ?? "-";
                          })
                          ->addColumn('brand', function($row) {
                            return @$row->group->group_name ?? "-";
                          })
                          ->addColumn('u_item_line', function($row) {
                            return @$row->u_item_line ?? "-";
                          })
                          ->addColumn('u_tires', function($row) {
                            return @$row->u_tires ?? "-";
                          })
                          ->addColumn('item_class', function($row) {
                            return @$row->item_class ?? "-";
                          })
                          ->addColumn('u_item_type', function($row) {
                            return @$row->u_item_type ?? "-";
                          })
                          ->addColumn('u_item_application', function($row) {
                            return @$row->u_item_application ?? "-";
                          })
                          ->addColumn('u_pattern2', function($row) {
                            return @$row->u_pattern2 ?? "-";
                          })

                          ->addColumn('unit', function($row) {
                            return @$row->sales_unit ?? "-";
                          })
                          ->addColumn('product_technology', function($row) {
                            return @$row->u_product_tech ?? "-";
                          })

                          //Tires Category
                          ->addColumn('u_pattern_type', function($row) {
                            return @$row->u_pattern_type ?? "-";
                          })
                          ->addColumn('u_section_width', function($row) {
                            return @$row->u_section_width ?? "-";
                          })
                          ->addColumn('u_series', function($row) {
                            return @$row->u_series ?? "-";
                          })
                          ->addColumn('u_tire_diameter', function($row) {
                            return @$row->u_tire_diameter ?? "-";
                          })
                          ->addColumn('u_loadindex', function($row) {
                            return @$row->u_loadindex ?? "-";
                          })
                          ->addColumn('u_speed_symbol', function($row) {
                            return @$row->u_speed_symbol ?? "-";
                          })
                          ->addColumn('u_ply_rating', function($row) {
                            return @$row->u_ply_rating ?? "-";
                          })
                          ->addColumn('u_tire_const', function($row) {
                            return @$row->u_tire_const ?? "-";
                          })
                          ->addColumn('u_fitment_conf', function($row) {
                            return @$row->u_fitment_conf ?? "-";
                          })

                          // Battery Category
                          ->addColumn('u_blength', function($row) {
                            return @$row->u_blength ?? "-";
                          })
                          ->addColumn('u_bwidth', function($row) {
                            return @$row->u_bwidth ?? "-";
                          })
                          ->addColumn('u_bheight', function($row) {
                            return @$row->u_bheight ?? "-";
                          })
                          ->addColumn('u_bthicknes', function($row) {
                            return @$row->u_bthicknes ?? "-";
                          })
                          ->addColumn('u_brsvdcapacity', function($row) {
                            return @$row->u_brsvdcapacity ?? "-";
                          })
                          ->addColumn('u_bcoldcrankamps', function($row) {
                            return @$row->u_bcoldcrankamps ?? "-";
                          })
                          ->addColumn('u_bamperhour', function($row) {
                            return @$row->u_bamperhour ?? "-";
                          })
                          ->addColumn('u_bhandle', function($row) {
                            return @$row->u_bhandle ?? "-";
                          })
                          ->addColumn('u_bpolarity', function($row) {
                            return @$row->u_bpolarity ?? "-";
                          })
                          ->addColumn('u_bterminal', function($row) {
                            return @$row->u_bterminal ?? "-";
                          })
                          ->addColumn('u_bholddown', function($row) {
                            return @$row->u_bholddown ?? "-";
                          })
                          ->addColumn('u_bleadweight', function($row) {
                            return @$row->u_bleadweight ?? "-";
                          })
                          ->addColumn('u_btotalweight', function($row) {
                            return @$row->u_btotalweight ?? "-";
                          })

                          ->addColumn('created_date', function($row) {
                            return date('M d, Y',strtotime($row->created_date));
                          })
                          ->addColumn('company', function($row) use ($request){
                            if(@$request->filter_company == 5){ //Solid Trend
                              return "SOLID TREND";
                            }

                            return @$row->sap_connection->company_name ?? "-";
                          })
                          ->addColumn('online_price', function($row) {
                            $price = "-";
                            $item_prices = json_decode($row->item_prices,true);

                            if(count($item_prices) > 0){
                              $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                              $price = '₱ '.(@$prices[11]['Price'] ? @$prices[11]['Price'] : '0');
                            }

                            return $price;
                          })
                          ->addColumn('commercial_price', function($row) {
                            $price = "-";
                            $item_prices = json_decode($row->item_prices,true);

                            if(count($item_prices) > 0){
                              $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                              $price = '₱ '.(@$prices[12]['Price'] ? @$prices[12]['Price'] : '0');
                            }

                            return $price;
                          })
                          ->addColumn('srp_price', function($row) {
                            $price = "-";
                            $item_prices = json_decode($row->item_prices,true);

                            if(count($item_prices) > 0){
                              $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                              $price = '₱ '.(@$prices[13]['Price'] ? @$prices[13]['Price'] : '0');
                            }

                            return $price;
                          })
                          ->addColumn('rdlp_price', function($row) {
                            $price = "-";
                            $item_prices = json_decode($row->item_prices,true);

                            if(count($item_prices) > 0){
                              $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                              $price = '₱ '.(@$prices[14]['Price'] ? @$prices[14]['Price'] : '0');
                            }

                            return $price;
                          })
                          ->addColumn('rdlp2_price', function($row) {
                            $price = "-";
                            $item_prices = json_decode($row->item_prices,true);

                            if(count($item_prices) > 0){
                              $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                              $price = '₱ '.(@$prices[15]['Price'] ? @$prices[15]['Price'] : '0');
                            }

                            return $price;
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
                          ->orderColumn('item_class', function ($query, $order) {
                            $query->orderBy('item_class', $order);
                          })
                          ->orderColumn('u_item_type', function ($query, $order) {
                            $query->orderBy('u_item_type', $order);
                          })
                          ->orderColumn('u_item_application', function ($query, $order) {
                            $query->orderBy('u_item_application', $order);
                          })
                          ->orderColumn('u_pattern2', function ($query, $order) {
                            $query->orderBy('u_pattern2', $order);
                          })



                          ->orderColumn('unit', function ($query, $order) {
                            $query->orderBy('sales_unit', $order);
                          })

                          ->orderColumn('product_technology', function ($query, $order) {
                            $query->orderBy('u_product_tech', $order);
                          })

                          //Tires Category
                          ->orderColumn('u_pattern_type', function ($query, $order) {
                            $query->orderBy('u_pattern_type', $order);
                          })
                          ->orderColumn('u_section_width', function ($query, $order) {
                            $query->orderBy('u_section_width', $order);
                          })
                          ->orderColumn('u_series', function ($query, $order) {
                            $query->orderBy('u_series', $order);
                          })
                          ->orderColumn('u_tire_diameter', function ($query, $order) {
                            $query->orderBy('u_tire_diameter', $order);
                          })
                          ->orderColumn('u_loadindex', function ($query, $order) {
                            $query->orderBy('u_loadindex', $order);
                          })
                          ->orderColumn('u_speed_symbol', function ($query, $order) {
                            $query->orderBy('u_speed_symbol', $order);
                          })
                          ->orderColumn('u_ply_rating', function ($query, $order) {
                            $query->orderBy('u_ply_rating', $order);
                          })
                          ->orderColumn('u_tire_const', function ($query, $order) {
                            $query->orderBy('u_tire_const', $order);
                          })
                          ->orderColumn('u_fitment_conf', function ($query, $order) {
                            $query->orderBy('u_fitment_conf', $order);
                          })

                          // Battery Category
                          ->orderColumn('u_blength', function ($query, $order) {
                            $query->orderBy('u_blength', $order);
                          })
                          ->orderColumn('u_bwidth', function ($query, $order) {
                            $query->orderBy('u_bwidth', $order);
                          })
                          ->orderColumn('u_bheight', function ($query, $order) {
                            $query->orderBy('u_bheight', $order);
                          })
                          ->orderColumn('u_bthicknes', function ($query, $order) {
                            $query->orderBy('u_bthicknes', $order);
                          })
                          ->orderColumn('u_brsvdcapacity', function ($query, $order) {
                            $query->orderBy('u_brsvdcapacity', $order);
                          })
                          ->orderColumn('u_bcoldcrankamps', function ($query, $order) {
                            $query->orderBy('u_bcoldcrankamps', $order);
                          })
                          ->orderColumn('u_bamperhour', function ($query, $order) {
                            $query->orderBy('u_bamperhour', $order);
                          })
                          ->orderColumn('u_bhandle', function ($query, $order) {
                            $query->orderBy('u_bhandle', $order);
                          })
                          ->orderColumn('u_bpolarity', function ($query, $order) {
                            $query->orderBy('u_bpolarity', $order);
                          })
                          ->orderColumn('u_bterminal', function ($query, $order) {
                            $query->orderBy('u_bterminal', $order);
                          })
                          ->orderColumn('u_bholddown', function ($query, $order) {
                            $query->orderBy('u_bholddown', $order);
                          })
                          ->orderColumn('u_bleadweight', function ($query, $order) {
                            $query->orderBy('u_bleadweight', $order);
                          })
                          ->orderColumn('u_btotalweight', function ($query, $order) {
                            $query->orderBy('u_btotalweight', $order);
                          })

                          ->orderColumn('created_date', function ($query, $order) {
                            $query->orderBy('created_date', $order);
                          })
                          ->orderColumn('status', function ($query, $order) {
                            $query->orderBy('is_active', $order);
                          })
                          ->orderColumn('brand', function ($query, $order) {
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


  public function export(Request $request){
    $filter = collect();
    if(@$request->data){
      $filter = json_decode(base64_decode($request->data));
    }

    $data = Product::orderBy('created_date', 'desc');

    if(@$filter->filter_status != ""){
      $data->where('is_active',$filter->filter_status);
    }

    /*if(@$filter->filter_company != ""){
      $data->where('sap_connection_id',$filter->filter_company);
    }*/

    if($filter->filter_company != ""){
      $filter_company = $filter->filter_company;
      if($filter->filter_company == 5){ //Solid Trend
        $filter_company = 1;
      }
      $data->where('sap_connection_id',$filter_company);
    }

    if(@$filter->filter_brand != ""){
      $data->where('items_group_code',$filter->filter_brand);
    }

    if(@$filter->filter_product_category != ""){
      $data->where('u_tires',$filter->filter_product_category);
    }

    if(@$filter->filter_product_line != ""){
      $data->where('u_item_line',$filter->filter_product_line);
    }

    if(@$filter->filter_product_class != ""){
      $data->where('item_class',$filter->filter_product_class);
    }

    if(@$filter->filter_product_type != ""){
      $data->where('u_item_type',$filter->filter_product_type);
    }

    if(@$filter->filter_product_application != ""){
      $data->where('u_item_application',$filter->filter_product_application);
    }

    if(@$filter->filter_product_pattern != ""){
      $data->where('u_pattern2',$filter->filter_product_pattern);
    }

    if(@$filter->filter_search != ""){
      $data->where(function($q) use ($request) {
        $q->orwhere('item_code','LIKE',"%".$filter->filter_search."%");
        $q->orwhere('item_name','LIKE',"%".$filter->filter_search."%");
      });
    }

    if(@$filter->filter_date_range != ""){
      $date = explode(" - ", $filter->filter_date_range);
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

    $data = $data->get();

    $product_category = strtolower(@$filter->filter_product_category);

    $records = array();
    foreach($data as $key => $value){

      $item_prices = json_decode($value->item_prices,true);
      $prices = array();
      if(count($item_prices) > 0){
        $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
      }

      if(@$filter->module_type != "product-tagging"){

        $filter_company = @$value->sap_connection->company_name ?? "-";
        if($filter->filter_company == 5){ //Solid Trend
          $filter_company = 'SOLID TREND';
        }

        $temp = array(
                  'no' => $key + 1,
                  'company' => $filter_company,
                  'item_name' => $value->item_name ?? "-",
                  'brand' => @$value->group->group_name ?? "",
                  'item_code' => $value->item_code ?? "-",
                  'product_line' => $value->u_item_line ?? "-",
                  'product_category' => $value->u_tires ?? "-",
                );


        // Shows Product Class
        if(in_array($product_category, ["lubes","chem","tires"])){
          $temp['item_class'] = $value->item_class ?? "-";
        }

        // Shows Product Pattern
        if(in_array($product_category, ["tires"])){
          $temp['u_pattern2'] = $value->u_pattern2 ?? "-";
        }

        $temp['created_at'] = date('M d, Y',strtotime($value->created_date));
        $temp['status'] = $value->is_active ? "Active" : "Inctive";
        $temp['online_price'] = @$prices[11]['Price'] ?? "-";
        $temp['commercial_price'] = @$prices[12]['Price'] ?? "-";
        $temp['srp_price'] = @$prices[13]['Price'] ?? "-";
        $temp['rdlp_price'] = @$prices[14]['Price'] ?? "-";
        $temp['rdlp2_price'] = @$prices[15]['Price'] ?? "-";

        $records[] = $temp;
      }else{

        $filter_company = @$value->sap_connection->company_name ?? "-";
        if($filter->filter_company == 5){ //Solid Trend
          $filter_company = 'SOLID TREND';
        }
        
        $temp = array(
                  'no' => $key + 1,
                  'company' => $filter_company,
                  'item_name' => $value->item_name ?? "-",
                  'brand' => @$value->group->group_name ?? "-",
                  'item_code' => $value->item_code ?? "-",
                  'product_line' => $value->u_item_line ?? "-",
                  'product_category' => $value->u_tires ?? "-",
                  'unit' => $value->sales_unit ?? "-",
                  'rdlp_price' => @$prices[14]['Price'] ?? "-",
                  'commercial_price' => @$prices[12]['Price'] ?? "-",
                  'srp_price' => @$prices[13]['Price'] ?? "-",
                  'product_application' => $value->u_item_application ?? "-",
                  'product_type' => $value->u_item_type ?? "-",
                );



        // Hide Product Type
        if(in_array($product_category, ["tires"])){
          unset($temp['product_type']);
        }

        // Shows Product Class
        if(in_array($product_category, ["lubes","tires"])){
          $temp['item_class'] = $value->item_class ?? "-";
        }

        // Shows Product Pattern
        if(in_array($product_category, ["tires"])){
          $temp['u_pattern2'] = $value->u_pattern2 ?? "-";
        }

        // Shows Product Technology
        if(in_array($product_category, ["battery","autoparts"])){
          $temp['product_technology'] = $value->u_product_tech ?? "-";
        }

        // Shows Tires Field
        if(in_array($product_category, ["tires"])){
          $temp['u_pattern_type'] = $value->u_pattern_type ?? "-";
          $temp['u_section_width'] = $value->u_section_width ?? "-";
          $temp['u_series'] = $value->u_series ?? "-";
          $temp['u_tire_diameter'] = $value->u_tire_diameter ?? "-";
          $temp['u_loadindex'] = $value->u_loadindex ?? "-";
          $temp['u_speed_symbol'] = $value->u_speed_symbol ?? "-";
          $temp['u_ply_rating'] = $value->u_ply_rating ?? "-";
          $temp['u_tire_const'] = $value->u_tire_const ?? "-";
          $temp['u_fitment_conf'] = $value->u_fitment_conf ?? "-";
        }

        // Shows Battery Field
        if(in_array($product_category, ["battery"])){
          $temp['u_blength'] = $value->u_blength ?? "-";
          $temp['u_bwidth'] = $value->u_bwidth ?? "-";
          $temp['u_bheight'] = $value->u_bheight ?? "-";
          $temp['u_bthicknes'] = $value->u_bthicknes ?? "-";
          $temp['u_brsvdcapacity'] = $value->u_brsvdcapacity ?? "-";
          $temp['u_bcoldcrankamps'] = $value->u_bcoldcrankamps ?? "-";
          $temp['u_bamperhour'] = $value->u_bamperhour ?? "-";
          $temp['u_bhandle'] = $value->u_bhandle ?? "-";
          $temp['u_bpolarity'] = $value->u_bpolarity ?? "-";
          $temp['u_bterminal'] = $value->u_bterminal ?? "-";
          $temp['u_bholddown'] = $value->u_bholddown ?? "-";
          $temp['u_bleadweight'] = $value->u_bleadweight ?? "-";
          $temp['u_btotalweight'] = $value->u_btotalweight ?? "-";
        }

        $records[] = $temp;
      }
    }

    $headers = array();
    // For Headers
    if(@$filter->module_type != "product-tagging"){
      $headers = array(
                      'No.',
                      'Business Unit',
                      'Product Name',
                      'Product Brand',
                      'Product Code',
                      'Product Line',
                      'Product Category'
                    );


      // Shows Product Class
      if(in_array($product_category, ["lubes","chem","tires"])){
        array_push($headers, 'Product Class');
      }

      // Shows Product Pattern
      if(in_array($product_category, ["tires"])){
        array_push($headers, 'Product Pattern');
      }

      array_push($headers, 'Created Date');
      array_push($headers, 'Status');
      array_push($headers, 'Online Price');
      array_push($headers, 'Commercial Price');
      array_push($headers, 'SRP');
      array_push($headers, 'RDLP');
      array_push($headers, 'RDLP-2');


    }else{
      $headers = array(
                'No.',
                'Product Code',
                'Product Name',
                'Brand',
                'Product Category',
                'Business Unit',
                'Product Line',
                'Unit',
                'RDLP',
                'Commercial Price',
                'SRP',
                'Product Application',
                'Product Type',
              );



      // Hide Product Type
      if(in_array($product_category, ["tires"])){
        $key = array_search('Product Type', $headers);
        unset($headers[$key]);
      }

      // Shows Product Class
      if(in_array($product_category, ["lubes","tires"])){
        array_push($headers, 'Product Class');
      }

      // Shows Product Pattern
      if(in_array($product_category, ["tires"])){
        array_push($headers, 'Pattern');
      }

      // Shows Product Technology
      if(in_array($product_category, ["battery","autoparts"])){
        array_push($headers, 'Product Technology');
      }

      // Shows Tires Field
      if(in_array($product_category, ["tires"])){
        array_push($headers, 'Tread Pattern Type');
        array_push($headers, 'Section Width');
        array_push($headers, 'Series');
        array_push($headers, 'Tire Diameter');
        array_push($headers, 'Load Index');
        array_push($headers, 'Speed Symbol');
        array_push($headers, 'Ply Rating');
        array_push($headers, 'Tire Construction');
        array_push($headers, 'Fitment Configuration');
      }

      // Shows Battery Field
      if(in_array($product_category, ["battery"])){
        array_push($headers, 'L');
        array_push($headers, 'W');
        array_push($headers, 'H');
        array_push($headers, 'TH');
        array_push($headers, 'RC');
        array_push($headers, 'CCA');
        array_push($headers, 'AH');
        array_push($headers, 'Handle');
        array_push($headers, 'Polarity');
        array_push($headers, 'Terminal');
        array_push($headers, 'Hold down');
        array_push($headers, 'Lead Weight(kg)');
        array_push($headers, 'Total Weight(kg)');
      }
    }

    // start from here
    if(count($records)){
      if(@$filter->module_type == "product-tagging"){
        $title = 'Product Tagging Report '.date('dmY').'.xlsx';
        return Excel::download(new ProductTaggingExport($records, $headers), $title);
      }else{
        $title = 'Product Report '.date('dmY').'.xlsx';
        return Excel::download(new ProductExport($records, $headers), $title);
      }
    }

    \Session::flash('error_message', common_error_msg('excel_download'));
    return redirect()->back();
  }

  public function getBrandData(Request $request){
    $data = collect();

    if(@$request->sap_connection_id != ""){

      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = ProductGroup::where('sap_connection_id', $sap_connection_id)->orderby('group_name')->where('is_active', true)->limit(50);

      $data->whereNotIn('group_name', ['Items', 'MKTG. MATERIALS', 'OFFICIAL DOCUMENT']);

      if(@$request->search  != ''){
        $data->where('group_name', 'like', '%' .$request->search . '%');
      }

      $data = $data->get();
    }

    return response()->json($data);
  }

  public function getProductCategoryData(Request $request){
    $data = collect();

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('u_tires')->orderby('u_tires')->limit(50);

      if(@$request->search  != ''){
        $data->where('u_tires', 'like', '%' .$request->search . '%');
      }

      $data = $data->get()->groupBy('u_tires');
    }

    return response()->json($data);
  }


  public function getProductLineData(Request $request){
    $data = collect();

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('u_item_line')->orderby('u_item_line')->limit(50);

      if(@$request->search  != ''){
        $data->where('u_item_line', 'like', '%' .$request->search . '%');
      }

      $data = $data->get()->groupBy('u_item_line');
    }

    return response()->json($data);
  }


  public function getProductClassData(Request $request){
    $data = collect();

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('item_class')->orderby('item_class')->limit(50);

      if(@$request->search  != ''){
        $data->where('item_class', 'like', '%' .$request->search . '%');
      }

      $data = $data->get()->groupBy('item_class');
    }

    return response()->json($data);
  }


  public function getProductTypeData(Request $request){
    $data = collect();

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('u_item_type')->orderby('u_item_type')->limit(50);

      if(@$request->search  != ''){
        $data->where('u_item_type', 'like', '%' .$request->search . '%');
      }

      $data = $data->get()->groupBy('u_item_type');
    }

    return response()->json($data);
  }


  public function getProductApplicationData(Request $request){
    $data = collect();

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('u_item_application')->orderby('u_item_application')->limit(50);

      if(@$request->search  != ''){
        $data->where('u_item_application', 'like', '%' .$request->search . '%');
      }

      $data = $data->get()->groupBy('u_item_application');
    }

    return response()->json($data);
  }


  public function getProductPatternData(Request $request){
    $data = collect();

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('u_pattern2')->orderby('u_pattern2')->limit(50);

      if(@$request->search  != ''){
        $data->where('u_pattern2', 'like', '%' .$request->search . '%');
      }

      $data = $data->get()->groupBy('u_pattern2');
    }

    return response()->json($data);
  }


  public function productTaggingIndex(){
    $company = SapConnection::all();

    return view('product.tagging',compact('company'));
  }

}
