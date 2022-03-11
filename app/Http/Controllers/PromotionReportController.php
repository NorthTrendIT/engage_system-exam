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
                $totalPending = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending'])->count();

                // Approved
                $totalApproved = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved'])->count();

                // Completed

                /** 2.Total Sales Quantity */

                // Pending
                $totalPendingQue = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending'])->sum('total_quantity');

                // Approved
                $totalApprovedQue = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved'])->sum('total_quantity');

                // Completed

                /** 2.Total Sales Revenue */

                // Pending
                $totalPendingRev = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending'])->sum('total_amount');

                // Approved
                $totalApprovedRev = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved'])->sum('total_amount');

                // Completed

                $pending = [
                    'no' => ++$no,
                    'company_name' => $companyName,
                    'status' => 'Pending',
                    'total_promotion' => $totalPending,
                    'total_quantity' => $totalPendingQue,
                    'total_amount' => $totalPendingRev,
                ];
                $approved = [
                    'no' => ++$no,
                    'company_name' => $companyName,
                    'status' => 'Approved',
                    'total_promotion' => $totalApproved,
                    'total_quantity' => $totalApprovedQue,
                    'total_amount' => $totalApprovedRev,
                ];

                array_push($outputData, $pending);
                array_push($outputData, $approved);
        }

        return ['status' => true, 'data' => $outputData];

    }
}
