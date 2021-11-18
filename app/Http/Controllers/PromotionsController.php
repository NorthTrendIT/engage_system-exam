<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotions;
use Validator;
use DataTables;
use Auth;

class PromotionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('promotions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('promotions.add');
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
                    'promotion_type_id' => 'required',
                    'title' => 'required|string|max:185',
                    'discount_percentage' => 'required',
                );

        if(request()->hasFile('promo_image')){
            $rules['promo_image'] = "required|max:5000|mimes:jpeg,png,jpg,eps,bmp,tif,tiff,webp";
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){
                $promotion = Promotions::find($input['id']);
                $message = "Promotion updated successfully.";
            }else{
                $promotion = new Promotions();
                $message = "Promotion created successfully.";
            }

            $old_promo_image = file_exists(public_path('sitebucket/promotion/') . "/" . $promotion->promo_image);
            if(request()->hasFile('promo_image') && $promotion->promo_image && $old_promo_image){
                unlink(public_path('sitebucket/promotion/') . "/" . $promotion->promo_image);
                $input['promo_image'] = null;
            }

            /*Upload Image*/
            if (request()->hasFile('promo_image')) {
                $file = $request->file('promo_image');
                $name = date("YmdHis") . $file->getClientOriginalName();
                request()->file('promo_image')->move(public_path() . '/sitebucket/promotion/', $name);
                $input['promo_image'] = $name;
            }

            $promotion->fill($input)->save();

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
        $edit = Promotions::where('id',$id)->firstOrFail();

        return view('promotions.add',compact('edit'));
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
        $data = Promotions::find($id);
        if(!is_null($data)){
            $data->delete();
            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){

        $data = Promotions::where("1");

        /*if($request->filter_role != ""){
            $data->where('role_id',$request->filter_role);
        }

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('first_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('last_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('email','LIKE',"%".$request->filter_search."%");
            });
        }*/

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $btn = '<a href="' . route('promotions.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                <i class="fa fa-pencil"></i>
                            </a>';
                    $btn .= ' <a href="javascript:void(0)" data-url="' . route('promotions.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                <i class="fa fa-trash"></i>
                              </a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
    }
}
