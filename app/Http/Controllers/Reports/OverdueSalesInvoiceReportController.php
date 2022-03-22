<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\SapConnection;

use DB;
use DataTables;

class OverdueSalesInvoiceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = SapConnection::all();
        return view('report.overdue-sales-invoice-report.index', compact('company'));
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

    public function getAll(Request $request){
        $date = date('Y-m-d', strtotime('-2 months'));

        $data = Invoice::orderBy('id', 'DESC');

        $data->where(function($query){
            $query->orwhere(function($q){
                $q->where('cancelled', '!=','No')->where('document_status', '!=', 'Cancelled');
            });

            $query->orwhere(function($q){
                $q->where('u_sostat', '!=','CM')->where('document_status', 'bost_Open');
            });
        });

        $data->whereDate('created_at', '<=', $date);

        $data = $data->get();

        $number_of_overdue_invoices = count($data);
        $total_amount_of_overdue_invoices = $data->sum('doc_total');

        $data = compact(
                        'data',
                        'number_of_overdue_invoices',
                        'total_amount_of_overdue_invoices',
                    );

        return $response = [ 'status' => true , 'message' => 'Report details fetched successfully !' , 'data' => $data ];
    }
}
