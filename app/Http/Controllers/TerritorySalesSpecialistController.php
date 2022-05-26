<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Territory;
use App\Models\User;
use App\Models\TerritorySalesSpecialist;
use Validator;
use DataTables;

class TerritorySalesSpecialistController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('territory-sales-specialist.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('territory-sales-specialist.add');
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
                    'territory_id' => 'required|array',
                    'territory_id.*' => 'required|exists:territories,id',
                    'sales_specialist_id' => 'required|exists:users,id',
                );

        $message = array(
                        'territory_id.required' => 'Please select territory.',
                        'sales_specialist_id.exists' => 'The selected sales specialist is not a valid.'
                    );

        $validator = Validator::make($input, $rules, $message);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            // Create Time
            if(!isset($input['id'])){
                $count = TerritorySalesSpecialist::where('user_id', $input['sales_specialist_id'])->count();

                if($count > 0){
                    return $response = ['status' => false,'message' => "The selected sales specialist is already used."];
                }
            }

            // In edit time select another 
            if(isset($input['id']) && $input['id'] != $input['sales_specialist_id']){

                $count = TerritorySalesSpecialist::where('user_id', $input['sales_specialist_id'])->count();

                if($count > 0){
                    return $response = ['status' => false,'message' => "The selected sales specialist is already used."];
                }

                TerritorySalesSpecialist::where('user_id', $input['id'])->delete();
            }

            $user = User::where('id', $input['sales_specialist_id'])->where('is_active', 1)->where('role_id', 2)->first();

            if($user){
                
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
        $edit = User::where('id', $id)->where('role_id', 2)->where('is_active', 1)->firstOrFail();

        return view('territory-sales-specialist.add',compact('edit'));
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
        $user = User::where('id', $id)->where('role_id', 2)->where('is_active', 1)->firstOrFail();
        if(!is_null($user)){
            TerritorySalesSpecialist::where('user_id', $user->id)->delete();

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = TerritorySalesSpecialist::with(['user','territory'])
                                            ->has('user')
                                            ->has('territory')
                                            ->select('user_id','territory_id')
                                            ->orderBy('id', 'desc')
                                            ->get()
                                            ->groupBy('user_id');


        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('territory', function($row) {

                                $descriptions = array_map( function ( $t ) {
                                               return $t['description'];
                                            }, array_column( $row->toArray(), 'territory' ) );

                                return implode(" | ", $descriptions);
                            })
                            ->addColumn('sales_specialist', function($row) {
                                return @$row->first()->user->sales_specialist_name ?? "-";
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('territory-sales-specialist.edit',$row->first()->user_id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('territory-sales-specialist.destroy',$row->first()->user_id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }

    public function getSalesSpecialist(Request $request){
        $search = $request->search;

        $data = User::where('sales_specialist_name','!=','-No Sales Employee-')->where('role_id',2)->where('is_active',true)->orderBy('sales_specialist_name','asc');

        if($search != ''){
            // $data->where('sales_specialist_name', 'like', '%' .$search . '%');

            $data->where(function($q) use ($search) {
                $q->orwhere('sales_specialist_name','LIKE',"%".$search."%");
                // $q->orwhere('email','LIKE',"%".$search."%");
                $q->orwhere('sales_employee_code','LIKE',"%".$search."%");
            });
        }

        $data = $data->limit(50)->get();

        return response()->json($data);
    }

    public function getTerritory(Request $request){
        $search = $request->search;

        $data = Territory::where('territory_id','!=','-2')->where('is_active',true)->orderBy('description','asc');
        
        if($search != ''){
            $data->where('description', 'like', '%' .$search . '%');
        }

        $data = $data->limit(50)->get();

        return response()->json($data);
    }

}
