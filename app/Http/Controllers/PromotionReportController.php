<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SapConnection;
use App\Models\Promotions;
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
            $data->where('id', $request->filter_company);
        }

        $company = $company->get();

        // dd($company);

        $outputData = [];
        foreach($company as $key => $item){
                $outputData[] = ['no' => $key, 'company_name' => $item->company_name, 'status' => $key, 'total_promotion' => 'TP-123', 'total_quantity' => 'TQ-123', 'total_amount' => 'TA-123'];
                // $totalPromotion = Promotions::where(['is_active' => 1, 'sap_connection_id' => ])->count()
        }

        return ['status' => true, 'data' => $outputData];

    }
}
