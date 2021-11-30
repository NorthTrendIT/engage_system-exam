<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Territory;
use App\Models\User;
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
                    'territory_id' => 'required|exists:territories,id|unique:users,territory_id,NULL,id,deleted_at,NULL',
                    'sales_specialist_id' => 'required|exists:users,id,territory_id,NULL',
                );

        if(isset($input['id'])){
            $rules['territory_id'] = 'required|exists:territories,id|unique:users,territory_id,'.$input['id'].',id,deleted_at,NULL';
            $rules['sales_specialist_id'] = 'required|exists:users,id,id,'.$input['id'];
        }

        $message = array(
                        'territory_id.unique' => 'The selected territory is already used.',
                        'sales_specialist_id.exists' => 'The selected sales specialist is already used.'
                    );

        $validator = Validator::make($input, $rules, $message);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            $user = User::where('id', $input['sales_specialist_id'])->where('role_id', 2)->first();

            if($user){
                $user->update(
                                ['territory_id' => $input['territory_id']]
                            );
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
        $edit = User::where('id', $id)->where('role_id', 2)->whereNotNull('territory_id')->firstOrFail();
        $territory = Territory::find($edit->territory_id);

        return view('territory-sales-specialist.add',compact('edit','territory'));
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
        $data = User::where('id', $id)->where('role_id', 2)->firstOrFail();
        if(!is_null($data)){
            $data->update(
                            ['territory_id' => NULL]
                        );

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = User::where('users.role_id', 2)->whereNotNull('users.territory_id');

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('sales_specialist_name','LIKE',"%".$request->filter_search."%");
                
                $q->orWhereHas('territory',function($q1) use ($request) {
                    $q1->where('description','LIKE',"%".$request->filter_search."%");
                });

            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('users.id', 'desc');
        });

        return DataTables::of($data)
                            ->addColumn('territory', function($row) {
                                return $row->territory->description;
                            })
                            ->addColumn('sales_specialist', function($row) {
                                return $row->sales_specialist_name ?? "-";
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('territory-sales-specialist.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('territory-sales-specialist.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                return $btn;
                            })
                            ->orderColumn('territory', function ($query, $order) {
                                $query->select('users.*')->join('territories', 'users.territory_id', '=', 'territories.id')
                                    ->orderBy('territories.description', $order);
                                
                            })
                            ->orderColumn('sales_specialist', function ($query, $order) {
                                $query->orderBy('sales_specialist_name', $order);
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }

    public function getSalesSpecialist(Request $request){
        $search = $request->search;

        $data = User::where('sales_specialist_name','!=','-No Sales Employee-')->where('role_id',2)->where('is_active',true)->orderBy('sales_specialist_name','asc');

        if($search != ''){
            $data->where('sales_specialist_name', 'like', '%' .$search . '%');
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
