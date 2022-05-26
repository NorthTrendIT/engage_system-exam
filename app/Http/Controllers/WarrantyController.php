<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Department;
use App\Models\ClaimPoint;
use App\Models\TireManifistation;
use App\Models\SapConnection;
use App\Models\Warranty;
use App\Models\WarrantyVehicle;
use App\Models\WarrantyPicture;
use App\Models\WarrantyClaimPoint;
use App\Models\WarrantyTireManifistation;
use App\Models\WarrantyDiagnosticReport;
use App\Models\Notification;
use App\Models\NotificationConnection;

use Mail;
use Auth;
use Validator;
use DataTables;
use OneSignal;
use PDF;

class WarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = collect();
        if(in_array(userrole(),[1,3])){
            $company = SapConnection::all();
        }

        $warranty_claim_types = Warranty::$warranty_claim_types;
        sort($warranty_claim_types);

        return view('warranty.index', compact('company','warranty_claim_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(in_array(userrole(),[1,3])){
            abort(404);
        }
        
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

        if(!isset($input['id']) && !in_array(userrole(),[1,3])){
            $input['user_id'] = Auth::id();
            // $input['sap_connection_id'] = Auth::user()->sap_connection_id;
        }

        $rules = array(
                        'user_id' => 'required|exists:users,id',
                        // 'sap_connection_id' => 'required|exists:sap_connections,id',
                        'warranty_claim_type' => 'required',
                        // 'dealer_name' => 'required',
                        'customer_address' => 'required',
                        'customer_name' => 'required',
                        'customer_email' => 'required',
                        'customer_phone' => 'required',
                        'customer_location' => 'required',
                        'customer_telephone' => 'required',
                        // 'dealer_location' => 'required',
                        // 'dealer_telephone' => 'required',
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

        // if(isset($input['id'])){
        //     unset($input['sap_connection_id']);
        //     unset($rules['sap_connection_id']);
        // }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){
                $warranty = Warranty::findOrFail($input['id']);
                $message = "Warranty details updated successfully.";
                $input['updated_by'] = Auth::id();

                add_log(47, $input);
            }else{
                $warranty = new Warranty();
                $message = "Warranty details saved successfully.";
                $input['created_by'] = Auth::id();

                add_log(46, $input);
            }

            if($warranty->fill($input)->save()){
                $warranty->ref_no = "#EOMSW".$warranty->id;
                $warranty->save();


                if(@$input['lt_tire_position']){
                    $input['lt_tire_position'] = implode(", ", $input['lt_tire_position']);
                }else{
                    $input['lt_tire_position'] = NULL;
                }
                if(@$input['tb_tire_position']){
                    $input['tb_tire_position'] = implode(", ", $input['tb_tire_position']);
                }else{
                    $input['tb_tire_position'] = NULL;
                }
                if(@$input['location_of_damage']){
                    $input['location_of_damage'] = implode(", ", $input['location_of_damage']);
                }else{
                    $input['location_of_damage'] = NULL;
                }

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
                        }else{
                            $insert['image'] = $value;
                        }

                        if($insert['image'] && @$input['default_pictures']['title'][$key] != ""){
                            $insert['title'] = $input['default_pictures']['title'][$key];
                            $insert['type'] = 'default';

                            if(@$input['default_pictures']['id'][$key] != ""){
                                $warranty_picture_obj = WarrantyPicture::find(@$input['default_pictures']['id'][$key]);
                            }else{
                                $warranty_picture_obj = New WarrantyPicture();
                            }

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
                        }else{
                            $insert['image'] = $value['image'];
                        }

                        if($insert['image'] && @$value['title'] != ""){
                            $insert['title'] = $value['title'];
                            $insert['type'] = 'other';

                            if(isset($value['id'])){
                                $warranty_picture_obj = WarrantyPicture::find($value['id']);
                            }else{
                                $warranty_picture_obj = New WarrantyPicture();
                            }

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
        $data = Warranty::findOrFail($id);

        if(!in_array(userid(),[$data->user_id, $data->assigned_user_id, 1])){ // Not a customer
            return abort(404);
        }

        $claim_points = ClaimPoint::with('sub_titles')->whereNull('parent_id')->get();

        $tire_manifistations = TireManifistation::all();

        $warranty_claim_points = array_combine(array_column($data->claim_points->toArray(),'claim_point_id'), array_column($data->claim_points->toArray(),'is_yes'));

        $warranty_tire_manifistations = array_combine(array_column($data->tire_manifistations->toArray(),'tire_manifistation_id'), array_column($data->tire_manifistations->toArray(),'is_yes'));


        add_log(49, null);

        return view('warranty.view', compact('claim_points','tire_manifistations','warranty_claim_points','warranty_tire_manifistations','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = Warranty::findOrFail($id);
        if(!in_array(userid(),[$edit->user_id, $edit->assigned_user_id, 1])){ // Not a customer
            return abort(404);
        }
        
        $warranty_claim_types = Warranty::$warranty_claim_types;
        sort($warranty_claim_types);

        $claim_points = ClaimPoint::with('sub_titles')->whereNull('parent_id')->get();

        $tire_manifistations = TireManifistation::all();



        $warranty_claim_points = array_combine(array_column($edit->claim_points->toArray(),'claim_point_id'), array_column($edit->claim_points->toArray(),'is_yes'));

        $warranty_tire_manifistations = array_combine(array_column($edit->tire_manifistations->toArray(),'tire_manifistation_id'), array_column($edit->tire_manifistations->toArray(),'is_yes'));

        $location_of_damage = explode(", ", $edit->vehicle->location_of_damage);
        $tb_tire_position = explode(", ", $edit->vehicle->tb_tire_position);
        $lt_tire_position = explode(", ", $edit->vehicle->lt_tire_position);


        return view('warranty.add', compact('warranty_claim_types','claim_points','tire_manifistations','warranty_claim_points','warranty_tire_manifistations', 'location_of_damage', 'tb_tire_position', 'lt_tire_position','edit'));
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
    public function destroy($id){
        $data = Warranty::where('id', $id)->first();
        if(!is_null($data) && in_array(userrole(),[1,3])){

            $data->updated_by = Auth::id();
            $data->save();

            add_log(48, $data->toArray());

            $data->delete();

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }


    public function getAll(Request $request){
        $data = Warranty::query();

        if(!in_array(userrole(),[1,3])){

            $data->where(function($q){
                $q->orwhere('user_id', userid());
                $q->orwhere('assigned_user_id', userid());
            });

        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('customer_phone','LIKE',"%".$request->filter_search."%");
                $q->orwhere('customer_email','LIKE',"%".$request->filter_search."%");
                $q->orwhere('customer_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('warranty_claim_type','LIKE',"%".$request->filter_search."%");
            });
        }

        if($request->filter_customer != ""){
            $data->where('user_id',$request->filter_customer);
        }

        if($request->filter_claim_type != ""){
            $data->where('warranty_claim_type',$request->filter_claim_type);
        }

        if($request->filter_company != ""){
            $data->whereHas('user.sap_connection',function($q) use ($request){
                $q->where('sap_connection_id',$request->filter_company);
            });
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('created_at', '>=' , $start);
            $data->whereDate('created_at', '<=' , $end);
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('name', function($row) {
                                return  @$row->user->sales_specialist_name ?? "-";
                            })
                            ->addColumn('customer_name', function($row) {
                                return  @$row->customer_name ?? "-";
                            })
                            ->addColumn('warranty_claim_type', function($row) {
                                return  @$row->warranty_claim_type ?? "-";
                            })
                            ->addColumn('ref_no', function($row) {
                                return @$row->ref_no ?? "-";
                            })
                            ->addColumn('created_at', function($row) {
                                return date('M d, Y',strtotime($row->created_at));
                            })
                            ->addColumn('company', function($row) {
                                return @$row->user->sap_connection->company_name ?? "-";
                            })
                            ->addColumn('action', function($row){

                                $btn = "";
                                if(in_array(userrole(),[1,3]) || @$row->user_id == userid()){
                                    $btn .= '<a href="' . route('warranty.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                            <i class="fa fa-pencil"></i>
                                        </a>';
                                }

                                if(in_array(userrole(),[1,3])){
                                    $btn .= ' <a href="javascript:void(0)" data-url="' . route('warranty.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete mr-10">
                                                <i class="fa fa-trash"></i>
                                              </a>';
                                }

                                $btn .= '<a href="' . route('warranty.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm ">
                                          <i class="fa fa-eye"></i>
                                      </a>';


                                return $btn;
                            })
                            ->orderColumn('name', function ($query, $order) {
                                $query->select('warranties.*')->join('users', 'warranties.user_id', '=', 'users.id')
                                    ->orderBy('users.sales_specialist_name', $order);
                            })
                            ->orderColumn('ref_no', function ($query, $order) {
                                $query->orderBy('ref_no', $order);
                            })
                            ->orderColumn('warranty_claim_type', function ($query, $order) {
                                $query->orderBy('warranty_claim_type', $order);
                            })
                            ->orderColumn('customer_name', function ($query, $order) {
                                $query->orderBy('customer_name', $order);
                            })
                            ->orderColumn('created_at', function ($query, $order) {
                                $query->orderBy('created_at', $order);
                            })
                            ->orderColumn('company', function ($query, $order) {
                                $query->select('warranties.*')
                                        ->leftjoin('users', 'users.id', '=', 'warranties.user_id')
                                        ->leftjoin('sap_connections', 'sap_connections.id', '=', 'users.id')
                                        ->orderBy('sap_connections.company_name', $order);
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }

    public function exportView($id){
        $data = Warranty::findOrFail($id);

        if(!in_array(userid(),[$data->user_id, $data->assigned_user_id, 1])){ // Not a customer
            return abort(404);
        }

        $claim_points = ClaimPoint::with('sub_titles')->whereNull('parent_id')->get();

        $tire_manifistations = TireManifistation::all();

        $warranty_claim_points = array_combine(array_column($data->claim_points->toArray(),'claim_point_id'), array_column($data->claim_points->toArray(),'is_yes'));

        $warranty_tire_manifistations = array_combine(array_column($data->tire_manifistations->toArray(),'tire_manifistation_id'), array_column($data->tire_manifistations->toArray(),'is_yes'));

        $title = @$data->ref_no." Warranty Details ".date('dmY');
        $pdf =  PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
        
        // return View('warranty.pdf', compact('claim_points','tire_manifistations','warranty_claim_points','warranty_tire_manifistations','data'));

        $pdf->loadView('warranty.pdf', compact('claim_points','tire_manifistations','warranty_claim_points','warranty_tire_manifistations','data'));
        return $pdf->stream($title . ".pdf");

    }

    public function getCustomer(Request $request){
        $search = $request->search;

        $data = User::with('sap_connection')->where('role_id', 4)->orderBy('sales_specialist_name','asc');

        if($search != ''){
            $data->where(function($q) use ($search){
                $q->orwhere('sales_specialist_name', 'like', '%' .$search . '%');
            });
        }

        if(@$request->sap_connection_id != ''){
            $data->where('sap_connection_id',@$request->sap_connection_id);
        }

        $data = $data->limit(50)->get();

        return response()->json($data);
    }

    public function storeAssignment(Request $request){   
        $input = $request->all();

        $rules = array(
                        'warranty_id' => 'required|exists:warranties,id',
                        'department_id' => 'required|exists:departments,id',
                        'user_id' => 'required|exists:users,id',
                  );


        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $obj = Warranty::find($input['warranty_id']);

            // Access only for admin
            if(userrole() == 1){

                $obj->assigned_user_id = $input['user_id'];
                $obj->save();
                // add_log(54, $comment->toArray());

                $message = "User assigned successfully.";
                $response = ['status'=>true,'message'=>$message];


                // Start Push Notification to receiver
                    $link = route('warranty.show', $obj->id);

                    // Create Local Notification
                    $notification = new Notification();
                    $notification->type = 'WTY';
                    $notification->title = 'Assigned a new warranty ticket.';
                    $notification->module = 'warranty';
                    $notification->sap_connection_id = null;
                    $notification->message = 'You have been assigned a new warranty ticket <a href="'.$link.'"><b>'.$obj->ticket_number.'</b></a>.';
                    $notification->user_id = userid();
                    $notification->save();

                    if($notification->id){
                        $connection = new NotificationConnection();
                        $connection->notification_id = $notification->id;
                        $connection->user_id = $input['user_id'];
                        $connection->record_id = null;
                        $connection->save();
                    }

                    // Send One Signal Notification.
                    $fields['filters'] = array(array("field" => "tag", "key" => "user", "relation"=> "=", "value"=> $input['user_id']));
                    $message_text = $notification->title;

                    $push = OneSignal::sendPush($fields, $message_text);
                // End Push Notification to receiver

            }else{
                return $response = ['status'=>false,'message'=>'Access Denied !'];
            }
        }

        return $response;
    }

    public function storeDiagnosticReport(Request $request){   
        $input = $request->all();

        $rules = array(
                        'warranty_id' => 'required|exists:warranties,id',
                        'result' => 'required',
                        'tire_manifistations' => 'nullable|array',
                    );


        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $warranty = Warranty::find($input['warranty_id']);

            $data = WarrantyDiagnosticReport::findOrNew($input['warranty_id']);

            // Access only for admin
            if(userrole() == 1 || @$warranty->assigned_user_id == userid()){

                if(@$input['tire_manifistations']){
                    $input['tire_manifistations'] = implode(", ", @$input['tire_manifistations']);
                }

                $data->fill($input)->save();
                // add_log(54, $comment->toArray());

                $message = "Warranty diagnostic report saved successfully.";
                $response = ['status'=>true,'message'=>$message];


                Mail::send('emails.warranty_diagnostic_report', compact('data'), function($message) use($data) {
                    $message->to($data->warranty->customer_email, $data->warranty->customer_name)
                            ->bcc($data->warranty->user->email, $data->warranty->user->sales_specialist_name )
                            ->subject('Warranty Diagnostic Report Update');
                });


                // Start Push Notification to receiver

                    $link = route('warranty.show', $data->warranty_id);

                    // Create Local Notification
                    $notification = new Notification();
                    $notification->type = 'WTY';
                    $notification->title = 'Warranty diagnostic report updated.';
                    $notification->module = 'warranty';
                    $notification->sap_connection_id = null;
                    $notification->message = 'Your warranty ticket <a href="'.$link.'"><b>'.$data->ticket_number.'</b></a> diagnostic report has been updated.';
                    $notification->user_id = userid();
                    $notification->save();

                    if($notification->id){
                        $connection = new NotificationConnection();
                        $connection->notification_id = $notification->id;
                        $connection->user_id = $data->warranty->user->id;
                        $connection->record_id = null;
                        $connection->save();
                    }

                    // Send One Signal Notification.
                    $fields['filters'] = array(array("field" => "tag", "key" => "user", "relation"=> "=", "value"=> $data->warranty->user->id));
                    $message_text = $notification->title;

                    $push = OneSignal::sendPush($fields, $message_text);
                // End Push Notification to receiver

            }else{
                return $response = ['status'=>false,'message'=>'Access Denied !'];
            }
        }

        return $response;
    }




    // Get Department
    public function getDepartment(Request $request){
        $search = $request->search;

        $data = Department::where('id','!=', 3)->select('id','name')->where('is_active',true);

        if($search != ''){
            $data->where('name', 'like', '%' .$search . '%');
        }

        $data = $data->orderby('name','asc')->limit(50)->get();

        return response()->json($data);
    }

    // Get Department User
    public function getDepartmentUser(Request $request){
        $search = $request->search;

        if(@$request->department_id){
            $data = User::with('role')->where('department_id', @$request->department_id)->where('is_active', true)->where('role_id','!=', 4);

            if($search != ''){
                $data->where(function($q) use ($request) {
                    $q->orwhere('sales_specialist_name','LIKE',"%".$search."%");
                    $q->orwhere('email','LIKE',"%".$search."%");
                });
            }

            $data = $data->orderby('sales_specialist_name','asc')->limit(50)->get();
        }else{
            $data = collect([]);
        }

        return response()->json($data);
    }
}
