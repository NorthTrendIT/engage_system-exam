<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CustomerDeliverySchedule;
use App\Models\User;
use App\Models\Customer;
use App\Models\Territory;
use Validator;
use DataTables;


class CustomerDeliveryScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('customer-delivery-schedule.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customer-delivery-schedule.add');
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
                    'customer_id' => 'required|array',
                    'customer_id.*' => 'required|exists:users,id,role_id,4',
                    'date' => 'required',
                );

        $message = array(
                        'customer_id.required' => 'Please select customers.',
                        'date.required' => 'Please select schedule dates.',
                    );

        $validator = Validator::make($input, $rules, $message);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{


            if(!isset($input['id'])){

                if(@$input['customer_id']){

                    foreach(@$input['customer_id'] as $key => $value){

                        foreach(explode(",", @$input['date']) as $d_key => $d_value){

                            CustomerDeliverySchedule::create(
                                                        [
                                                            'user_id' => $value,
                                                            'date' => date("Y-m-d",strtotime(str_replace("/", "-", $d_value))),
                                                        ]
                                                    );

                        }
                    }
                }

            }else{
                $dates = [];
                CustomerDeliverySchedule::where('user_id', $input['id'])->delete();
                if(isset($input['date'])){
                    foreach (explode(",", $input['date']) as $key => $value) {
                        $dates[] = $value;

                        CustomerDeliverySchedule::create(
                                                    [
                                                        'user_id' => $input['id'],
                                                        'date' => date("Y-m-d",strtotime(str_replace("/", "-", $value))),
                                                    ]
                                                );
                        
                    }
                }
            }
            
            if(isset($input['id'])){
                $message = "Record updated successfully.";

                //save log
                add_log(39, $input);
            }else{
                $message = "Record created successfully.";

                //save log
                add_log(38, $input);
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
        $data = User::where('id', $id)->where('role_id', 4)->firstOrFail();

        $dates = array();

        $dates = "[";
        foreach (@$data->customer_delivery_schedules as $value) {
            // $dates[] = array(
            //                     // 'allDay' => true,
            //                     // 'title' => "",
            //                     'start' => date("Y-m-d",strtotime($value->date)),
            //                     'end' => date("Y-m-d",strtotime($value->date)),
            //                     // 'className' => "calendar-event-enduring",
            //                     "display" => 'background'
            //                 );

            // $dates[] = array(
            //                     'startDate' => 'new Date('. date("Y-m-d",strtotime($value->date)) .')',
            //                     'endDate' => 'new Date('. date("Y-m-d",strtotime($value->date)) .')',
            //                     "class" => 'active'
            //                 );

            $dates .= "{startDate: new Date('".date("Y-m-d",strtotime($value->date))."'), endDate: new Date('".date("Y-m-d",strtotime($value->date))."'),class:'active'},";
        }
        $dates .= ']';

        return view('customer-delivery-schedule.view',compact('data','dates'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = User::where('id', $id)->where('role_id', 4)->firstOrFail();

        $dates = "";

        if($edit->customer_delivery_schedules){

            $dates = array_map( function ( $t ) {
                           return date('d/m/Y',strtotime($t));
                        }, array_column( $edit->customer_delivery_schedules->toArray(), 'date' ) );

            $dates = implode(",", $dates);
        }

        return view('customer-delivery-schedule.add',compact('edit','dates'));
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
        $user = User::where('id', $id)->where('role_id', 4)->firstOrFail();
        if(!is_null($user)){

            //save log
            add_log(40, $input);

            CustomerDeliverySchedule::where('user_id', $user->id)->delete();

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = CustomerDeliverySchedule::with(['user'])
                                            ->has('user')
                                            ->select('user_id','date')
                                            ->orderBy('id', 'desc')
                                            ->get()
                                            ->groupBy('user_id');


        return DataTables::of($data)
                            // ->addColumn('date', function($row) {

                            //     $dates = array_map( function ( $t ) {
                            //                    return date('M d, Y',strtotime($t));
                            //                 }, array_column( $row->toArray(), 'date' ) );

                            //     return implode(" | ", $dates);
                            // })
                            ->addIndexColumn()
                            ->addColumn('customer', function($row) {
                                return @$row->first()->user->sales_specialist_name ?? "-";
                            })
                            ->addColumn('territory', function($row) {
                                return @$row->first()->user->customer->territories->description ?? "-";
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('customer-delivery-schedule.edit',$row->first()->user_id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                    <i class="fa fa-pencil"></i>
                                  </a>';

                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('customer-delivery-schedule.destroy',$row->first()->user_id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete mr-10">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                $btn .= ' <a href="' . route('customer-delivery-schedule.show',$row->first()->user_id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                  </a>';

                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }

    public function getCustomerList(Request $request){
        $search = $request->search;
        $territory = $request->territory;

        if($territory != ''){
            $data = User::where('role_id',4)->where('is_active',true)->orderBy('sales_specialist_name','asc');

            if($search != ''){
                $data->where('sales_specialist_name', 'like', '%' .$search . '%');
            }

            $data->whereHas('customer', function($q) use ($territory){
                $q->where('territory', $territory);
            });
            
            $data = $data->limit(50)->get();

        }else{
            $data = collect();
        }


        return response()->json($data);
    }


    public function getTerritory(Request $request){
        $search = $request->search;

        $data = Territory::orderby('description','asc')->select('territory_id','description')->limit(50);

        if($search != ''){
            $data->where('description', 'like', '%' .$search . '%');
        }

        $data = $data->get();

        // foreach($data as $value){
        //     $response[] = array(
        //         "id" => $value->territory_id,
        //         "text" => $value->description
        //     );
        // }

        return response()->json($data);
    }

    public function ssView(Request $request){

        if($request->ajax()){
            $data = User::where('customer_id', $request->customer_id)->where('role_id', 4)->first();
            $dates = array();
            if(@$data->customer_delivery_schedules){
                foreach ($data->customer_delivery_schedules as $value) {
                    $dates[] = array(
                                        // 'allDay' => true,
                                        // 'title' => "",
                                        'start' => date("Y-m-d",strtotime($value->date)),
                                        'end' => date("Y-m-d",strtotime($value->date)),
                                        // 'className' => "calendar-event-enduring",
                                        "display" => 'background'
                                    );
                }
            }

            return response()->json($dates);
        }

        return view('customer-delivery-schedule.ss_view');
    }

    public function getSsCustomerList(Request $request){
        if(userrole() == 1){
            $customers = Customer::has('user.customer_delivery_schedules');
        }else{
            $customers = Customer::has('user.customer_delivery_schedules')->whereHas('sales_specialist', function($q) {
                        return $q->where('ss_id', userid());
                    });
        }

        if($request->filter_search != ""){
            $customers->where(function($q) use ($request) {
                $q->orwhere('card_code','LIKE',"%".$request->filter_search."%");
                $q->orwhere('card_name','LIKE',"%".$request->filter_search."%");
            });
        }

        $customers = $customers->get();

        return response()->json($customers);
    }


    public function allView(Request $request){

        if($request->ajax()){
            $data = CustomerDeliverySchedule::query();

            if($request->customer_id){
                $data->whereHas('user', function($q) use ($request) {
                        return $q->where('customer_id', $request->customer_id);
                    });
            }

            $data = $data->get();
            
            $dates = array();
            if(count($data)){
                foreach ($data as $value) {
                    $dates[] = array(
                                        'allDay' => true,
                                        'title' => @$value->user->customer->card_name." ".(@$value->user->customer->card_code ? "(Code: ".$value->user->customer->card_code.")" : ""),
                                        'start' => date("Y-m-d",strtotime($value->date)),
                                        'end' => date("Y-m-d",strtotime($value->date)),
                                        'className' => "calendar-event-enduring",
                                        // "display" => 'background'
                                    );
                }
            }

            return response()->json($dates);
        }

        return view('customer-delivery-schedule.all_view');
    }
}
