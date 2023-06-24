<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SapConnection;
use App\Models\Product;
use App\Models\CustomerPromotion;
use Auth;
use Carbon\Carbon;
use App\Models\CustomerProductGroup;
use App\Models\CustomerProductItemLine;
use App\Models\CustomerProductTiresCategory;
use App\Models\Customer;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductReportExport;
use Illuminate\Support\Facades\DB;
use App\Models\Quotation;

class ProductReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('report.product-report.index', compact('company'));
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
        //
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
        //
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

    public function getAll(Request $request){
        $company = SapConnection::query();

        if($request->filter_company != ""){
            $company->where('id', $request->filter_company);
        }

        $company = $company->get();

        $outputData = [];
        $no = 0;
        foreach($company as $key => $item){
                $companyName = $item->company_name;

                // Active Products
                $activeProducts = Product::where('sap_connection_id', $item->id)->where('is_active', 1);

                if($request->filter_brand != ""){
                    $activeProducts->where('items_group_code',$request->filter_brand);
                }

                if($request->filter_product_category != ""){
                    $activeProducts->where('u_tires',$request->filter_product_category);
                }

                if($request->filter_product_line != ""){
                    $activeProducts->where('u_item_line',$request->filter_product_line);
                }

                if($request->filter_product_class != ""){
                    $activeProducts->where('item_class',$request->filter_product_class);
                }

                if($request->filter_product_type != ""){
                    $activeProducts->where('u_item_type',$request->filter_product_type);
                }

                if($request->filter_product_application != ""){
                    $activeProducts->where('u_item_application',$request->filter_product_application);
                }

                if($request->filter_product_pattern != ""){
                    $activeProducts->where('u_pattern2',$request->filter_product_pattern);
                }

                $activeProducts = $activeProducts->count();


                // Sleeping Products
                $sleepingProducts = Product::where('sap_connection_id', $item->id)->where('is_active', 0);

                if($request->filter_brand != ""){
                    $sleepingProducts->where('items_group_code',$request->filter_brand);
                }

                if($request->filter_product_category != ""){
                    $sleepingProducts->where('u_tires',$request->filter_product_category);
                }

                if($request->filter_product_line != ""){
                    $sleepingProducts->where('u_item_line',$request->filter_product_line);
                }

                if($request->filter_product_class != ""){
                    $sleepingProducts->where('item_class',$request->filter_product_class);
                }

                if($request->filter_product_type != ""){
                    $sleepingProducts->where('u_item_type',$request->filter_product_type);
                }

                if($request->filter_product_application != ""){
                    $sleepingProducts->where('u_item_application',$request->filter_product_application);
                }

                if($request->filter_product_pattern != ""){
                    $sleepingProducts->where('u_pattern2',$request->filter_product_pattern);
                }

                $sleepingProducts = $sleepingProducts->count();

                // Product Movement
                $productMovement = Product::where('products.sap_connection_id', $item->id)->join("invoice_items",function($join){
                        $join->on('invoice_items.item_code','=','products.item_code');
                    })
                    ->where('is_active', 1)
                    ->where('invoice_items.ship_date', '>=', Carbon::now()->subDays(60)->toDateTimeString());

                if($request->filter_brand != ""){
                    $productMovement->where('items_group_code',$request->filter_brand);
                }

                if($request->filter_product_category != ""){
                    $productMovement->where('u_tires',$request->filter_product_category);
                }

                if($request->filter_product_line != ""){
                    $productMovement->where('u_item_line',$request->filter_product_line);
                }

                if($request->filter_product_class != ""){
                    $productMovement->where('item_class',$request->filter_product_class);
                }

                if($request->filter_product_type != ""){
                    $productMovement->where('u_item_type',$request->filter_product_type);
                }

                if($request->filter_product_application != ""){
                    $productMovement->where('u_item_application',$request->filter_product_application);
                }

                if($request->filter_product_pattern != ""){
                    $productMovement->where('u_pattern2',$request->filter_product_pattern);
                }

                $productMovement = $productMovement->count();

                $row = [
                    'no' => ++$no,
                    'company_name' => $companyName,
                    'active_product' => number_format($activeProducts),
                    'sleeping_product' => number_format($sleepingProducts),
                    'product_movement' => number_format($productMovement),
                ];
                array_push($outputData, $row);
        }

        return ['status' => true, 'data' => $outputData];

    }

    public function getChartData(Request $request){
        $company = SapConnection::query();

        if($request->filter_company != ""){
            $company->where('id', $request->filter_company);
        }

        $company = $company->get();

        $activeProducts = [];
        $sleepingProducts = [];
        $productMovement = [];
        $totalRevenue = [];
        $category = [];

        foreach($company as $key => $item){
            $companyName = $item->company_name;

            // // Company Name
            // array_push($category, $companyName);

            // Active Products
            $active = Product::where('sap_connection_id', $item->id)->where('is_active', 1)->count();
            array_push($activeProducts, $active);

            // Sleeping Products
            $sleeping = Product::where('sap_connection_id', $item->id)->where('is_active', 0)->count();
            array_push($sleepingProducts, $sleeping);

            // Product Movement
            $movement = Product::where('products.sap_connection_id', $item->id)->join("invoice_items",function($join){
                    $join->on('invoice_items.item_code','=','products.item_code');
                })
                ->where('is_active', 1)
                ->where('invoice_items.ship_date', '>=', Carbon::now()->subDays(60)->toDateTimeString())
                ->count();

            array_push($productMovement, $movement);


            // Company Name
            $companyName .= " (".($active+$sleeping+$movement).")";
            array_push($category, $companyName);

        }
        $data = [];
        array_push($data, array('name' => 'Active Product', 'data' => $activeProducts));
        array_push($data, array('name' => 'Sleeping Products', 'data' => $sleepingProducts));
        array_push($data, array('name' => 'Product Movements', 'data' => $productMovement));

        return ['status' => true, 'data' => $data, 'category' => $category];

    }

    // public function export(Request $request){
    //     ini_set('memory_limit', '1024M');
    //     ini_set('max_execution_time', 1800);
    //     $filter = collect();
    //     if(@$request->data){
    //       $filter = json_decode(base64_decode($request->data));
    //     }

    //     $records = array();
    //     $headers = array();

    //     $activeProducts = $this->getActiveProducts($filter);
    //     $inactiveProducts = $this->getInactiveProducts($filter);
    //     $productMovement = $this->getProductMovement($filter);

    //     $headers = $this->getHeaders($filter);

    //     array_push($records, $activeProducts);
    //     array_push($records, $inactiveProducts);
    //     array_push($records, $productMovement);

    //     if(count($records)){
    //         $title = 'Product Report '.date('dmY').'.xlsx';
    //         return Excel::download(new ProductReportExport($records, $headers), $title);
    //     }

    //     \Session::flash('error_message', common_error_msg('excel_download'));
    //     return redirect()->back();
    // }

    public function export(Request $request){
      $company = SapConnection::query();
      if($request->filter_company != ""){
          $company->where('id', $request->filter_company);
      }
      $company = $company->get();
      $outputData = [];
      $no = 0;
      foreach($company as $key => $item){
        $companyName = $item->company_name;
        // Active Products
        $activeProducts = Product::where('sap_connection_id', $item->id)->where('is_active', 1);

        if($request->filter_brand != ""){
          $activeProducts->where('items_group_code',$request->filter_brand);
        }

        if($request->filter_product_category != ""){
          $activeProducts->where('u_tires',$request->filter_product_category);
        }

        if($request->filter_product_line != ""){
          $activeProducts->where('u_item_line',$request->filter_product_line);
        }

        if($request->filter_product_class != ""){
          $activeProducts->where('item_class',$request->filter_product_class);
        }

        if($request->filter_product_type != ""){
          $activeProducts->where('u_item_type',$request->filter_product_type);
        }

        if($request->filter_product_application != ""){
          $activeProducts->where('u_item_application',$request->filter_product_application);
        }

        if($request->filter_product_pattern != ""){
          $activeProducts->where('u_pattern2',$request->filter_product_pattern);
        }

        $activeProducts = $activeProducts->count();

        // Sleeping Products
        $sleepingProducts = Product::where('sap_connection_id', $item->id)->where('is_active', 0);
        if($request->filter_brand != ""){
            $sleepingProducts->where('items_group_code',$request->filter_brand);
        }

        if($request->filter_product_category != ""){
            $sleepingProducts->where('u_tires',$request->filter_product_category);
        }

        if($request->filter_product_line != ""){
            $sleepingProducts->where('u_item_line',$request->filter_product_line);
        }

        if($request->filter_product_class != ""){
            $sleepingProducts->where('item_class',$request->filter_product_class);
        }

        if($request->filter_product_type != ""){
            $sleepingProducts->where('u_item_type',$request->filter_product_type);
        }

        if($request->filter_product_application != ""){
            $sleepingProducts->where('u_item_application',$request->filter_product_application);
        }

        if($request->filter_product_pattern != ""){
            $sleepingProducts->where('u_pattern2',$request->filter_product_pattern);
        }

        $sleepingProducts = $sleepingProducts->count();

        // Product Movement
        $productMovement = Product::where('products.sap_connection_id', $item->id)->join("invoice_items",function($join){
                $join->on('invoice_items.item_code','=','products.item_code');
            })
            ->where('is_active', 1)
            ->where('invoice_items.ship_date', '>=', Carbon::now()->subDays(60)->toDateTimeString());

        if($request->filter_brand != ""){
            $productMovement->where('items_group_code',$request->filter_brand);
        }

        if($request->filter_product_category != ""){
            $productMovement->where('u_tires',$request->filter_product_category);
        }

        if($request->filter_product_line != ""){
            $productMovement->where('u_item_line',$request->filter_product_line);
        }

        if($request->filter_product_class != ""){
            $productMovement->where('item_class',$request->filter_product_class);
        }

        if($request->filter_product_type != ""){
            $productMovement->where('u_item_type',$request->filter_product_type);
        }

        if($request->filter_product_application != ""){
            $productMovement->where('u_item_application',$request->filter_product_application);
        }

        if($request->filter_product_pattern != ""){
            $productMovement->where('u_pattern2',$request->filter_product_pattern);
        }

        $productMovement = $productMovement->count();
        $row = [
            'no' => ++$no,
            'company_name' => $companyName,
            'active_product' => number_format($activeProducts),
            'sleeping_product' => number_format($sleepingProducts),
            'product_movement' => number_format($productMovement),
        ];
        array_push($outputData, $row);
      }

      $headers = array(
          'No.',
          'Business Unit',
          'Active Product',
          'Sleeping Product',
          'Product Movement',
      );
      $title = 'Product Report '.date('dmY').'.xlsx';
      return Excel::download(new ProductReportExport($outputData, $headers), $title);
    }

    public function getActiveProducts($filter){
        $data = Product::where('is_active', 1)->orderBy('created_date', 'desc');

        if(@$filter->filter_company != ""){
          $data->where('sap_connection_id',$filter->filter_company);
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
            $temp = array(
                      'no' => $key + 1,
                      'company' => @$value->sap_connection->company_name ?? "-",
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
              $temp['u_pattern2'] = @$value->u_pattern2_sap_value->value ?? $value->u_pattern2 ?? "-";
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

            $temp = array(
                      'no' => $key + 1,
                      'company' => @$value->sap_connection->company_name ?? "-",
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
        }

        return $records;
    }

    public function getInactiveProducts($filter){
      $data = Product::where('is_active', 0)->orderBy('created_date', 'desc');

      if(@$filter->filter_company != ""){
        $data->where('sap_connection_id',$filter->filter_company);
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
          $temp = array(
                    'no' => $key + 1,
                    'company' => @$value->sap_connection->company_name ?? "-",
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
            $temp['u_pattern2'] = @$value->u_pattern2_sap_value->value ?? $value->u_pattern2 ?? "-";
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

          $temp = array(
                    'no' => $key + 1,
                    'company' => @$value->sap_connection->company_name ?? "-",
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
      }

      return $records;
    }

    public function getProductMovement($filter){
        $data = Product::join("invoice_items",function($join){
            $join->on('invoice_items.item_code','=','products.item_code');
        })
        ->where('is_active', 1)
        ->where('invoice_items.ship_date', '>=', Carbon::now()->subDays(60)->toDateTimeString())
        ->orderBy('created_date', 'desc');

        if(@$filter->filter_company != ""){
          $data->where('sap_connection_id',$filter->filter_company);
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
            $temp = array(
                      'no' => $key + 1,
                      'company' => @$value->sap_connection->company_name ?? "-",
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
              $temp['u_pattern2'] = @$value->u_pattern2_sap_value->value ?? $value->u_pattern2 ?? "-";
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

            $temp = array(
                      'no' => $key + 1,
                      'company' => @$value->sap_connection->company_name ?? "-",
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
        }

        return $records;
    }

    public function getHeaders($filter){
        $headers = array();

        $product_category = strtolower(@$filter->filter_product_category);
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

        return $headers;
    }

    public function getChartTopProductQuantityData(Request $request){

        $c_product_tires_category = $c_product_item_line = $c_product_group = array();
        $customer_id = [];
        $customer = collect();
        $sap_connection_id = [];
        $customer_price_list_no = [];

        $customer_id = explode(',', Auth::user()->multi_customer_id);
        $sap_connection_id = explode(',', Auth::user()->multi_real_sap_connection_id);
        $customer_price_list_no = get_customer_price_list_no_arr($customer_id);
        $customer_vat  = Customer::whereIn('id', $customer_id)->get();

        if(in_array(5, $sap_connection_id)){
            array_push($sap_connection_id, '5');
        }
        // Is Customer
        if(!empty($customer_id)){

            // Product Group
            $c_product_group = CustomerProductGroup::with('product_group')->whereIn('customer_id', $customer_id)->get();
            $c_product_group = array_column( $c_product_group->toArray(), 'product_group_id' );

            // Product Item Line
            $c_product_item_line = CustomerProductItemLine::with('product_item_line')->whereIn('customer_id', $customer_id)->get();
            $c_product_item_line = array_column( $c_product_item_line->toArray(), 'product_item_line_id' ); 

            // Product Tires Category
            $c_product_tires_category = CustomerProductTiresCategory::with('product_tires_category')->whereIn('customer_id', $customer_id)->get();
            $c_product_tires_category = array_column( $c_product_tires_category->toArray(), 'product_tires_category_id' );            
        }

        if(empty($c_product_group) && empty($c_product_tires_category) && empty($c_product_item_line)){
            $products = collect([]);
        }

        $where = array('is_active' => true);
        $products = Product::where($where);
        $products->whereHas('group', function($q){
            $q->where('is_active', true);
        });        

        $products->where(function($q) use ($request, $c_product_tires_category, $c_product_item_line, $c_product_group) {
            if(!empty($c_product_group)){
                $q->orwhereHas('group', function($q1) use ($c_product_group){
                    $q1->whereIn('id', $c_product_group);
                });
            }

            if(!empty($c_product_tires_category)){
                $q->orwhereHas('product_tires_category', function($q1) use ($c_product_tires_category){
                    $q1->whereIn('id', $c_product_tires_category);
                });
            }

            if(!empty($c_product_item_line)){
                $q->orwhereHas('product_item_line', function($q1) use ($c_product_item_line){
                    $q1->whereIn('id', $c_product_item_line);
                });
            }
        });

        $products1 = $products->whereIn('sap_connection_id', $sap_connection_id)->get();
        
        $customer_price = 0;
        $sum = 0;
        foreach ($products1 as $key => $value) {
          foreach($customer_vat as $cust){
            if($sap_connection_id === $cust->real_sap_connection_id){
                $customer_price = (get_product_customer_price(@$value->item_prices,@$customer_price_list_no[$sap_connection_id[$key]], false, false, $cust));
                $data2[$value->id] = $customer_price;
                $sum = $sum + $customer_price;
            }
          }
        }             
        arsort($data2);           
        $newArray = array_slice($data2, 0, 5, true);
        $keys_array = array_keys($newArray);
        $value_array = array_values($newArray);
        $amounts = Product::whereIn('id', $keys_array)->get();
        $sum1 = 0;
        foreach($amounts as $key=>$val){
            $data3[$key]['price'] = ($value_array[$key]); 
            $data3[$key]['item'] = $val->item_name." ".number_format_value($value_array[$key]);
            $sum1 = $sum1 + $value_array[$key];
        }
        $data['item'] = $data3;
        $data['others'] = $sum - $sum1;
        $data = compact('data');

        $sum3 = 0;
        $products2 = $products->whereIn('sap_connection_id', $sap_connection_id)->orderBy('quantity_ordered_by_customers','DESC')->take(5)->get();
        $products2_sum = $products->whereIn('sap_connection_id', $sap_connection_id)->orderBy('quantity_ordered_by_customers','DESC')->sum('quantity_ordered_by_customers');

        foreach ($products2 as $key => $value) {
            $data1[$key]['item'] = $value->item_name;
            $data1[$key]['qty'] = $value->quantity_ordered_by_customers;
            $sum3 = $sum3 + $value->quantity_ordered_by_customers;
        }

        $data1['item'] = $data1;
        $data1['others'] = $products2_sum - $sum3;
        $response = [ 'status' => true, 'data' => $data,'data1'=>$data1];
        return $response;
    }

    public function getChartTopPerformingData(Request $request){
        $category = [];  
        $total_quantity = []; 
        $data = [];     

        $where = array('is_active' => true);
        $products = Product::where($where);
        $products->whereHas('group', function($q){
            $q->where('is_active', true);
        });

        if($request->range == 'this_month'){
            $products->whereMonth('created_at', Carbon::now()->month);
        }else if($request->range == 'this_week'){
            $products->whereBetween('created_at', 
                        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
                    );
        }else if($request->range == 'custom_date'){
            $date = explode(" - ", $request->custom);
            $start = date("Y-m-d H:i:s", strtotime($date[0]));
            $end = date("Y-m-d H:i:s", strtotime($date[1]));

            $products->whereDate('created_date', '>=' , $start);
            $products->whereDate('created_date', '<=' , $end);
        }

        if($request->type == 'Quantity'){
            $products = $products->orderBy('quantity_ordered_by_customers','DESC')->take(5)->get();
            foreach($products as $val){
                array_push($category, $val->item_name);
                array_push($total_quantity, $val->quantity_ordered_by_customers);
            }
            array_push($data, array('name' => 'Qty', 'data' => $total_quantity));
        }else if($request->type == 'Amount'){
            $data2 = [];
            $data3 = [];
            $products = $products->get();
            foreach ($products as $key => $value) {
              $data2[$value->id] = 0;
              // $data2[$value->id] = (get_product_customer_price(@$value->item_prices,@$customer_price_list_no[$sap_connection_id[$key]], false, false, ''));
            }
            arsort($data2);           
            $newArray = array_slice($data2, 0, 5, true);
            $keys_array = array_keys($newArray);
            $value_array = array_values($newArray);
            $amounts = Product::whereIn('id', $keys_array)->get();
            foreach($amounts as $key=>$val){
                array_push($category, $val->item_name);
                array_push($total_quantity, $value_array[$key]);
            }
            array_push($data, array('name' => 'Amount', 'data' => $total_quantity));
        }

        return ['status' => true, 'data' => $data, 'category' => $category];
    }


    public function getProductStatistics(Request $request){
      $cust_id = explode(',', Auth::user()->multi_customer_id);

      $sum = '';
      if($request->type == 'Quantity'){
        $sum = 'item.quantity';
      }else if($request->type == 'Liters'){ //work
        $sum = 'item.quantity';
      }
      else if($request->type == 'Amount'){
        $sum = 'item.gross_total';
      }

      $items = [];
      if($request->order == 'back_order'){
        $sum = substr($sum, 5); //'remove item.*'
        $totalSelectQuery = ($request->type == 'Liters')? '(sum(item.'.$sum.') - sum(item1.'.$sum.') * prod.sales_unit_weight)' : 'sum(item.'.$sum.') - sum(item1.'.$sum.')';
        $query = DB::table('quotations as quot')
                    ->join('quotation_items as item', 'item.quotation_id', '=', 'quot.id')
                    // ->join('orders as ord', function($join){
                    //    $join->on('ord.base_entry', '=', 'quot.doc_entry');
                    //    $join->on('ord.sap_connection_id', '=', 'quot.sap_connection_id');
                    // })
                    ->join('invoices as inv', function($join){
                       $join->on('inv.u_omsno', '=', 'quot.doc_entry');
                       $join->on('inv.sap_connection_id', '=', 'quot.sap_connection_id');
                    })
                    ->join('invoice_items as item1', 'item1.invoice_id', '=', 'inv.id');
                    if($request->type == 'Liters'){
                      $query->leftJoin('products as prod', function($join){
                        $join->on('prod.item_code', '=', 'item.item_code');
                        $join->on('prod.sap_connection_id', '=', 'item.real_sap_connection_id');
                      });
                    }
        $query->join('customers as cust', 'cust.card_code', '=', 'quot.card_code')
                    ->selectRaw('item.item_code, item.item_description, '.$totalSelectQuery.' as total_order')
                    ->whereIn('cust.id', $cust_id)
                    ->where('quot.cancelled', 'No')
                    ->where('inv.cancelled', 'No');
                    if($request->type == 'Liters'){
                      $query->whereIn('prod.items_group_code', [109, 111]); //mobile and castrol
                            // ->havingRaw('(sum(item.'.$sum.') - sum(item1.'.$sum.') * prod.sales_unit_weight) > 0');
                      // $query->where('prod.is_active', 1);
                    }
        $query->groupBy('item.item_code')
                    ->orderBy('total_order', 'desc')
                    ->limit(5);
        $items = $query->get();
      }else{
        if($request->order == 'order'){
          $table = 'quotation';
          $alias = 'quot';
        }else if($request->order == 'invoice'){
          $table = 'invoice';
          $alias = 'inv';
        }

        $totalSelectQuery = ($request->type == 'Liters')? '(sum('.$sum.') * prod.sales_unit_weight)  as total_order' : 'sum('.$sum.') as total_order';
        $query = DB::table(''.$table.'s as '.$alias.'')
                    ->join(''.$table.'_items as item', 'item.'.$table.'_id', '=', $alias.'.id');
                    if($request->type == 'Liters'){
                      $query->leftJoin('products as prod', function($join){
                        $join->on('prod.item_code', '=', 'item.item_code');
                        $join->on('prod.sap_connection_id', '=', 'item.real_sap_connection_id');
                      });
                    }
                    $query->join('customers as cust', function($join) use ($alias){
                        $join->on('cust.card_code', '=', $alias.'.card_code');
                        // $join->on('cust.real_sap_connection_id', '=', $alias.'.real_sap_connection_id');
                    })
                    ->selectRaw('item.item_code, item.item_description, '.$totalSelectQuery.', item.real_sap_connection_id')
                    ->whereIn('cust.id', $cust_id)
                    ->where('cancelled', 'No');
                    if($request->type == 'Liters'){
                      $query->whereIn('prod.items_group_code', [109, 111]); //mobile and castrol
                      // $query->where('prod.is_active', 1);
                    }
                    $query->groupBy('item.item_code')
                    ->orderBy('total_order', 'desc')
                    ->limit(5);
        $items = $query->get();
      }

      $data = [];
      foreach($items as $key=>$val){
        
        $data[$key]['name'] = $val->item_code;
        // if($request->type == 'Liters'){
        //    $product = DB::table('products')->select('item_code', 'sales_unit_weight')->where('item_code', $val->item_code)->where('sap_connection_id', $val->real_sap_connection_id)->first();
          
        //    if($val->item_code == $product->item_code){ 
        //     $data[$key]['key'] = floor($val->total_order * $product->sales_unit_weight); 
        //     $items[$key] =  (object) array('item_description' => $val->item_description.' '.$product->sales_unit_weight ,
        //                                     'total_order' => $data[$key]['key'] ); 
        //    } 
        // }else{
          $data[$key]['key'] = floor($val->total_order);
        // }
      }
      // dd($items);
      $response = ['status' => true, 'data'=>$items,'data1'=>$data];
      return $response;
    }


  }
