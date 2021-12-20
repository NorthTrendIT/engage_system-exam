<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CustomerDeliverySchedule;
use App\Models\User;
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
                $territory_ids = [];
                if(isset($input['territory_id'])){
                    foreach ($input['territory_id'] as $key => $value) {
                        $territory_ids[] = $value;

                        TerritorySalesSpecialist::updateOrCreate(
                                                    array(
                                                        'territory_id' => $value,
                                                        'user_id' => $user->id,
                                                    ),
                                                    array(
                                                        'territory_id' => $value,
                                                        'user_id' => $user->id,
                                                    )
                                                );
                        
                    }
                    TerritorySalesSpecialist::where('user_id', $user->id)->whereNotIn('territory_id',$territory_ids)->delete();
                }else{
                    TerritorySalesSpecialist::where('user_id', $user->id)->delete();
                }
            }
            
            if(isset($input['id'])){
                $message = "Record updated successfully.";
            }else{
                $message = "Record created successfully.";
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
                            ->addColumn('date', function($row) {

                                $dates = array_map( function ( $t ) {
                                               return date('M d, Y',strtotime($t));
                                            }, array_column( $row->toArray(), 'date' ) );

                                return implode(" | ", $dates);

                                // return implode(" | ", array_column( $row->toArray(), 'date' ));
                            })
                            ->addColumn('customer', function($row) {
                                return @$row->first()->user->sales_specialist_name ?? "-";
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('customer-delivery-schedule.edit',$row->first()->user_id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('customer-delivery-schedule.destroy',$row->first()->user_id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }

    public function getCustomerList(Request $request){
        $search = $request->search;

        $data = User::where('role_id',4)->where('is_active',true)->orderBy('sales_specialist_name','asc');

        if($search != ''){
            $data->where('sales_specialist_name', 'like', '%' .$search . '%');
        }

        $data = $data->limit(50)->get();

        return response()->json($data);
    }
}
