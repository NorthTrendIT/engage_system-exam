<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\SAPCustomer;
use App\Jobs\SyncCustomers;
use App\Models\Customer;
use DataTables;

class CustomerController extends Controller
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
        return view('customer.index');
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
        $data = Customer::where('id',$id)->firstOrFail();

        return view('customer.view',compact('data'));
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

    public function syncCustomers(){
        try {

            // Add Sync Customer data log.
            add_log(\Auth::id(), 15, null, \Request::ip());

            // Save Data of customer in database
            SyncCustomers::dispatch('TEST-APBW', 'manager', 'test');

            $response = ['status' => true, 'message' => 'Sync Customer successfully !'];
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Something went wrong !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Customer::query();

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('card_code','LIKE',"%".$request->filter_search."%");
                $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('email','LIKE',"%".$request->filter_search."%");
                $q->orwhere('credit_limit','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {
                                $btn = ' <a href="' . route('customer.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                return $btn;
                            })
                            ->addColumn('name', function($row) {
                                $html = "";

                                $html .= '<div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="'.asset('assets/assets/media/default_user.png').'" alt="">
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a href="javascript:" class="text-dark fw-bolder text-hover-primary fs-6">';

                                $html .= @$row->card_name ?? " ";

                                $html .= '</a>
                                                <span class="text-muted fw-bold text-muted d-block fs-7">Code: ';

                                $html .= @$row->card_code ?? " ";

                                if($row->email != null){
                                    $html .= " | Email: ".$row->email;
                                }

                                $html .= '</span>
                                            </div>
                                        </div>';

                                return $html;
                            })
                            ->addColumn('status', function($row) {
                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                                }else{
                                    $btn .= '<a href="javascript:" class="btn btn-sm btn-light-danger btn-inline status">Inctive</a>';
                                }

                                return $btn;
                            })
                            ->addColumn('class', function($row) {
                                $html = "";
                                return $html;
                            })
                            ->addColumn('credit_limit', function($row) {
                                return @$row->credit_limit ?? "-";
                            })
                            ->addColumn('created_date', function($row) {
                                return date('M d, Y',strtotime($row->created_date));
                            })
                            ->orderColumn('name', function ($query, $order) {
                                $query->orderBy('card_name', $order);
                            })
                            ->orderColumn('created_date', function ($query, $order) {
                                $query->orderBy('created_date', $order);
                            })
                            ->orderColumn('city', function ($query, $order) {
                                $query->orderBy('city', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->orderColumn('credit_limit', function ($query, $order) {
                                $query->orderBy('credit_limit', $order);
                            })
                            ->rawColumns(['name', 'role','status','action','credit_limit'])
                            ->make(true);
    }
}
