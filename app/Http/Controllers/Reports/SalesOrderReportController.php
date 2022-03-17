<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SapConnection;

use DB;
use DataTables;

class SalesOrderReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('report.sales-order-report.index', compact('company'));
    }

    public function getAll(Request $request)
    {
        // For Pending
        $pending_total_sales_orders = $pending_total_sales_quantity = $pending_total_sales_revenue = 0;

        $pending_quotation_item = QuotationItem::whereHas('quotation', function($q){
                                                    $q->doesntHave('order')->where('document_status','bost_Open')->where('cancelled', "No");
                                                })->get()->toArray();
        $pending_total_sales_orders = count($pending_quotation_item);

        if(!empty($pending_quotation_item)){
            $pending_total_sales_quantity = array_sum(array_column($pending_quotation_item, 'quantity'));
            $pending_total_sales_revenue = round(array_sum(array_column($pending_quotation_item, 'price_after_vat')), 2);
        }


        // For Approved
        $approved_total_sales_orders = $approved_total_sales_quantity = $approved_total_sales_revenue = 0;

        $approved_order_item = OrderItem::whereHas('order', function($q){
                                                    $q->doesntHave('invoice')->where('document_status','bost_Open')->where('u_sostat', "OP");
                                                })->get()->toArray();
        $approved_total_sales_orders = count($approved_order_item);

        if(!empty($approved_order_item)){
            $approved_total_sales_quantity = array_sum(array_column($approved_order_item, 'quantity'));
            $approved_total_sales_revenue = round(array_sum(array_column($approved_order_item, 'price_after_vat')), 2);
        }


        // For Disapproved 
        // quotation table
        $disapproved_total_sales_orders = $disapproved_total_sales_quantity = $disapproved_total_sales_revenue = 0;

        $disapproved_quotation_item = QuotationItem::whereHas('quotation', function($q){
                                                    $q->doesntHave('order');

                                                    $q->where(function($query){
                                                        $query->orwhere(function($q1){
                                                           $q1->where('document_status','Cancelled');
                                                        });

                                                        $query->orwhere(function($q1){
                                                            $q1->where('cancelled', "Yes");
                                                        });
                                                    });
                                                })->get()->toArray();

        $disapproved_total_sales_orders = count($disapproved_quotation_item);

        if(!empty($disapproved_quotation_item)){
            $disapproved_total_sales_quantity += array_sum(array_column($disapproved_quotation_item, 'quantity'));
            $disapproved_total_sales_revenue += round(array_sum(array_column($disapproved_quotation_item, 'price_after_vat')), 2);
        }

        // order table
        $disapproved_order_item = OrderItem::whereHas('order', function($q){
                                                    $q->has('quotation')->doesntHave('invoice');

                                                    $q->where(function($query){
                                                        $query->orwhere(function($q1){
                                                           $q1->where('document_status','Cancelled');
                                                        });

                                                        $query->orwhere(function($q1){
                                                            $q1->where('cancelled', "Yes");
                                                        });
                                                    });
                                                })->get()->toArray();

        $disapproved_total_sales_orders += count($disapproved_order_item);

        if(!empty($disapproved_order_item)){
            $disapproved_total_sales_quantity += array_sum(array_column($disapproved_order_item, 'quantity'));
            $disapproved_total_sales_revenue += round(array_sum(array_column($disapproved_order_item, 'price_after_vat')), 2);
        }

        $data = compact(
                            'pending_total_sales_quantity',
                            'pending_total_sales_revenue', 
                            'pending_total_sales_orders',

                            'approved_total_sales_quantity',
                            'approved_total_sales_revenue', 
                            'approved_total_sales_orders',

                            'disapproved_total_sales_quantity',
                            'disapproved_total_sales_revenue', 
                            'disapproved_total_sales_orders',
                        );


        $response = [ 'status' => true, 'data' => $data, 'message' => 'Report fetched successfully!'];
        return $response;
    }

}
