<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClaimPoint;
use App\Models\TireManifistation;
use App\Models\Warranty;
use App\Models\WarrantyVehicle;
use App\Models\WarrantyPicture;
use App\Models\WarrantyClaimPoint;
use App\Models\WarrantyTireManifistation;
use Auth;
use Validator;
use DataTables;

class WarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $warranty_claim_types = Warranty::$warranty_claim_types;
        sort($warranty_claim_types);

        $claim_points = ClaimPoint::with('sub_titles')->whereNull('parent_id')->get();

        $tire_manifistations = TireManifistation::all();

        return view('warranty.add', compact('warranty_claim_types','claim_points','tire_manifistations'));
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

        // dd($input);

        $input['user_id'] = Auth::id();

        $rules = array(
                        'user_id' => 'required|exists:users,id',
                        'warranty_claim_type' => 'required',
                        'dealer_name' => 'required',
                        'customer_address' => 'required',
                        'customer_email' => 'required',
                        'customer_phone' => 'required',
                        'customer_location' => 'required',
                        'customer_telephone' => 'required',
                        'dealer_location' => 'required',
                        'dealer_telephone' => 'required',
                        'vehicle_maker' => 'required',
                        'year' => 'required|integer',
                        'vehicle_model' => 'required',
                        'license_plate' => 'required',
                        'vehicle_mileage' => 'required',
                        'reason_for_tire_return' => 'required',

                        'lt_tire_position' => 'nullable|array',
                        'tb_tire_position' => 'nullable|array',
                        'location_of_damage' => 'nullable|array',

                        'default_pictures.title.*' => 'required|max:185',
                        'default_pictures.title.*' => 'required',

                        'other_pictures.title.*' => 'required|max:185',
                        'other_pictures.title.*' => 'required',

                        'claim_point' => 'nullable|array',
                        // 'claim_point.*' => 'exists:claim_points,id',

                        'tire_manifistation' => 'nullable|array',
                        // 'tire_manifistation.*' => 'exists:tire_manifistations,id',
                    );


        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){
                $warranty = Warranty::findOrFail($input['id']);
                $message = "Warranty details updated successfully.";
            }else{
                $warranty = new Warranty();
                $message = "Warranty details saved successfully.";
            }

            if($warranty->fill($input)->save()){

                $input['lt_tire_position'] = implode(",", $input['lt_tire_position']);
                $input['tb_tire_position'] = implode(",", $input['tb_tire_position']);
                $input['location_of_damage'] = implode(",", $input['location_of_damage']);
                $input['warranty_id'] = $warranty->id;

                $warranty_vehicle_obj = WarrantyVehicle::firstOrNew(['warranty_id' => $warranty->id]);
                $warranty_vehicle_obj->fill($input)->save();

                // Start Claim Point
                if(isset($input['claim_point'])){
                    $claim_points = ClaimPoint::whereNotNull('parent_id')->get();

                    foreach($claim_points as $key=>$value){

                        $is_yes = 0;
                        if(isset($input['claim_point'][$value->id])){
                            $is_yes = $input['claim_point'][$value->id];
                        }

                        WarrantyClaimPoint::updateOrCreate(
                                                [
                                                    'warranty_id' => $warranty->id,
                                                    'claim_point_id' => $value->id,
                                                ],
                                                [
                                                    'warranty_id' => $warranty->id,
                                                    'claim_point_id' => $value->id,
                                                    'is_yes' => $is_yes,
                                                ]
                                            );
                    }
                }else{
                    $claim_points = ClaimPoint::whereNotNull('parent_id')->get();

                    foreach($claim_points as $key=>$value){

                        WarrantyClaimPoint::updateOrCreate(
                                                [
                                                    'warranty_id' => $warranty->id,
                                                    'claim_point_id' => $value->id,
                                                ],
                                                [
                                                    'warranty_id' => $warranty->id,
                                                    'claim_point_id' => $value->id,
                                                    'is_yes' => 0,
                                                ]
                                            );
                    }
                }
                // End Claim Point

                // Start Tire Manifisation
                if(isset($input['tire_manifistation'])){
                    $tire_manifistations = TireManifistation::all();

                    foreach($tire_manifistations as $key=>$value){

                        $is_yes = 0;
                        if(isset($input['tire_manifistation'][$value->id])){
                            $is_yes = $input['tire_manifistation'][$value->id];
                        }

                        WarrantyTireManifistation::updateOrCreate(
                                                [
                                                    'warranty_id' => $warranty->id,
                                                    'tire_manifistation_id' => $value->id,
                                                ],
                                                [
                                                    'warranty_id' => $warranty->id,
                                                    'tire_manifistation_id' => $value->id,
                                                    'is_yes' => $is_yes,
                                                ]
                                            );
                    }
                }else{
                    $tire_manifistations = TireManifistation::all();

                    foreach($tire_manifistations as $key=>$value){

                        WarrantyTireManifistation::updateOrCreate(
                                                [
                                                    'warranty_id' => $warranty->id,
                                                    'tire_manifistation_id' => $value->id,
                                                ],
                                                [
                                                    'warranty_id' => $warranty->id,
                                                    'tire_manifistation_id' => $value->id,
                                                    'is_yes' => 0,
                                                ]
                                            );
                    }
                }
                // End Manifisation


                // Start Warranty Pictures
                $warranty_pictures_ids = array();
                if(isset($input['default_pictures']['image'])){
                    foreach ($input['default_pictures']['image'] as $key => $value) {
                        $insert['warranty_id'] = $warranty->id;

                        if(isset($value) && is_object($value)){
                            $file = $request->file('default_pictures')['image'][$key];

                            if(!in_array($file->extension(),['jpeg','jpg','png','eps','bmp','tif','tiff','webp'])){
                              continue;
                            }

                            if($file->getSize() <= 10 * 1024 * 1024){ //10MB
                              $name = date("YmdHis")."_".$key."_".$file->getClientOriginalName() ;
                              $file->move(public_path() . '/sitebucket/warranty-pictures/', $name);
                              $insert['image'] = $name;
                            }
                        }

                        if($insert['image'] && @$input['default_pictures']['title'][$key] != ""){
                            $insert['title'] = $input['default_pictures']['title'][$key];
                            $insert['type'] = 'default';

                            // if(isset($value['id'])){
                            //     $warranty_picture_obj = WarrantyPicture::find($value['id']);
                            // }else{
                                $warranty_picture_obj = New WarrantyPicture();
                            //}

                            $warranty_picture_obj->fill($insert)->save();

                            if(@$warranty_picture_obj->id){
                              array_push($warranty_pictures_ids, $warranty_picture_obj->id);
                            }
                        }
                    }
                }

                if(isset($input['other_pictures'])){
                    foreach ($input['other_pictures'] as $key => $value) {
                        $insert['warranty_id'] = $warranty->id;

                        if(isset($value['image']) && is_object($value['image'])){
                            $file = $request->file('other_pictures')[$key]['image'];

                            if(!in_array($file->extension(),['jpeg','jpg','png','eps','bmp','tif','tiff','webp'])){
                                continue;
                            }

                            if($file->getSize() <= 10 * 1024 * 1024){ //10MB
                                $name = date("YmdHis")."_".$key."_".$file->getClientOriginalName() ;
                                $file->move(public_path() . '/sitebucket/warranty-pictures/', $name);
                                $insert['image'] = $name;
                            }
                        }

                        if($insert['image'] && @$value['title'] != ""){
                            $insert['title'] = $value['title'];
                            $insert['type'] = 'other';

                            // if(isset($value['id'])){
                            //     $warranty_picture_obj = WarrantyPicture::find($value['id']);
                            // }else{
                                $warranty_picture_obj = New WarrantyPicture();
                            // }

                            $warranty_picture_obj->fill($insert)->save();

                            if(@$warranty_picture_obj->id){
                              array_push($warranty_pictures_ids, $warranty_picture_obj->id);
                            }
                        }
                    }
                }

                if(!isset($input['default_pictures']) && !isset($input['other_pictures'])){
                    $removeWarrantyPicture = WarrantyPicture::where('warranty_id',$warranty->id);
                    $removeWarrantyPicture->delete();
                }elseif(!empty($warranty_pictures_ids)){
                    $removeWarrantyPicture = WarrantyPicture::where('warranty_id',$warranty->id);
                    $removeWarrantyPicture->whereNotIn('id',$warranty_pictures_ids);
                    $removeWarrantyPicture->delete();
                }
                // End Warranty Pictures

                $response = ['status' => true,'message' => $message];
            }else{
                $response = ['status' => false,'message' => "Something went wrong."];
            }
        }

        dd($response);
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
}
