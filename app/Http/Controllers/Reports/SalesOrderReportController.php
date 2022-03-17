<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Order;
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
        $pending_data = Quotation::doesntHave('order')->where('document_status','bost_Open')->where('cancelled', "No")->get();

        dd(count($pending_data));


    }

}
