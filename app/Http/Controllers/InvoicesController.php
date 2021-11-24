<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPInvoices;
use App\Jobs\SyncInvoices;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use DataTables;

class InvoicesController extends Controller
{
    public function __construct(){

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('invoices.index');
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

    public function syncInvoices(){
        try {

            // Add Sync Invoice data log.
            add_log(\Auth::id(), 17, null, null);

            // Save Data of invoice in database
            SyncInvoices::dispatch('TEST-APBW', 'manager', 'test');

            $response = ['status' => true, 'message' => 'Sync Invoices successfully !'];
        } catch (\Exception $e) {
            dd($e);
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Invoice::query();

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('doc_type','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('type', function($row) {
                                return $row->doc_type;
                            })
                            ->addColumn('name', function($row) {
                                return $row->card_name;
                            })
                            ->addColumn('total', function($row) {
                                return $row->doc_total;
                            })
                            ->addColumn('date', function($row) {
                                return date('M d, Y',strtotime($row->doc_date));
                            })
                            ->addColumn('due_date', function($row) {
                                return date('M d, Y',strtotime($row->doc_due_date));
                            })
                            ->orderColumn('type', function ($query, $order) {
                                $query->orderBy('doc_type', $order);
                            })
                            ->orderColumn('name', function ($query, $order) {
                                $query->orderBy('card_name', $order);
                            })
                            ->orderColumn('total', function ($query, $order) {
                                $query->orderBy('doc_total', $order);
                            })
                            ->orderColumn('date', function ($query, $order) {
                                $query->orderBy('doc_date', $order);
                            })
                            ->orderColumn('due_date', function ($query, $order) {
                                $query->orderBy('doc_due_date', $order);
                            })
                            ->make(true);
    }
}
