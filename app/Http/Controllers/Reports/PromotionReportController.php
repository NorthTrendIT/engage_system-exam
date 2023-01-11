<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SapConnection;
use App\Models\Promotions;
use App\Models\CustomerPromotion;
use Auth;
use App\Models\User;
use App\Models\ProductGroup;
use App\Models\Classes;
use App\Models\Customer;
use App\Models\Role;
use App\Exports\PromotionReportExport;
use Maatwebsite\Excel\Facades\Excel;

class PromotionReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = [];
        $managers = [];
        if(Auth::user()->role_id == 1){
            $company = SapConnection::all();
            $role = Role::where('name','Manager')->first();
            $managers = User::where('role_id',@$role->id)->get();
        }
        if(Auth::user()->role_id == 6){
            $company = SapConnection::all();          
        }
        return view('report.promotion-report.index', compact('company','managers'));
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
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
            $company = SapConnection::query();
            if($request->filter_company != ""){
                $company->where('id', $request->filter_company);
            }
            $company = $company->get();
        }else{
            $user = User::where('id',Auth::id())->first();
            $company = SapConnection::where(['id'=>$user->sap_connection_id])->get();
        }
        

        $outputData = [];
        $no = 0;
        $grand_total_of_pending_sales_revenue = $grand_total_of_approved_sales_revenue = 0;
        foreach($company as $key => $item){
                $companyName = $item->company_name;

                /** 1. Number of Promo */
                // Pending
                $totalPending = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalPending->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalPending->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer Filter
                if(Auth::user()->role_id == 4){
                    $totalPending->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if($request->filter_customer != ""){
                        $totalPending->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_sales_specialist != ""){
                        $totalPending->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalPending->where('sales_specialist_id', Auth::id());
                }

                // Date Range Filter
                if($request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalPending->whereDate('created_at', '>=' , $start);
                    $totalPending->whereDate('created_at', '<=' , $end);
                }

                $totalPending = $totalPending->count();

                // Approved
                $totalApproved = CustomerPromotion::with('customer_user')->where(['sap_connection_id' => $item->id, 'status' => 'approved']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalApproved->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalApproved->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer filter
                if(Auth::user()->role_id == 4){
                    $totalApproved->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if($request->filter_customer != ""){
                        $totalApproved->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_sales_specialist != ""){
                        $totalApproved->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalApproved->where('sales_specialist_id', Auth::id());
                }

                // Date range Filter
                if($request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalApproved->whereDate('created_at', '>=' , $start);
                    $totalApproved->whereDate('created_at', '<=' , $end);
                }

                $totalApproved = $totalApproved->count();

                // Completed

                /** 2.Total Sales Quantity */

                // Pending
                $totalPendingQue = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalPendingQue->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalPendingQue->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer Filter
                if(Auth::user()->role_id == 4){
                    $totalPendingQue->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if($request->filter_customer != ""){
                        $totalPendingQue->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_sales_specialist != ""){
                        $totalPendingQue->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalPendingQue->where('sales_specialist_id', Auth::id());
                }

                // Date Range Filter
                if($request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalPendingQue->whereDate('created_at', '>=' , $start);
                    $totalPendingQue->whereDate('created_at', '<=' , $end);
                }

                $totalPendingQue = $totalPendingQue->sum('total_quantity');

                // Approved
                $totalApprovedQue = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalApprovedQue->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalApprovedQue->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer Filter
                if(Auth::user()->role_id == 4){
                    $totalApprovedQue->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if($request->filter_customer != ""){
                        $totalApprovedQue->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_sales_specialist != ""){
                        $totalApprovedQue->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalApprovedQue->where('sales_specialist_id', Auth::id());
                }
                
                // Date Range filter
                if($request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalApprovedQue->whereDate('created_at', '>=' , $start);
                    $totalApprovedQue->whereDate('created_at', '<=' , $end);
                }

                $totalApprovedQue = $totalApprovedQue->sum('total_quantity');

                // Completed

                /** 2.Total Sales Revenue */

                // Pending
                $totalPendingRev = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalPendingRev->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalPendingRev->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer Filter
                if(Auth::user()->role_id == 4){
                    $totalPendingRev->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if($request->filter_customer != ""){
                        $totalPendingRev->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_sales_specialist != ""){
                        $totalPendingRev->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalPendingRev->where('sales_specialist_id', Auth::id());
                }

                // Date Range Filter
                if($request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalPendingRev->whereDate('created_at', '>=' , $start);
                    $totalPendingRev->whereDate('created_at', '<=' , $end);
                }

                $totalPendingRev = $totalPendingRev->sum('total_amount');

                // Approved
                $totalApprovedRev = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalApprovedRev->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalApprovedRev->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer Filter
                if(Auth::user()->role_id == 4){
                    $totalApprovedRev->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if($request->filter_customer != ""){
                        $totalApprovedRev->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent Filter
                if(Auth::user()->role_id == 1){
                    if($request->filter_sales_specialist != ""){
                        $totalApprovedRev->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalApprovedRev->where('sales_specialist_id', Auth::id());
                }

                // Date Range Filter
                if($request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalApprovedRev->whereDate('created_at', '>=' , $start);
                    $totalApprovedRev->whereDate('created_at', '<=' , $end);
                }

                $totalApprovedRev = $totalApprovedRev->sum('total_amount');

                $pending = [
                    'no' => ++$no,
                    'company_name' => $companyName,
                    'status' => 'Pending',
                    'total_promotion' => $totalPending,
                    'total_quantity' => $totalPendingQue,
                    'total_amount' => "₱ ".number_format_value($totalPendingRev, 2),
                ];
                $approved = [
                    'no' => ++$no,
                    'company_name' => $companyName,
                    'status' => 'Approved',
                    'total_promotion' => $totalApproved,
                    'total_quantity' => $totalApprovedQue,
                    'total_amount' => "₱ ".number_format_value($totalApprovedRev, 2),
                ];

                array_push($outputData, $pending);
                array_push($outputData, $approved);

                $grand_total_of_pending_sales_revenue += $totalPendingRev;
                $grand_total_of_approved_sales_revenue += $totalApprovedRev;
        }

        return ['status' => true, 'data' => $outputData, 'grand_total_of_pending_sales_revenue' => "₱ ".number_format_value($grand_total_of_pending_sales_revenue), 'grand_total_of_approved_sales_revenue' => "₱ ".number_format_value($grand_total_of_approved_sales_revenue)];

    }

    // public function getChartData(Request $request){
    //     $company = SapConnection::query();

    //     if($request->filter_company != ""){
    //         $company->where('id', $request->filter_company);
    //     }

    //     $company = $company->get();

    //     $pendingPromotion = [];
    //     $approvedPromotion = [];
    //     $cancelPromotion = [];
    //     $totalRevenue = [];
    //     $category = [];

    //     foreach($company as $key => $item){
    //         $companyName = $item->company_name;

    //         array_push($category, $companyName);
    //         /** 1. Number of Promo */
    //         // Pending
    //         $totalPending = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending'])->count();
    //         array_push($pendingPromotion, $totalPending);

    //         // Approved
    //         $totalApproved = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved'])->count();
    //         array_push($approvedPromotion, $totalApproved);

    //         // Revenue
    //         $revenue = CustomerPromotion::where('sap_connection_id', '=',$item->id)->sum('total_amount');
    //         array_push($totalRevenue, number_format($revenue, 2));

    //         // Cancel
    //         $canceled = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'canceled'])->count();
    //         array_push($cancelPromotion, $canceled);

    //     }
    //     $data = [];
    //     array_push($data, array('name' => 'Pending Promotion', 'data' => $pendingPromotion));
    //     array_push($data, array('name' => 'Approved Promotion', 'data' => $approvedPromotion));
    //     // array_push($data, array('name' => 'Total Revenue', 'data' => $totalRevenue));
    //     array_push($data, array('name' => 'Canceled Promotion', 'data' => $cancelPromotion));

    //     return ['status' => true, 'data' => $data, 'category' => $category];
    // }

    // public function getChartData(Request $request){
    //     $company = SapConnection::query();

    //     if($request->filter_company != ""){
    //         $company->where('id', $request->filter_company);
    //     }

    //     $company = $company->get();

    //     $pendingPromotion = [];
    //     $approvedPromotion = [];
    //     $cancelPromotion = [];
    //     $totalRevenue = [];
    //     $category = [];

    //     foreach($company as $key => $item){
    //         $companyName = $item->company_name;

    //         array_push($category, $companyName);
    //         /** 1. Number of Promo */
    //         // Pending
    //         $totalPending = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending'])->count();
    //         array_push($pendingPromotion, $totalPending);

    //         // Approved
    //         $totalApproved = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved'])->count();
    //         array_push($approvedPromotion, $totalApproved);

    //         // Revenue
    //         $revenue = CustomerPromotion::where('sap_connection_id', '=',$item->id)->sum('total_amount');
    //         array_push($totalRevenue, number_format($revenue, 2));

    //         // Cancel
    //         $canceled = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'canceled'])->count();
    //         array_push($cancelPromotion, $canceled);

    //     }
    //     $data = [];
    //     array_push($data, array('name' => 'Pending Promotion', 'data' => $pendingPromotion));
    //     array_push($data, array('name' => 'Approved Promotion', 'data' => $approvedPromotion));
    //     // array_push($data, array('name' => 'Total Revenue', 'data' => $totalRevenue));
    //     array_push($data, array('name' => 'Canceled Promotion', 'data' => $cancelPromotion));

    //     return ['status' => true, 'data' => $data, 'category' => $category];
    // }

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
            $totalActive = Promotions::where(['sap_connection_id' => $item->id, 'is_active' => '1'])->count();
            array_push($pendingPromotion, $totalActive);

            // Approved
            $totalAInactive = Promotions::where(['sap_connection_id' => $item->id, 'is_active' => '0'])->count();
            array_push($approvedPromotion, $totalAInactive);

            // Revenue
            // $revenue = CustomerPromotion::where('sap_connection_id', '=',$item->id)->sum('total_amount');
            // array_push($totalRevenue, number_format($revenue, 2));

            // // Cancel
            // $canceled = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'canceled'])->count();
            // array_push($cancelPromotion, $canceled);

        }
        $data = [];
        array_push($data, array('name' => 'Active Promotion', 'data' => $pendingPromotion));
        array_push($data, array('name' => 'Inactive Promotion', 'data' => $approvedPromotion));
        // array_push($data, array('name' => 'Total Revenue', 'data' => $totalRevenue));
        //array_push($data, array('name' => 'Canceled Promotion', 'data' => $cancelPromotion));

        return ['status' => true, 'data' => $data, 'category' => $category];
    }

    public function export(Request $request){
        $filter = collect();
        if(@$request->data){
          $filter = json_decode(base64_decode($request->data));
        }

        $data = $this->getReportResultData($filter);

        $records = array();
        foreach($data as $key => $value){

            $records[] = array(
                            'no' => $key + 1,
                            'business_unit' => @$value['company_name'] ?? "-",
                            'status' => @$value['status'] ?? "-",
                            'no_of_promotion' => @$value['total_promotion'] ?? "-",
                            'total_sales_quantity' => @$value['total_quantity'] ?? "-",
                            'total_sales_revenue' => @$value['total_amount'] ?? "-",
                          );
        }
        if(count($records)){
            $title = 'Promotion Report '.date('dmY').'.xlsx';
            return Excel::download(new PromotionReportExport($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }

    public function getReportResultData($request){
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6){
            $company = SapConnection::query();
            if($request->filter_company != ""){
                $company->where('id', $request->filter_company);
            }
            $company = $company->get();
        }else{
            $user = User::where('id',Auth::id())->first();
            $company = SapConnection::where(['id'=>$user->sap_connection_id])->get();
        }
        $outputData = [];
        $no = 0;

        foreach($company as $key => $item){
                $companyName = $item->company_name;

                /** 1. Number of Promo */
                // Pending
                $totalPending = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalPending->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalPending->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer Filter
                if(Auth::user()->role_id == 4){
                    $totalPending->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if(@$request->filter_customer != ""){
                        $totalPending->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_sales_specialist != ""){
                        $totalPending->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalPending->where('sales_specialist_id', Auth::id());
                }

                // Date Range Filter
                if(@$request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalPending->whereDate('created_at', '>=' , $start);
                    $totalPending->whereDate('created_at', '<=' , $end);
                }

                $totalPending = $totalPending->count();

                // Approved
                $totalApproved = CustomerPromotion::with('customer_user')->where(['sap_connection_id' => $item->id, 'status' => 'approved']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalApproved->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalApproved->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer filter
                if(Auth::user()->role_id == 4){
                    $totalApproved->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if(@$request->filter_customer != ""){
                        $totalApproved->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_sales_specialist != ""){
                        $totalApproved->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalApproved->where('sales_specialist_id', Auth::id());
                }

                // Date range Filter
                if(@$request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalApproved->whereDate('created_at', '>=' , $start);
                    $totalApproved->whereDate('created_at', '<=' , $end);
                }

                $totalApproved = $totalApproved->count();

                // Completed

                /** 2.Total Sales Quantity */

                // Pending
                $totalPendingQue = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalPendingQue->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalPendingQue->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer Filter
                if(Auth::user()->role_id == 4){
                    $totalPendingQue->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if(@$request->filter_customer != ""){
                        $totalPendingQue->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_sales_specialist != ""){
                        $totalPendingQue->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalPendingQue->where('sales_specialist_id', Auth::id());
                }

                // Date Range Filter
                if(@$request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalPendingQue->whereDate('created_at', '>=' , $start);
                    $totalPendingQue->whereDate('created_at', '<=' , $end);
                }

                $totalPendingQue = $totalPendingQue->sum('total_quantity');

                // Approved
                $totalApprovedQue = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalApprovedQue->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalApprovedQue->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer Filter
                if(Auth::user()->role_id == 4){
                    $totalApprovedQue->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if(@$request->filter_customer != ""){
                        $totalApprovedQue->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_sales_specialist != ""){
                        $totalApprovedQue->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalApprovedQue->where('sales_specialist_id', Auth::id());
                }
                
                // Date Range filter
                if(@$request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalApprovedQue->whereDate('created_at', '>=' , $start);
                    $totalApprovedQue->whereDate('created_at', '<=' , $end);
                }

                $totalApprovedQue = $totalApprovedQue->sum('total_quantity');

                // Completed

                /** 2.Total Sales Revenue */

                // Pending
                $totalPendingRev = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'pending']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalPendingRev->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalPendingRev->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer Filter
                if(Auth::user()->role_id == 4){
                    $totalPendingRev->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if(@$request->filter_customer != ""){
                        $totalPendingRev->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_sales_specialist != ""){
                        $totalPendingRev->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalPendingRev->where('sales_specialist_id', Auth::id());
                }

                // Date Range Filter
                if(@$request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalPendingRev->whereDate('created_at', '>=' , $start);
                    $totalPendingRev->whereDate('created_at', '<=' , $end);
                }

                $totalPendingRev = $totalPendingRev->sum('total_amount');

                // Approved
                $totalApprovedRev = CustomerPromotion::where(['sap_connection_id' => $item->id, 'status' => 'approved']);

                // Manager Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_manager != ""){
                        $salesAgent = User::where('parent_id',$request->filter_manager)->pluck('id')->toArray();
                        $totalApprovedRev->whereIn('sales_specialist_id', $salesAgent);
                    }
                }else if(Auth::user()->role_id == 6){
                    $salesAgent = User::where('parent_id',Auth::id())->pluck('id')->toArray();
                    $totalApprovedRev->whereIn('sales_specialist_id', $salesAgent);
                }

                // Customer Filter
                if(Auth::user()->role_id == 4){
                    $totalApprovedRev->whereHas('user',function($q) use($request){
                        $q->where('customer_id', Auth::id());
                    });
                }else{
                    if(@$request->filter_customer != ""){
                        $totalApprovedRev->whereHas('user',function($q) use($request){
                            $q->where('customer_id', $request->filter_customer);
                        });
                    }
                }

                // Sales Agent Filter
                if(Auth::user()->role_id == 1){
                    if(@$request->filter_sales_specialist != ""){
                        $totalApprovedRev->where('sales_specialist_id', $request->filter_sales_specialist);
                    }
                }else if(Auth::user()->role_id == 2){
                    $totalApprovedRev->where('sales_specialist_id', Auth::id());
                }

                // Date Range Filter
                if(@$request->filter_date_range != ""){
                    $date = explode(" - ", $request->filter_date_range);
                    $start = date("Y-m-d H:i:s", strtotime($date[0]));
                    $end = date("Y-m-d H:i:s", strtotime($date[1]));
                    $totalApprovedRev->whereDate('created_at', '>=' , $start);
                    $totalApprovedRev->whereDate('created_at', '<=' , $end);
                }

                $totalApprovedRev = $totalApprovedRev->sum('total_amount');

                $pending = [
                    'no' => ++$no,
                    'company_name' => $companyName,
                    'status' => 'Pending',
                    'total_promotion' => $totalPending,
                    'total_quantity' => $totalPendingQue,
                    'total_amount' => "₱ ".number_format_value($totalPendingRev, 2),
                ];
                $approved = [
                    'no' => ++$no,
                    'company_name' => $companyName,
                    'status' => 'Approved',
                    'total_promotion' => $totalApproved,
                    'total_quantity' => $totalApprovedQue,
                    'total_amount' => "₱ ".number_format_value($totalApprovedRev, 2),
                ];

                array_push($outputData, $pending);
                array_push($outputData, $approved);
        }

        return $outputData;
    }
}
