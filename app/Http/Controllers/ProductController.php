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
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use App\Exports\ProductTaggingExport;
use App\Models\Customer; //added for sap connection only
use App\Models\CustomerProductGroup;
use App\Models\ProductBenefits;
use App\Models\RecommendedProduct;
use App\Models\RecommendedProductAssignment;
use App\Models\RecommendedProductItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
      $recom_id = decrypt($id);
      $response = $this->addRecommendedProducts($request, $recom_id, 'update');

      return $response;
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

  public function syncProducts(Request $request){
    try {      

      // // Add sync Product data log.
      // add_log(18, null);

      // // Save Data of Product in database
      // SyncProducts::dispatch('TEST-APBW', 'manager', 'test');
      if($request->filter_company != ""){
        $sap_connections = SapConnection::where('id', $request->filter_company)->first();

        $log_id = add_sap_log([
                                  'ip_address' => userip(),
                                  'activity_id' => 18,
                                  'user_id' => userid(),
                                  'data' => null,
                                  'type' => "S",
                                  'status' => "in progress",
                                  'sap_connection_id' => $sap_connections->id,
                              ]);
        SyncProducts::dispatch($sap_connections->db_name, $sap_connections->user_name , $sap_connections->password, $log_id, $request->filter_search);

      }else{
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
          SyncProducts::dispatch($value->db_name, $value->user_name , $value->password, $log_id, $request->filter_search);
        }
      }

      $response = ['status' => true, 'message' => 'Sync Product successfully !'];
    } catch (\Exception $e) {
      $response = ['status' => false, 'message' => 'Something went wrong !'];
    }
    return $response;
  }

  public function getAll(Request $request){

    $data = Product::whereRaw('last_sync_at > "2023-03-27 09:39:36"')->whereHas('sap_connection',function($q){
              $q->WhereNull('deleted_at');
            });

    if($request->filter_status != ""){
      $data->where('is_active',$request->filter_status);
    }

    if($request->filter_company != ""){
      $filter_company = $request->filter_company;
      if($request->filter_company == 5){ //Solid Trend
        $filter_company = 1;
      }
      $data->where('products.sap_connection_id',$filter_company);
    }

    if($request->filter_brand != ""){
      $data->where('products.items_group_code',$request->filter_brand);
    }

    if($request->filter_product_category != ""){
      $data->where('products.u_tires',$request->filter_product_category);
    }

    if($request->filter_product_line != ""){
      $data->where('products.u_item_line',$request->filter_product_line);
    }

    if($request->filter_product_class != ""){
      $data->where('products.item_class',$request->filter_product_class);
    }

    if($request->filter_product_type != ""){
      $data->where('products.u_item_type',$request->filter_product_type);
    }

    if($request->filter_product_application != ""){
      $data->where('products.u_item_application',$request->filter_product_application);
    }

    if($request->filter_product_pattern != ""){
      $data->where('products.u_pattern2',$request->filter_product_pattern);
    }

    if($request->filter_search != ""){
      $data->where(function($q) use ($request) {
        $q->orwhere('products.item_code','LIKE',"%".$request->filter_search."%");
        $q->orwhere('products.item_name','LIKE',"%".$request->filter_search."%");
      });
    }

    if($request->filter_date_range != ""){
      $date = explode(" - ", $request->filter_date_range);
      $start = date("Y-m-d", strtotime($date[0]));
      $end = date("Y-m-d", strtotime($date[1]));

      $data->whereDate('created_date', '>=' , $start);
      $data->whereDate('created_date', '<=' , $end);
    }


    if(!in_array(userrole(),[1,11])){
      $data->where('products.is_active', true);

      if(@Auth::user()->sap_connection_id){
        $data->where('products.sap_connection_id', @Auth::user()->sap_connection_id);
      }

      $data->whereHas('group', function($q){
        $q->where('is_active', true);
      });

    }

    $data->when(!isset($request->order), function ($q) {
      $q->orderBy('products.created_date', 'desc');
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
                            return @$row->u_item_line_sap_value->value ?? @$row->u_item_line ?? "-";
                          })
                          ->addColumn('u_tires', function($row) {
                            return @$row->u_tires ?? "-";
                          })
                          ->addColumn('item_class', function($row) {
                            return @$row->item_class ?? "-";
                          })
                          ->addColumn('u_item_type', function($row) {
                            return @$row->u_item_type_sap_value->value ?? @$row->u_item_type ?? "-";
                          })
                          ->addColumn('u_item_application', function($row) {
                            return @$row->u_item_application_sap_value->value ?? @$row->u_item_application ?? "-";
                          })
                          ->addColumn('u_pattern2', function($row) {
                            return @$row->u_pattern2_sap_value->value ?? @$row->u_pattern2 ?? "-" ;
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
                          ->addColumn('price1', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[1]['Currency'] ? get_product_currency(@$prices[1]['Currency']) : '₱');
                                $price = (@$prices[1]['Price'] ? @$prices[1]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price2', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[2]['Currency'] ? get_product_currency(@$prices[2]['Currency']) : '₱');
                                $price = (@$prices[2]['Price'] ? @$prices[2]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price3', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[3]['Currency'] ? get_product_currency(@$prices[3]['Currency']) : '₱');
                                $price = (@$prices[3]['Price'] ? @$prices[3]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price4', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[4]['Currency'] ? get_product_currency(@$prices[4]['Currency']) : '₱');
                                $price = (@$prices[4]['Price'] ? @$prices[4]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price5', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[5]['Currency'] ? get_product_currency(@$prices[5]['Currency']) : '₱');
                                $price = (@$prices[5]['Price'] ? @$prices[5]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price6', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[6]['Currency'] ? get_product_currency(@$prices[6]['Currency']) : '₱');
                                $price = (@$prices[6]['Price'] ? @$prices[6]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price7', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[7]['Currency'] ? get_product_currency(@$prices[7]['Currency']) : '₱');
                                $price = (@$prices[7]['Price'] ? @$prices[7]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price8', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[8]['Currency'] ? get_product_currency(@$prices[8]['Currency']) : '₱');
                                $price = (@$prices[8]['Price'] ? @$prices[8]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price9', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[9]['Currency'] ? get_product_currency(@$prices[9]['Currency']) : '₱');
                                $price = (@$prices[9]['Price'] ? @$prices[9]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price10', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[10]['Currency'] ? get_product_currency(@$prices[10]['Currency']) : '₱');
                                $price = (@$prices[10]['Price'] ? @$prices[10]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price11', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[11]['Currency'] ? get_product_currency(@$prices[11]['Currency']) : '₱');
                                $price = (@$prices[11]['Price'] ? @$prices[11]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price12', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[12]['Currency'] ? get_product_currency(@$prices[12]['Currency']) : '₱');
                                $price = (@$prices[12]['Price'] ? @$prices[12]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price13', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[13]['Currency'] ? get_product_currency(@$prices[13]['Currency']) : '₱');
                                $price = (@$prices[13]['Price'] ? @$prices[13]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price14', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[14]['Currency'] ? get_product_currency(@$prices[14]['Currency']) : '₱');
                                $price = (@$prices[14]['Price'] ? @$prices[14]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price15', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[15]['Currency'] ? get_product_currency(@$prices[15]['Currency']) : '₱');
                                $price = (@$prices[15]['Price'] ? @$prices[15]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price16', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[16]['Currency'] ? get_product_currency(@$prices[16]['Currency']) : '₱');
                                $price = (@$prices[16]['Price'] ? @$prices[16]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price17', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[17]['Currency'] ? get_product_currency(@$prices[17]['Currency']) : '₱');
                                $price = (@$prices[17]['Price'] ? @$prices[17]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price18', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[18]['Currency'] ? get_product_currency(@$prices[18]['Currency']) : '₱');
                                $price = (@$prices[18]['Price'] ? @$prices[18]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price19', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[19]['Currency'] ? get_product_currency(@$prices[19]['Currency']) : '₱');
                                $price = (@$prices[19]['Price'] ? @$prices[19]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
                          })
                          ->addColumn('price20', function($row) {
                              $price = "0";
                              $item_prices = json_decode($row->item_prices,true);

                              if(count($item_prices) > 0){
                                $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                                $currency = (@$prices[20]['Currency'] ? get_product_currency(@$prices[20]['Currency']) : '₱');
                                $price = (@$prices[20]['Price'] ? @$prices[20]['Price'] : '0');
                              }

                              return $currency.' '.number_format($price, 2);
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
                          ->addColumn('lp_price', function($row) {
                            $price = "-";
                            $item_prices = json_decode($row->item_prices,true);

                            if(count($item_prices) > 0){
                              $prices = array_combine(array_column($item_prices, 'PriceList'), array_values($item_prices));
                              $price = '₱ '.(@$prices[16]['Price'] ? @$prices[16]['Price'] : '0');
                            }

                            return $price;
                          })
                          ->addColumn('status', function($row) {

                            $btn = "";
                            if($row->is_active){
                              $btn .= '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                            }else{
                              $btn .= '<a href="javascript:" class="btn btn-sm btn-light-danger btn-inline status">Inactive</a>';
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
     ini_set('memory_limit', '1024M');
     ini_set('max_execution_time', 1800);
    $filter = collect();
    if(@$request->data){
      $filter = json_decode(base64_decode($request->data));
    }

    $data = Product::whereRaw('last_sync_at > "2023-03-27 09:39:36"')->orderBy('created_date', 'desc');

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
      $data->where('products.sap_connection_id',$filter_company);
    }

    if(@$filter->filter_brand != ""){
      $data->where('products.items_group_code',$filter->filter_brand);
    }

    if(@$filter->filter_product_category != ""){
      $data->where('products.u_tires',$filter->filter_product_category);
    }

    if(@$filter->filter_product_line != ""){
      $data->where('products.u_item_line',$filter->filter_product_line);
    }

    if(@$filter->filter_product_class != ""){
      $data->where('products.item_class',$filter->filter_product_class);
    }

    if(@$filter->filter_product_type != ""){
      $data->where('products.u_item_type',$filter->filter_product_type);
    }

    if(@$filter->filter_product_application != ""){
      $data->where('products.u_item_application',$filter->filter_product_application);
    }

    if(@$filter->filter_product_pattern != ""){
      $data->where('products.u_pattern2',$filter->filter_product_pattern);
    }

    if(@$filter->filter_search != ""){
      $data->where(function($q) use ($filter) {
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
      $data->where('products.is_active', true);

      if(@Auth::user()->sap_connection_id){
        $data->where('products.sap_connection_id', @Auth::user()->sap_connection_id);
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
                  'product_line' => @$value->u_item_line_sap_value->value ?? @$value->u_item_line ?? "-",
                  'product_category' => $value->u_tires ?? "-",
                );


        // Shows Product Class
        if(in_array($product_category, ["lubes","chem","tires"])){
          $temp['item_class'] = $value->item_class ?? "-";
        }

        // Shows Product Pattern
        if(in_array($product_category, ["tires"])){
          $temp['u_pattern2'] = @$value->u_pattern2_sap_value->value ?? $value->u_pattern2 ?? "-";
        }

        $temp['created_at'] = date('M d, Y',strtotime($value->created_date));
        $temp['status'] = $value->is_active ? "Active" : "Inactive";

        //start diri ang customization
        foreach(@$filter->priceLists as $pl){
          $currency = (@$prices[$pl->no]['Currency'] ? get_product_currency(@$prices[$pl->no]['Currency']) : '₱');
          $price = (@$prices[$pl->no]['Price'] ? @$prices[$pl->no]['Price'] : '0');
          // $temp['price'.$pl->no] = @$prices[$pl->no]['Price'] ?? "-";
          $temp['price'.$pl->no] = $currency.' '.number_format($price, 2);
        }

        // $temp['online_price'] = @$prices[11]['Price'] ?? "-";
        // $temp['commercial_price'] = @$prices[12]['Price'] ?? "-";
        // $temp['srp_price'] = @$prices[13]['Price'] ?? "-";
        // $temp['rdlp_price'] = @$prices[14]['Price'] ?? "-";
        // $temp['rdlp2_price'] = @$prices[15]['Price'] ?? "-";
        // $temp['lp_price'] = @$prices[16]['Price'] ?? "-";

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
                  'product_line' => @$value->u_item_line_sap_value->value ?? $value->u_item_line ?? "-",
                  'product_category' => $value->u_tires ?? "-",
                  'unit' => $value->sales_unit ?? "-",
                  'rdlp_price' => @$prices[14]['Price'] ?? "-",
                  'commercial_price' => @$prices[12]['Price'] ?? "-",
                  'srp_price' => @$prices[13]['Price'] ?? "-",
                  'product_application' => @$value->u_item_application_sap_value->value ?? $value->u_item_application ?? "-",
                  'product_type' => @$value->u_item_type_sap_value->value ?? $value->u_item_type ?? "-",
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
          $temp['u_pattern2'] = @$value->u_pattern2_sap_value->value ?? $value->u_pattern2 ?? "-";
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
    } //end of foreach

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

      foreach(@$filter->priceLists as $pl){
        array_push($headers, $pl->name);
      }

      // array_push($headers, 'Online Price');
      // array_push($headers, 'Commercial Price');
      // array_push($headers, 'SRP');
      // array_push($headers, 'DLP');
      // array_push($headers, 'Gross Price');
      // array_push($headers, 'LP');


    }else{
      $headers = array(
                'No.',
                'Business Unit',
                'Product Name',
                'Brand',
                'Product Code',
                'Product Line',
                'Product Category',
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
    $c_product_group = [];
    $customer_id = [];
    $sap_conn_id = $request->sap_connection_id;
    if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
      if($request->sap_connection_id == "" && $request->filter_customer == ""){
          return response()->json($data);
      }
    }else{
      $customer_id = explode(',', Auth::user()->multi_customer_id);
      $user = User::where('id',Auth::id())->first();
      $request->sap_connection_id = $user->sap_connection_id;
    }

    if($request->filter_customer != ""){
      $customer = Customer::find($request->filter_customer);
      $customer_id = [$customer->id];
      $sap_connection = $customer->sap_connection;
      $request->sap_connection_id = $sap_connection->id;
    }

    $c_product_group = CustomerProductGroup::with('product_group')->whereIn('customer_id', $customer_id)->get();
    $c_product_group = array_column( $c_product_group->toArray(), 'product_group_id' );

    if($sap_conn_id != ""){

      $sap_connection_id = $sap_conn_id;
      if($sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = ProductGroup::query();
      if(Auth::user()->role_id !== 1){ //distributor
        $data->whereIn('id', $c_product_group);
      }else{
        $data->where('sap_connection_id', $sap_connection_id);
      }
      $data->orderby('group_name')->where('is_active', true)->limit(50);

      $data->whereNotIn('group_name', ['Items', 'MKTG. MATERIALS', 'OFFICIAL DOCUMENT']);

      if(@$request->search  != ''){
        $data->where('group_name', 'like', '%' .$request->search . '%');
      }

      $data = $data->get();
    }

    return response()->json($data);
  }

  public function getProductCategoryData(Request $request){
    $response = array();
    if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
        if($request->sap_connection_id == ""){
            return response()->json($response);
        }
    }else{
        $user = User::where('id',Auth::id())->first();
        $request->sap_connection_id = $user->sap_connection_id;
    }

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('u_tires')->orderby('u_tires')->limit(50);

      if(@$request->search  != ''){
        $data->where('u_tires', 'like', '%' .$request->search . '%');
      }

      $data = $data->groupBy('u_tires')->get();

      foreach($data as $value){
        $response[] = array(
          "id" => @$value->u_tires,
          "text" => @$value->u_tires,
        );
      }
    }

    return response()->json($response);
  }


  public function getProductLineData(Request $request){
    $response = array();
    if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
        if($request->sap_connection_id == ""){
            return response()->json($response);
        }
    }else{
        $user = User::where('id',Auth::id())->first();
        $request->sap_connection_id = $user->sap_connection_id;
    }

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('u_item_line')->orderby('u_item_line')->limit(50);

      if(@$request->search  != ''){
        $data->where('u_item_line', 'like', '%' .$request->search . '%');
      }

      $data = $data->groupBy('u_item_line')->get();

      foreach($data as $value){
        $response[] = array(
          "id" => @$value->u_item_line,
          "text" => @$value->u_item_line_sap_value->value ?? @$value->u_item_line,
        );
      }
    }

    return response()->json($response);
  }


  public function getProductClassData(Request $request){
    $response = array();
    if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
        if($request->sap_connection_id == ""){
            return response()->json($response);
        }
    }else{
        $user = User::where('id',Auth::id())->first();
        $request->sap_connection_id = $user->sap_connection_id;
    }

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('item_class')->orderby('item_class')->limit(50);

      if(@$request->search  != ''){
        $data->where('item_class', 'like', '%' .$request->search . '%');
      }

      $data = $data->groupBy('item_class')->get();

      foreach($data as $value){
        $response[] = array(
          "id" => @$value->item_class,
          "text" => @$value->item_class,
        );
      }

    }

    return response()->json($response);
  }


  public function getProductTypeData(Request $request){
    $response = array();
    if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
        if($request->sap_connection_id == ""){
            return response()->json($response);
        }
    }else{
        $user = User::where('id',Auth::id())->first();
        $request->sap_connection_id = $user->sap_connection_id;
    }

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('u_item_type')->orderby('u_item_type')->limit(50);

      if(@$request->search  != ''){
        $data->where('u_item_type', 'like', '%' .$request->search . '%');
      }

      $data = $data->groupBy('u_item_type')->get();

      foreach($data as $value){
        $response[] = array(
          "id" => @$value->u_item_type,
          "text" => @$value->u_item_type_sap_value->value ?? @$value->u_item_type,
        );
      }

    }

    return response()->json($response);
  }


  public function getProductApplicationData(Request $request){
    $response = array();
    if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
        if($request->sap_connection_id == ""){
            return response()->json($response);
        }
    }else{
        $user = User::where('id',Auth::id())->first();
        $request->sap_connection_id = $user->sap_connection_id;
    }

    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('u_item_application')->orderby('u_item_application')->limit(50);

      if(@$request->search  != ''){
        $data->where('u_item_application', 'like', '%' .$request->search . '%');
      }

      $data = $data->groupBy('u_item_application')->get();

      foreach($data as $value){
        $response[] = array(
          "id" => @$value->u_item_application,
          "text" => @$value->u_item_application_sap_value->value ?? @$value->u_item_application,
        );
      }
    }

    return response()->json($response);
  }


  public function getProductPatternData(Request $request){
    $response = array();
    if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
        if($request->sap_connection_id == ""){
            return response()->json($response);
        }
    }else{
        $user = User::where('id',Auth::id())->first();
        $request->sap_connection_id = $user->sap_connection_id;
    }
    
    if(@$request->sap_connection_id != "" && @$request->items_group_code != ""){
      
      $sap_connection_id = $request->sap_connection_id;
      if($request->sap_connection_id == 5){ //Solid Trend
        $sap_connection_id = 1;
      }

      $data = Product::where('is_active', true)->where('items_group_code', $request->items_group_code)->where('sap_connection_id', $sap_connection_id)->whereNotNull('u_pattern2')->orderby('u_pattern2')->limit(50);

      if(@$request->search  != ''){
        $data->whereHas('u_pattern2_sap_value', function($q) use ($request){
          $q->where('value', 'like', '%' .$request->search . '%');
        });
      }

      $data = $data->groupBy('u_pattern2')->get();

      foreach($data as $value){
        $response[] = array(
          "id" => @$value->u_pattern2,
          "text" => @$value->u_pattern2_sap_value->value ?? @$value->u_pattern2,
        );
      }

    }

    return response()->json($response);
  }


  public function productTaggingIndex(){
    $company = SapConnection::all();

    return view('product.tagging',compact('company'));
  }

  public function getRecommendedProductLists(){
    $company = SapConnection::all();
    return view('product.recommend',compact('company'));
  }

  public function createRecommendedProducts(){
    $company = SapConnection::all();
    return view('product.recommend-create',compact('company'));
  }

  public function productPriceLists(Request $request){

    $sap_connections = SapConnection::where('id', $request->filter_company)->first();
    $sap_priceLists = new \App\Support\SAPCustomer($sap_connections->db_name, $sap_connections->user_name , $sap_connections->password, false, '');
    $priceRecord = $sap_priceLists->fetchPriceLists();
    $priceLists = [];
    foreach($priceRecord['value'] as $price){
        $priceLists[$price['PriceListNo']] = $price['PriceListName'];
    }

    return $priceLists;
  }


  public function fetchProducts(Request $request){
    $search = $request->search;
    $sap_connection_id = $request->sap_connection_id;

    $response = array();
    if($sap_connection_id){
        if($sap_connection_id == 5){
            $sap_connection_id = 1;
        }

        $data = Product::orderby('item_name','asc')->where('sap_connection_id', $sap_connection_id)
                                                   ->where('is_active', true)
                                                   ->select('id', 'item_code', 'item_name')->limit(50);
        if($search != ''){
          $data->where(function($q) use ($search) {
            $q->orWhere('item_code', 'like', '%' .$search . '%');
            $q->orWhere('item_name', 'like', '%' .$search . '%');
          });
        }

        $data = $data->get();

        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=> '('.$value->item_code.') '.$value->item_name
            );
        }
    }

    return response()->json($response);
  }

  public function addRecommendedProducts(Request $request, $recom_id = null, $update = null){
    // dd($recom_id, $update, $request->all());
    $response = array();
    $input = $request->all();
    $rules = array(
                'sap_connection_id' => 'required',
                'product_ids' => 'required'
            );
    $validator = Validator::make($input, $rules);

    if ($validator->fails()) {
      $response = ['status'=>false,'message'=>$validator->errors()->first()];
    }else{ //success

      $cust_class = ($input['module'] == 'customer_class') ? $request->select_class_customer : NULL;
      $rec_ids = '';
      $count = 0;
      if(isset($input['record_id']) && count($input['record_id']) > 0 ){
        foreach($input['record_id'] as $id){
          $comma = ($count > 0) ? ',' : '';
          if(strpos($rec_ids, $id) === false){
            $rec_ids .= $comma.$id;
          }
          $count ++;
        }

      }

      if($update === 'update'){
        // dd($input['module']);
        $recommend = (object)[];
        $recommend->id = $recom_id;
        RecommendedProduct::where('id', $recom_id)->update([
          'b_unit' => $input['sap_connection_id'],
          'title' => $input['title'],
          'module' => $input['module'],
          'cust_select' => $cust_class,
          'ids' => $rec_ids,
        ]);
        RecommendedProductAssignment::where('assignment_id', $recom_id)->delete();
        RecommendedProductItem::where('assignment_id', $recom_id)->delete();
      }else{
        $recommend = new RecommendedProduct();
        $recommend->b_unit = $input['sap_connection_id'];
        $recommend->title = $input['title'];
        $recommend->module = $input['module'];
        $recommend->cust_select = $cust_class;
        $recommend->ids = $rec_ids;
        $recommend->save();
      }

      $product = [];
      $count = 0;
      foreach($input['product_ids'] as $id){
        $product[$count]['assignment_id'] = $recommend->id;
        $product[$count]['product_id'] = $id;
        $product[$count]['created_at'] = Carbon::now();
        $count++;
      }
      RecommendedProductItem::insert($product);

      if($input['module'] == 'all'){
        $data = Customer::where('is_active', '1')->where('sap_connection_id', $input['sap_connection_id'])->get();

        $connection = [];
        $counter = 0;
        foreach($data as $customer){
            $user = @$customer->user;
            if($user){
                $connection[$counter]['assignment_id'] = $recommend->id;          
                $connection[$counter]['customer_id'] = $customer->id;
                $connection[$counter]['created_at'] = Carbon::now();
                $counter++;
            }
        }
        RecommendedProductAssignment::insert($connection);
      }

      if(isset($input['record_id']) && count($input['record_id']) > 0 ){
        $records = $input['record_id'];

        if($input['module'] == 'brand'){
          foreach($records as $record_id){
              $data = CustomerProductGroup::where('product_group_id', $record_id)->get();
              $connection = [];
              $counter = 0;
              foreach($data as $item){
                  $user = @$item->customer->user;
                  if($user){
                    if(@$item->customer->sap_connection_id == $input['sap_connection_id']){
                      $connection[$counter]['assignment_id'] = $recommend->id;              
                      $connection[$counter]['customer_id'] = @$item->customer->id;
                      $connection[$counter]['created_at'] = Carbon::now();
                      $counter++;
                    }
                  }
              }
              RecommendedProductAssignment::insert($connection);
          }
        }

        if($input['module'] == 'customer'){
          $connection = [];
          $counter = 0;
          foreach($records as $customer_id){
              $customer = Customer::where('is_active', '1')->where('id', $customer_id)->where('sap_connection_id', $input['sap_connection_id'])->firstOrFail();

              $connection[$counter]['assignment_id'] = $recommend->id;             
              $connection[$counter]['customer_id'] = $customer->id;
              $connection[$counter]['created_at'] = Carbon::now();
              $counter++;
          }
          RecommendedProductAssignment::insert($connection);
        }

        if($input['module'] == 'customer_class'){
          foreach($records as $record_id){
              $data = Customer::where(['class_id' => $record_id, 'sap_connection_id' => $input['sap_connection_id'], 'is_active' => '1' ]);

              if(@$request->select_class_customer == "specific" && !empty(@$request->class_customer)){
                  $data->whereIn('id', @$request->class_customer);
              }

              $data = $data->get();
              $connection = [];
              $counter = 0;
              foreach($data as $customer){
                  $user = @$customer->user;
                  $connection[$counter]['assignment_id'] = $recommend->id;                 
                  $connection[$counter]['customer_id'] = $customer->id;
                  $connection[$counter]['created_at'] = Carbon::now();
                  $counter++;
              }
              RecommendedProductAssignment::insert($connection);
          }
        }

        if($input['module'] == 'sales_specialist'){
          foreach($records as $record_id){
              $data = Customer::where('is_active', '1')->where('sap_connection_id', $input['sap_connection_id'])->whereHas('sales_specialist', function($q) use($record_id){
                  $q->where('ss_id', $record_id);
              })->get();

              $connection = [];
              $counter = 0;
              foreach($data as $customer){
                  $connection[$counter]['assignment_id'] = $recommend->id;                 
                  $connection[$counter]['customer_id'] = $customer->id;
                  $connection[$counter]['created_at'] = Carbon::now();
                  $counter++;
              }
              RecommendedProductAssignment::insert($connection);
          }
        }

        if($input['module'] == 'territory'){
          foreach($records as $record_id){
              $data = Customer::where('is_active', '1')->where('territory', $record_id)->where('sap_connection_id', $input['sap_connection_id'])->get();
              $connection = [];
              $counter = 0;
              foreach($data as $customer){
                  $connection[$counter]['assignment_id'] = $recommend->id;        
                  $connection[$counter]['customer_id'] = $customer->id;
                  $connection[$counter]['created_at'] = Carbon::now();
                  $counter++;
              }
              RecommendedProductAssignment::insert($connection);
          }
        }

        if($input['module'] == 'market_sector'){
          foreach($records as $record_id){
              $data = Customer::where(['u_sector' => $record_id, 'sap_connection_id' => $input['sap_connection_id'], 'is_active' => '1'])->get();
              $connection = [];
              $counter = 0;
              foreach($data as $customer){
                  $connection[$counter]['assignment_id'] = $recommend->id;          
                  $connection[$counter]['customer_id'] = $customer->id;
                  $connection[$counter]['created_at'] = Carbon::now();
                  $counter++;
              }
              RecommendedProductAssignment::insert($connection);
          }
        }
        
      }

      $dyn_word = ($update === 'update') ? 'updated' : 'added';
      $response = ['status'=>true,'message'=>'Recommended products for '.$input['title'].' '.$dyn_word.' successfully.'];
    }

    return $response;
  }

  public function fetchRecommendedData(Request $request){
    $data = RecommendedProduct::query();

    if($request->filter_company != ""){
      $data->where('b_unit', $request->filter_company);
    }
    
    if($request->filter_search != ""){
      $data->where(function($q) use ($request) {

        $q->orwhere('title','LIKE',"%".$request->filter_search."%");

        $q->orWhereHas('items', function($i) use ($request){
          $i->whereHas('product', function($p) use ($request){
            $p->where('item_code','LIKE',"%".$request->filter_search."%");
          });
        });

      });
    }

    $response = $data->get();
    return DataTables::of($response)
                      ->addIndexColumn()
                      ->addColumn('bu', function($row) {
                          return  $row->sap_connection->company_name;
                      })
                      ->addColumn('title', function($row) {
                        return  $row->title;
                      })
                      // ->addColumn('customers', function($row) {
                      //   return  $row->assignments->first()->customer->card_code;
                      // })
                      ->addColumn('products', function($row) {
                        $products = '';
                        $count = 0;
                        foreach($row->items as $i){
                            $comma = ($count > 0) ? ', ' : '';
                            if(strpos($products, $i->product->item_code) === false){
                              $products .= $comma.'<code>'.$i->product->item_code.'</code>';
                            }
                            $count ++;
                        }
                        return $products;
                      })
                      ->addColumn('action', function($row) {
                        return  '<a href="'.route('recommended-products.edit', [encrypt($row->id)]).'" class="btn btn-sm p-1"><i class="fa fa-pencil"></i></a>
                                 <a href="#" data-url="'.route('recommended-products.destroy', [encrypt($row->id)]).'" class="btn btn-sm p-1 delete"><i class="fa fa-trash"></i></a>';
                      })
                      ->rawColumns(['products','action'])
                      ->make(true);
  }

  public function showBenefits(){
    $benefits = ProductBenefits::all();
    return view('product.benefits', compact('benefits'));
  }

  public function updateBenefits(Request $request){
    $benefits = ProductBenefits::find($request->id);

    $update = [
                'code' => $request->code,
                'name'  => $request->description,
              ];
    $request->validate(['icon' => 'mimes:jpg,jpeg,png|max:2048']);
    if ($request->file('icon') !== null && $request->file('icon')->isValid()) {
      $file = $request->file('icon');
      $fileName = Str::random(10).$file->getClientOriginalName();

      if(!Storage::disk('public')->has('products/benefits')){
          Storage::disk('public')->makeDirectory('products/benefits/');
      }

      Storage::disk('public')->delete('products/benefits/'.$benefits->icon);
      $file->storeAs('products/benefits/', $fileName, 'public');
      $update += ['icon' => $fileName];
    } 

    $benefits->update($update);

    return back()
            ->with('success','File has been uploaded.');
  }

  public function benefitsAssignment(){
    $company = SapConnection::all();
    $benefits = ProductBenefits::pluck('code')->all();
    return view('product.benefits-assignment',compact('company', 'benefits'));
  }

  public function addBenefitsAssignment(Request $request){
    foreach($request->bnf_assignment as $b){
      Product::find($b['product_id'])->update([ 'product_benefits' => $b['benefit_ids'] ]);
    }

    return ['status' => true, 'message' => 'Product Benefits updated successfully!'];
  }


  public function getAllAssignedProductBenefits(Request $request){

    $data = Product::whereRaw('last_sync_at > "2023-03-27 09:39:36"')->whereHas('sap_connection',function($q){
              $q->WhereNull('deleted_at');
            });

    if($request->filter_status != ""){
      $data->where('is_active',$request->filter_status);
    }

    if($request->filter_company != ""){
      $filter_company = $request->filter_company;
      if($request->filter_company == 5){ //Solid Trend
        $filter_company = 1;
      }
      $data->where('products.sap_connection_id',$filter_company);
    }

    if($request->filter_search != ""){
      $data->where(function($q) use ($request) {
        $q->orwhere('products.item_code','LIKE',"%".$request->filter_search."%");
        $q->orwhere('products.item_name','LIKE',"%".$request->filter_search."%");
      });
    }

    if($request->filter_date_range != ""){
      $date = explode(" - ", $request->filter_date_range);
      $start = date("Y-m-d", strtotime($date[0]));
      $end = date("Y-m-d", strtotime($date[1]));

      $data->whereDate('created_date', '>=' , $start);
      $data->whereDate('created_date', '<=' , $end);
    }


    if(!in_array(userrole(),[1,11])){
      $data->where('products.is_active', true);

      if(@Auth::user()->sap_connection_id){
        $data->where('products.sap_connection_id', @Auth::user()->sap_connection_id);
      }

      $data->whereHas('group', function($q){
        $q->where('is_active', true);
      });

    }

    $data->when(!isset($request->order), function ($q) {
      $q->orderBy('products.created_date', 'desc');
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
                            return @$row->u_item_line_sap_value->value ?? @$row->u_item_line ?? "-";
                          })
                          ->addColumn('u_tires', function($row) {
                            return @$row->u_tires ?? "-";
                          })
                          ->addColumn('item_class', function($row) {
                            return @$row->item_class ?? "-";
                          })
                          ->addColumn('u_item_type', function($row) {
                            return @$row->u_item_type_sap_value->value ?? @$row->u_item_type ?? "-";
                          })
                          ->addColumn('u_item_application', function($row) {
                            return @$row->u_item_application_sap_value->value ?? @$row->u_item_application ?? "-";
                          })
                          ->addColumn('u_pattern2', function($row) {
                            return @$row->u_pattern2_sap_value->value ?? @$row->u_pattern2 ?? "-" ;
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

                          ->addColumn('status', function($row) {

                            $btn = "";
                            if($row->is_active){
                              $btn .= '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                            }else{
                              $btn .= '<a href="javascript:" class="btn btn-sm btn-light-danger btn-inline status">Inactive</a>';
                            }

                            return $btn;
                          })
                          ->addColumn('bnf1', function($row) {
                            $ids = explode(",",$row->product_benefits);
                            $checked = (in_array("1", $ids)) ? 'checked' : '';

                            $cbox = '<div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="gridCheck" value="1" '.$checked.'>
                                     </div>';
                            return $cbox;
                          })
                          ->addColumn('bnf2', function($row) {
                            $ids = explode(",",$row->product_benefits);
                            $checked = (in_array("2", $ids)) ? 'checked' : '';

                            $cbox = '<div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="gridCheck" value="2" '.$checked.'>
                                     </div>';
                            return $cbox;
                          })
                          ->addColumn('bnf3', function($row) {
                            $ids = explode(",",$row->product_benefits);
                            $checked = (in_array("3", $ids)) ? 'checked' : '';

                            $cbox = '<div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="gridCheck" value="3" '.$checked.'>
                                     </div>';
                            return $cbox;
                          })
                          ->addColumn('bnf4', function($row) {
                            $ids = explode(",",$row->product_benefits);
                            $checked = (in_array("4", $ids)) ? 'checked' : '';

                            $cbox = '<div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="gridCheck" value="4" '.$checked.'>
                                     </div>';
                            return $cbox;
                          })
                          ->addColumn('bnf5', function($row) {
                            $ids = explode(",",$row->product_benefits);
                            $checked = (in_array("5", $ids)) ? 'checked' : '';

                            $cbox = '<div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="gridCheck" value="5" '.$checked.'>
                                     </div>';
                            return $cbox;
                          })
                          ->addColumn('bnf6', function($row) {
                            $ids = explode(",",$row->product_benefits);
                            $checked = (in_array("6", $ids)) ? 'checked' : '';

                            $cbox = '<div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="gridCheck" value="6" '.$checked.'>
                                     </div>';
                            return $cbox;
                          })
                          ->addColumn('bnf7', function($row) {
                            $ids = explode(",",$row->product_benefits);
                            $checked = (in_array("7", $ids)) ? 'checked' : '';

                            $cbox = '<div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="gridCheck" value="7" '.$checked.'>
                                     </div>';
                            return $cbox;
                          })
                          ->addColumn('bnf8', function($row) {
                            $ids = explode(",",$row->product_benefits);
                            $checked = (in_array("8", $ids)) ? 'checked' : '';

                            $cbox = '<div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="gridCheck" value="8" '.$checked.'>
                                     </div>';
                            return $cbox;
                          })
                          ->addColumn('bnf9', function($row) {
                            $ids = explode(",",$row->product_benefits);
                            $checked = (in_array("9", $ids)) ? 'checked' : '';

                            $cbox = '<div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="gridCheck" value="9" '.$checked.'>
                                     </div>';
                            return $cbox;
                          })
                          ->addColumn('bnf10', function($row) {
                            $ids = explode(",",$row->product_benefits);
                            $checked = (in_array("10", $ids)) ? 'checked' : '';

                            $cbox = '<div class="form-check">
                                      <input class="form-check-input" type="checkbox" id="gridCheck" value="10" '.$checked.'>
                                     </div>';
                            return $cbox;
                          })
                          ->rawColumns(['status', 'bnf1','bnf2','bnf3','bnf4','bnf5','bnf6','bnf7','bnf8','bnf9','bnf10'])
                          ->make(true);
  }


}
