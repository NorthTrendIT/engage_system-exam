<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductFeatures;
use Validator;
use DataTables;
use Auth;

class ProductFeaturesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('productfeatures.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('productfeatures.add');
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
                    'name' => 'required|string|max:185',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){
                $record = ProductFeatures::find($input['id']);
                $message = "Feature updated successfully.";
            }else{
                $record = new ProductFeatures();
                $message = "Feature created successfully.";
            }
            $record->fill($input)->save();

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
        $edit = ProductFeatures::where('id',$id)->firstOrFail();

        return view('productfeatures.add',compact('edit'));
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
        $data = ProductFeatures::find($id);
        if(!is_null($data)){
            $data->delete();
            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = ProductFeatures::orderBy('name','asc')->get();

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $btn = '<a href="' . route('productfeatures.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                        <i class="fa fa-pencil"></i>
                        </a>';
                    $btn .= ' <a href="javascript:void(0)" data-url="' . route('productfeatures.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                        <i class="fa fa-trash"></i>
                        </a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
    }
}
