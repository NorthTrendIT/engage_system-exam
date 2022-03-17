<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SapConnection;
use App\Models\Product;
use App\Models\CustomerPromotion;
use App\Models\InvoiceItem;
use Auth;
use Carbon\Carbon;

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
                $activeProducts = Product::where('sap_connection_id', $item->id)->where('is_active', 1)->count();

                // Sleeping Products
                $sleepingProducts = Product::where('sap_connection_id', $item->id)->where('is_active', 0)->count();

                // Product Movement
                $productMovement = Product::where('sap_connection_id', $item->id)->join("invoice_items",function($join){
                        $join->on('invoice_items.item_code','=','products.item_code');
                    })
                    ->where('is_active', 1)
                    ->where('invoice_items.ship_date', '>=', Carbon::now()->subDays(60)->toDateTimeString())->count();

                // dd($productMovement);

                $row = [
                    'no' => ++$no,
                    'company_name' => $companyName,
                    'active_product' => number_format($activeProducts),
                    'sleeping_product' => number_format($sleepingProducts),
                    'product_movement' => number_format($productMovement),
                ];

                // dd($row);

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

            // Company Name
            array_push($category, $companyName);

            // Active Products
            $active = Product::where('sap_connection_id', $item->id)->where('is_active', 1)->count();
            array_push($activeProducts, $active);

            // Sleeping Products
            $sleeping = Product::where('sap_connection_id', $item->id)->where('is_active', 0)->count();
            array_push($sleepingProducts, $sleeping);

            // Product Movement
            $movement = Product::where('sap_connection_id', $item->id)->join("invoice_items",function($join){
                    $join->on('invoice_items.item_code','=','products.item_code');
                })
                ->where('is_active', 1)
                ->where('invoice_items.ship_date', '>=', Carbon::now()->subDays(60)->toDateTimeString())
                ->count();

            array_push($productMovement, $movement);

        }
        $data = [];
        array_push($data, array('name' => 'Active Product', 'data' => $activeProducts));
        array_push($data, array('name' => 'Sleeping Products', 'data' => $sleepingProducts));
        array_push($data, array('name' => 'Product Movements', 'data' => $productMovement));

        return ['status' => true, 'data' => $data, 'category' => $category];

    }
}
