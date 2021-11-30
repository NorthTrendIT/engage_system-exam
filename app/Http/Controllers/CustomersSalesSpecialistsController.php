<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomersSalesSpecialist;
use App\Models\Customer;
use App\Models\User;
use Validator;
use DataTables;

class CustomersSalesSpecialistsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('customers-sales-specialist.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers-sales-specialist.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $rules = array(
                    'customer_id' => 'required',
                    'ss_ids' => 'required',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            // dd($input);
            if(isset($input['id'])){
                $message = "Details updated successfully.";
            }else{
                $message = "Created successfully.";
            }

            if(isset($input['ss_ids']) ){
                $s_ids = $input['ss_ids'];
                CustomersSalesSpecialist::where('customer_id', $input['customer_id'])->delete();
                foreach($s_ids as $value){
                    $ss = new CustomersSalesSpecialist();
                    $ss->customer_id = $input['customer_id'];
                    $ss->ss_id = $value;
                    $ss->save();
                }
            }

            $response = ['status'=>true,'message'=>$message];
        }

        return $response;
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
        $edit = CustomersSalesSpecialist::where('customer_id', $id)->with( 'sales_person')->get();
        $customer = Customer::find($id);

        return view('customers-sales-specialist.add',compact('edit','customer'));
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
        $data = CustomersSalesSpecialist::where('customer_id', $id)->get();
        if(!is_null($data)){
            CustomersSalesSpecialist::where('customer_id', $id)->delete();

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Customer::whereHas('sales_specialist');

        // dd($data);

        if($request->filter_search != ""){
            // $data->where(function($q) use ($request) {
            //     $q->orwhere('locations.name','LIKE',"%".$request->filter_search."%");
            // });
            $data->with('sales_specialist.sales_person')->where(function($q) use ($request) {
                $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
            });
        } else {
            $data->with('sales_specialist.sales_person');
        }

        $data->when(!isset($request->order), function ($q) {
            // $q->orderBy('locations.id', 'desc');
        });

        return DataTables::of($data)
                            ->addColumn('customer', function($row) {
                                return $row->card_name;
                            })
                            ->addColumn('sales_specialist', function($row) {
                                $ss = array();
                                if(!empty($row->sales_specialist)){
                                    foreach($row->sales_specialist as $value){
                                        if(!empty($value->sales_person->sales_specialist_name)){
                                            $ss[] = $value->sales_person->sales_specialist_name;
                                        }
                                    }
                                }
                                return implode(" | ",$ss);
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('customers-sales-specialist.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('customers-sales-specialist.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }

    public function getSalseSpecialist(Request $request){
        $search = $request->search;

        if($search == ''){
            $data = User::whereHas(
                        'role', function($q){
                            $q->where('name', '=' ,'Sales Specialist');
                        }
                    )->orderby('sales_specialist_name','asc')->select('id','sales_specialist_name')->limit(50)->get();
        }else{
            $data = User::whereHas(
                'role', function($q){
                    $q->where('name', '=' ,'Sales Specialist');
                }
            )->orderby('sales_specialist_name','asc')->select('id','sales_specialist_name')->where('sales_specialist_name', 'like', '%' .$search . '%')->limit(50)->get();
        }

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->sales_specialist_name
            );
        }

        return response()->json($response);
    }

    function getCustomers(Request $request){
        $search = $request->search;

        if($search == ''){
            $data = Customer::orderby('card_name','asc')->select('id','card_name')->limit(50)->get();
        }else{
            $data = Customer::orderby('card_name','asc')->select('id','card_name')->where('card_name', 'like', '%' .$search . '%')->limit(50)->get();
        }

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->card_name
            );
        }

        return response()->json($response);
    }
}
