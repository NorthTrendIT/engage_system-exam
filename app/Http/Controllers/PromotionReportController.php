<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SapConnection;
use App\Models\Promotions;
use App\Models\CustomerPromotion;
use Auth;

class PromotionReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('report.promotion', compact('company'));
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

                /** 1. Number of Promo */
                // Pending
                $totalPending = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending']);

                if($request->filter_customer != ""){
                    $totalPending->whereHas('user',function($q) use($request){
                        $q->where('customer_id', $request->filter_customer);
                    });
                }

                if($request->filter_sales_specialist != ""){
                    $totalPending->where('sales_specialist_id', $request->filter_sales_specialist);
                }

                $totalPending = $totalPending->count();

                // Approved
                $totalApproved = CustomerPromotion::with('customer_user')->where(['sap_connection_id' => $item->id, 'status' => 'approved']);

                if($request->filter_customer != ""){
                    $totalApproved->whereHas('user',function($q) use($request){
                        $q->where('customer_id', $request->filter_customer);
                    });
                }

                if($request->filter_sales_specialist != ""){
                    $totalApproved->where('sales_specialist_id', $request->filter_sales_specialist);
                }

                $totalApproved = $totalApproved->count();

                // Completed

                /** 2.Total Sales Quantity */

                // Pending
                $totalPendingQue = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending']);

                if($request->filter_customer != ""){
                    $totalPendingQue->whereHas('user',function($q) use($request){
                        $q->where('customer_id', $request->filter_customer);
                    });
                }

                if($request->filter_sales_specialist != ""){
                    $totalPendingQue->where('sales_specialist_id', $request->filter_sales_specialist);
                }

                $totalPendingQue = $totalPendingQue->sum('total_quantity');

                // Approved
                $totalApprovedQue = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved']);

                if($request->filter_customer != ""){
                    $totalApprovedQue->whereHas('user',function($q) use($request){
                        $q->where('customer_id', $request->filter_customer);
                    });
                }

                if($request->filter_sales_specialist != ""){
                    $totalApprovedQue->where('sales_specialist_id', $request->filter_sales_specialist);
                }

                $totalApprovedQue = $totalApprovedQue->sum('total_quantity');

                // Completed

                /** 2.Total Sales Revenue */

                // Pending
                $totalPendingRev = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending']);

                if($request->filter_customer != ""){
                    $totalPendingRev->whereHas('user',function($q) use($request){
                        $q->where('customer_id', $request->filter_customer);
                    });
                }

                if($request->filter_sales_specialist != ""){
                    $totalPendingRev->where('sales_specialist_id', $request->filter_sales_specialist);
                }

                $totalPendingRev = $totalPendingRev->sum('total_amount');

                // Approved
                $totalApprovedRev = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved']);

                if($request->filter_customer != ""){
                    $totalApprovedRev->whereHas('user',function($q) use($request){
                        $q->where('customer_id', $request->filter_customer);
                    });
                }

                if($request->filter_sales_specialist != ""){
                    $totalApprovedRev->where('sales_specialist_id', $request->filter_sales_specialist);
                }

                $totalApprovedRev = $totalApprovedRev->sum('total_amount');

                // Completed

                $pending = [
                    'no' => ++$no,
                    'company_name' => $companyName,
                    'status' => 'Pending',
                    'total_promotion' => $totalPending,
                    'total_quantity' => $totalPendingQue,
                    'total_amount' => number_format($totalPendingRev, 2),
                ];
                $approved = [
                    'no' => ++$no,
                    'company_name' => $companyName,
                    'status' => 'Approved',
                    'total_promotion' => $totalApproved,
                    'total_quantity' => $totalApprovedQue,
                    'total_amount' => number_format($totalApprovedRev, 2),
                ];

                array_push($outputData, $pending);
                array_push($outputData, $approved);
        }

        return ['status' => true, 'data' => $outputData];

    }

    public function getChartData(Request $request){
        $company = SapConnection::query();

        if($request->filter_company != ""){
            $company->where('id', $request->filter_company);
        }

        $company = $company->get();

        $pendingPromotion = [];
        $approvedPromotion = [];
        $cancelPromotion = [];
        $totalRevenue = [];
        $category = [];

        foreach($company as $key => $item){
            $companyName = $item->company_name;

            array_push($category, $companyName);
            /** 1. Number of Promo */
            // Pending
            $totalPending = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending'])->count();
            array_push($pendingPromotion, $totalPending);

            // Approved
            $totalApproved = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved'])->count();
            array_push($approvedPromotion, $totalApproved);

            // Revenue
            $revenue = CustomerPromotion::where('sap_connection_id', '=',$item->id)->sum('total_amount');
            array_push($totalRevenue, number_format($revenue, 2));

            // Cancel
            $canceled = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'canceled'])->count();
            array_push($cancelPromotion, $canceled);

        }
        $data = [];
        array_push($data, array('name' => 'Pending Promotion', 'data' => $pendingPromotion));
        array_push($data, array('name' => 'Approved Promotion', 'data' => $approvedPromotion));
        // array_push($data, array('name' => 'Total Revenue', 'data' => $totalRevenue));
        array_push($data, array('name' => 'Canceled Promotion', 'data' => $cancelPromotion));

        return ['status' => true, 'data' => $data, 'category' => $category];

    }
}
