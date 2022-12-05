<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SapConnection;
use App\Models\Product;
use App\Models\CustomerPromotion;
use Auth;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductReportExport;

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
}
