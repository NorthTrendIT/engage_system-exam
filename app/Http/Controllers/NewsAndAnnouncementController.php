<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationConnection;
use App\Models\NotificationDocument;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Classes;
use App\Models\User;
use App\Models\Territory;
use App\Models\SapConnection;
use App\Models\ProductGroup;
use App\Models\CustomerProductGroup;
use OneSignal;
use DataTables;
use Validator;
use Auth;

class NewsAndAnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role_id !== 1){
            return redirect()->route('news-and-announcement.feed');
        }
        $sap_connection = SapConnection::all();
        return view('news-and-announcement.index',compact('sap_connection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sap_connections = SapConnection::all();
        return view('news-and-announcement.add', compact('sap_connections'));
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
        $rules = array(
                    'title' => 'required|unique:notifications',
                    'type' => 'required',
                    'message' => 'required',
                    'module' => 'required',
                    'is_important' => 'required',
                    'sap_connection_id' => 'required',
                );

        if(isset($input['id'])){
            $rules['title'] = 'required|max:185|unique:notifications,title,'.$input['id'].',id';
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){
                $notification = Notification::find($input['id']);
                $message = "Notification details updated successfully.";
            } else{
                $notification = new Notification();
                $message = "New Notification created successfully.";
            }
            $notification->sap_connection_id = $input['sap_connection_id'];
            $notification->type = $input['type'];
            $notification->title = $input['title'];
            $notification->module = $input['module'];
            $notification->message = $input['message'];
            $notification->is_important = $input['is_important'];
            $notification->user_id = Auth::user()->id;
            $notification->start_date = date('Y-m-d',strtotime($input['start_date']));
            $notification->end_date = date('Y-m-d',strtotime($input['end_date']));
            $notification->customer_selection = @$input['select_class_customer'] ?? 'all';

            if(in_array($input['module'], ['brand', 'customer_class', 'territory', 'market_sector'])){
                $notification->request_payload = json_encode($input['record_id']);
            }

            $notification->save();

            if(isset($input['id'])){
                NotificationConnection::where('notification_id', $notification->id)->delete();
            }

            // Start Notification Document
            $docs_ids = array();
            if(isset($input['documents'])){
                foreach ($input['documents'] as $key => $value) {
                    $value['notification_id'] = $notification->id;

                    if(isset($value['file']) && is_object($value['file'])){
                        $file = $value['file'];
                        // dd($file->extension());
                        if(!in_array($file->extension(),['jpeg','jpg','png','eps','bmp','tif','tiff','webp', 'pdf','doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'mp4', 'wmv'])){
                        continue;
                        }

                        // if($file->getSize() <= 10 * 1024 * 1024){ //10MB
                            $name = date("YmdHis") . $file->getClientOriginalName() ;
                            $file->move(public_path() . '/sitebucket/news-and-announcement/', $name);
                            $value['file'] = $name;
                        // }
                    }

                    if($value['file']){
                        if(isset($value['id'])){
                            $notification_doc = NotificationDocument::find($value['id']);
                        }else{
                            $notification_doc = New NotificationDocument();
                        }

                        $notification_doc->fill($value)->save();

                        if(@$notification_doc->id){
                            array_push($docs_ids, $notification_doc->id);
                        }
                    }
                }
            }

            if(!isset($input['documents'])){
                $removeDoc = NotificationDocument::where('notification_id',$notification->id);
                $removeDoc->delete();
            }elseif(!empty($docs_ids)){
                $removeDoc = NotificationDocument::where('notification_id',$notification->id);
                $removeDoc->whereNotIn('id',$docs_ids);
                $removeDoc->delete();
            }
            // End Notification Document


            if($input['module'] == 'all'){
                $data = Customer::where('is_active', '1')->where('sap_connection_id', $input['sap_connection_id'])->get();

                $connection = [];
                $counter = 0;
                foreach($data as $customer){
                    // $user = User::where('customer_id', $customer->id)->firstOrFail();
                    $user = @$customer->user;
                    if($user){
                        $connection[$counter]['notification_id'] = $notification->id;
                        $connection[$counter]['user_id'] = $user->id;
                        $connection[$counter]['record_id'] = $customer->id;
                        $counter++;
                    }
                }

                NotificationConnection::insert($connection);
            }

            if(isset($input['record_id']) && count($input['record_id']) > 0 ){
                $records = $input['record_id'];
                NotificationConnection::where('notification_id', $notification->id)->delete();

                if($input['module'] == 'brand'){
                    foreach($records as $record_id){
                        $data = CustomerProductGroup::where('product_group_id', $record_id)->get();
                        $connection = [];
                        $counter = 0;
                        foreach($data as $item){
                            $user = @$item->customer->user;
                            if($user){
                                $connection[$counter]['notification_id'] = $notification->id;
                                $connection[$counter]['user_id'] = $user->id;
                                $connection[$counter]['record_id'] = @$item->customer->id;
                                $counter++;
                            }
                        }
                        NotificationConnection::insert($connection);
                    }
                }

                if($input['module'] == 'customer'){
                    $connection = [];
                    $counter = 0;
                    foreach($records as $customer_id){
                        $customer = Customer::where('is_active', '1')->where('id', $customer_id)->firstOrFail();
                        $user = @$customer->user;

                        $connection[$counter]['notification_id'] = $notification->id;
                        $connection[$counter]['user_id'] = $user->id;
                        $counter++;
                    }
                    NotificationConnection::insert($connection);
                }

                if($input['module'] == 'customer_class'){
                    foreach($records as $record_id){
                        $data = Customer::where(['class_id' => $record_id, 'sap_connection_id' => $input['sap_connection_id'], 'is_active' => '1' ]);

                        if(@$request->select_class_customer == "specific" && !empty(@$request->class_customer)){
                            $data->whereIn('id', @$request->class_customer);
                        }

                        $data = $data->get();
                        $connection = [];
                        $counter = 0;
                        foreach($data as $customer){
                            $user = @$customer->user;
                            $connection[$counter]['notification_id'] = $notification->id;
                            $connection[$counter]['user_id'] = $user->id;
                            $connection[$counter]['record_id'] = $customer->id;
                            $counter++;
                        }
                        NotificationConnection::insert($connection);
                    }
                }

                if($input['module'] == 'sales_specialist'){
                    foreach($records as $record_id){
                        $data = Customer::where('is_active', '1')->whereHas('sales_specialist', function($q) use($record_id){
                            $q->where('ss_id', $record_id);
                        })->get();

                        $connection = [];
                        $counter = 0;
                        foreach($data as $customer){
                            $user = @$customer->user;
                            $connection[$counter]['notification_id'] = $notification->id;
                            $connection[$counter]['user_id'] = $user->id;
                            $connection[$counter]['record_id'] = $customer->id;
                            $counter++;
                        }
                        NotificationConnection::insert($connection);
                    }
                }

                if($input['module'] == 'territory'){
                    foreach($records as $record_id){
                        $data = Customer::where('is_active', '1')->where('territory', $record_id)->get();
                        $connection = [];
                        $counter = 0;
                        foreach($data as $customer){
                            $user = @$customer->user;
                            $connection[$counter]['notification_id'] = $notification->id;
                            $connection[$counter]['user_id'] = $user->id;
                            $connection[$counter]['record_id'] = $customer->id;
                            $counter++;
                        }
                        NotificationConnection::insert($connection);
                    }
                }

                if($input['module'] == 'market_sector'){
                    foreach($records as $record_id){
                        $data = Customer::where(['u_sector' => $record_id, 'sap_connection_id' => $input['sap_connection_id'], 'is_active' => '1'])->get();
                        $connection = [];
                        $counter = 0;
                        foreach($data as $customer){
                            $user = @$customer->user;
                            $connection[$counter]['notification_id'] = $notification->id;
                            $connection[$counter]['user_id'] = $user->id;
                            $counter++;
                        }
                        NotificationConnection::insert($connection);
                    }
                }
            }            

            // Send Push Notification
            if(isset($notification->id)){
                $connections = NotificationConnection::where('notification_id', $notification->id)->get();

                $fields['filters'] = [];
                foreach ($connections as $value) {
                    $fields['filters'][] = array("field" => "tag", "key" => "user", "relation"=> "=", "value"=> $value->user_id);
                } 

                $message = $notification->title;

                // $push = OneSignal::sendPush($fields, $message);
                // if(!empty($push['id'])){
                //     $message = "Notification Send.";
                //     return $response = ['status'=>true,'message'=>$message];
                // }
                // if(!empty($push['errors'])){
                //     $message = $push['errors'][0];
                //     return $response = ['status'=>false,'message'=>$message];
                // }
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
        $brands = collect();
        $territories = collect();
        if(Auth::user()->role_id != 1){
            $connection = NotificationConnection::where('user_id', '=', @Auth::user()->id)
                ->where('notification_id', '=', $id)->first();
            $connection->is_seen = 1;
            $connection->save();
        }
        $data = Notification::with(['user', 'documents', 'connections'])->where('id', $id)->firstOrFail();
        if(@$data->module == 'brand' && !empty(@$data->request_payload)){
            $brand_ids = json_decode(@$data->request_payload);
            $brands = ProductGroup::whereIn('id', $brand_ids)->orderby('group_name','asc')->where('is_active', true)->pluck('group_name');
        }
        if(@$data->module == 'territory' && !empty(@$data->request_payload)){
            $territory_ids = json_decode(@$data->request_payload);
            $territories = Territory::whereIn('id', $territory_ids)->where('territory_id','!=','-2')->where('is_active',true)->orderBy('description','asc')->pluck('description');
        }
        // dd($brand)
        return view('news-and-announcement.view', compact('data', 'brands', 'territories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = collect();
        $edit = Notification::with(['user', 'documents'])->where('id', $id)->firstOrFail();
        $sap_connections = SapConnection::all();
        if(@$edit->module == 'brand' && !empty(@$edit->request_payload)){
            $brand_ids = json_decode(@$edit->request_payload);
            $brands = ProductGroup::whereIn('id', $brand_ids)->orderby('group_name','asc')->where('is_active', true)->pluck('group_name');
        }
        if(@$edit->module == 'territory' && !empty(@$edit->request_payload)){
            $territory_ids = json_decode(@$edit->request_payload);
            $territories = Territory::whereIn('id', $territory_ids)->where('territory_id','!=','-2')->where('is_active',true)->orderBy('description','asc')->pluck('description');
        }
        return view('news-and-announcement.add', compact('edit', 'sap_connections'));
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
        $notification = Notification::where('id', $id)->firstOrFail();
        if(!is_null($notification)){
            Notification::where('id', $id)->delete();
            NotificationDocument::where('notification_id',$id)->delete();
            NotificationConnection::where('notification_id',$id)->delete();
            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function updateStatus($id)
    {
        $data = Notification::find($id);
        if(!is_null($data)){
            $data->is_active = !$data->is_active;
            $data->save();
            $response = ['status'=>true,'message'=>'Status update successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getAll(Request $request){
        if(@Auth::user()->role_id == 1){
            $data = Notification::with(['user'])->whereIn('type',['A','N']);
        } else {
            $now = date("Y-m-d");
            $data = Notification::whereHas('connections', function($q){
                $q->where('user_id', '=', @Auth::user()->id);
            })->where('is_active', true);

            $data->where(function($query) use ($now){
                $query->orwhere(function($q) use ($now){
                    $q->where('start_date','<=',$now)->where('end_date','>=',$now);
                });

                $query->orwhere(function($q1){
                    $q1->whereNull('start_date')->whereNull('end_date');
                });
            });
        }

        if($request->filter_type != ""){
            $data->where('type',$request->filter_type);
        }

        if($request->filter_sap_connection != ""){
            $data->where('sap_connection_id', $request->filter_sap_connection);
        }


        if($request->filter_module != ""){
            $data->where('module',$request->filter_module);
        }

        if($request->filter_priority != ""){
            $data->where('is_important',$request->filter_priority);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
            });
        }

        if($request->filter_date_range != ""){
            $date = explode(" - ", $request->filter_date_range);
            $start = date("Y-m-d", strtotime($date[0]));
            $end = date("Y-m-d", strtotime($date[1]));

            $data->whereDate('start_date', '>=' , $start);
            $data->whereDate('end_date', '<=' , $end);
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('bussines_unit', function($row) {
                                return @$row->sap_connection->company_name ?? "-";
                            })
                            ->addColumn('title', function($row) {
                                return $row->title;
                            })
                            ->addColumn('user_name', function($row) {
                                return $row->user->first_name.' '.$row->user->last_name;
                            })
                            ->addColumn('type', function($row) {
                                return getNotificationType($row->type);
                            })
                            ->addColumn('date_period', function($row) {
                                if($row->type != 'OU'){
                                    $startDate = date('M d, Y',strtotime($row->start_date));
                                    $endDate = date('M d, Y',strtotime($row->end_date));
                                    return $startDate.' - '.$endDate;
                                } else {
                                    return '-';
                                }
                            })
                            ->addColumn('module', function($row) {
                                return ucwords(str_replace('_',' ',$row->module));
                            })
                            ->addColumn('is_important', function($row) {
                                if($row->is_important == 0){
                                    /*return '<button type="button" class="btn btn-info btn-sm">Normal</button>';*/
                                    return '<span class="badge badge-primary">Normal</span>';
                                }elseif($row->is_important == 1){
                                    return '<span class="badge badge-danger">Important</span>';
                                }
                                return "-";
                            })
                            ->orderColumn('title', function ($query, $order) {
                                $query->orderBy('title', $order);
                            })
                            ->orderColumn('user_name', function ($query, $order) {
                                $query->orderBy('user_id', $order);
                            })
                            ->orderColumn('type', function ($query, $order) {
                                $query->orderBy('type', $order);
                            })
                            ->orderColumn('date_period', function ($query, $order) {
                                $query->orderBy('start_date', $order);
                            })
                            ->orderColumn('module', function ($query, $order) {
                                $query->orderBy('module', $order);
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('news-and-announcement.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm mr-10" title="View">
                                  <i class="fa fa-eye"></i>
                                </a>';
                                if(@Auth::user()->role_id == 1){
                                    // $btn .= '<a href="' . route('news-and-announcement.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10 ">
                                    // <i class="fa fa-pencil"></i>
                                    // </a>';
                                    if($row->type != 'OU'){
                                        $btn .= '<a href="javascript:"  data-url="' . route('news-and-announcement.destroy',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10 delete" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>';
                                    }
                                }
                                return $btn;
                            })
                            ->addColumn('status', function($row) {
                                $btn = '';
                                if($row->is_active){
                                    $btn .= '<div class="form-group" title="Status: Active">
                                    <div class="col-3">
                                     <span class="switch">
                                      <label>
                                       <input type="checkbox" checked="checked" name="status" class="status" data-url="' . route('news-and-announcement.status',$row->id) . '"/>
                                       <span></span>
                                      </label>
                                     </span>
                                    </div>';
                                }else{
                                    $btn .= '<div class="form-group" title="Status: Inactive">
                                    <div class="col-3">
                                     <span class="switch">
                                      <label>
                                       <input type="checkbox" name="status" class="status" data-url="' . route('news-and-announcement.status',$row->id) . '"/>
                                       <span></span>
                                      </label>
                                     </span>
                                    </div>';
                                }
                                return $btn;
                            })
                            ->rawColumns(['action', 'is_important', 'status'])
                            ->make(true);
    }

    public function getRoles(Request $request){
        $search = $request->search;

        if($search == ''){
            $data = Role::orderby('name','asc')->select('id','name')->limit(50)->get();
        }else{
            $data = Role::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->limit(50)->get();
        }

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->name
            );
        }

        return response()->json($response);
    }

    public function getCustomer(Request $request){
        $search = $request->search;
        $sap_connection_id = $request->sap_connection_id;

        // if($sap_connection_id == 5){
        //     $sap_connection_id = 1;
        // }

        if($search == ''){
            $data = Customer::orderby('card_name','asc')->where('sap_connection_id', $sap_connection_id)->select('id','card_code', 'card_name')->limit(50)->get();
        }else{
            $data = Customer::orderby('card_name','asc')->where('sap_connection_id', $sap_connection_id)->select('id','card_code','card_name')->where('card_name', 'like', '%' .$search . '%')->limit(50)->get();
        }

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=> '('.$value->card_code.') '.$value->card_name
            );
        }

        return response()->json($response);
    }

    public function getClassCustomer(Request $request){
        $search = $request->search;
        $sap_connection_id = $request->sap_connection_id;

        $response = array();
        if($sap_connection_id){
            // if($sap_connection_id == 5){
            //     $sap_connection_id = 1;
            // }


            $data = Customer::orderby('card_name','asc')->where('sap_connection_id', $sap_connection_id)->select('id', 'card_code', 'card_name')->limit(50)->whereIn('class_id', $request->class_id);
            if($search != ''){
                $data->where('card_name', 'like', '%' .$search . '%');
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id"=>$value->id,
                    "text"=> '('.$value->card_code.') '.$value->card_name
                );
            }
        }

        return response()->json($response);
    }

    public function getCustomerClass(Request $request){
        $search = $request->search;
        $sap_connection_id = $request->sap_connection_id;

        // if($sap_connection_id == 5){
        //     $sap_connection_id = 1;
        // }

        $data = Classes::where('module', 'C')->limit(50);

        if($search != ''){
            $data->whereHas('name_sap_value', function($q) use ($search) {
                $q->where('value','LIKE',"%".$search."%");
            });
        }

        if(@$request->sap_connection_id != ''){
            $data->where('sap_connection_id', @$request->sap_connection_id);
        }

        $data = $data->get();

        $response = array();
        foreach($data as $value){

            $text = @$value->name_sap_value->value ?? $value->name;

            $response[$text] = array(
                "id"=>$value->id,
                "text"=> $text
            );
        }

        sort($response);

        return response()->json($response);
    }

    public function getSalesSpecialist(Request $request){
        $search = $request->search;
        // $sap_connection_id = $request->sap_connection_id;

        // if($sap_connection_id == 5){
        //     $sap_connection_id = 1;
        // }

        if($search == ''){
            $data = User::orderby('sales_specialist_name','asc')->select('id','sales_specialist_name')->where(['role_id' => 14, 'is_active' => true])->limit(50)->get();
        }else{
            $data = User::orderby('sales_specialist_name','asc')->select('id','sales_specialist_name')->where(['role_id' => 14, 'is_active' => true])->where('sales_specialist_name', 'like', '%' .$search. '%')->limit(50)->get();
        }

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->sales_specialist_name,
            );
        }

        return response()->json($response);
    }

    public function getTerritory(Request $request){
        $search = $request->search;

        $data = Territory::where('territory_id','!=','-2')->where('is_active',true)->orderBy('description','asc');

        if($search != ''){
            $data->where('description', 'like', '%' .$search . '%');
        }

        $data = $data->limit(50)->get();

        $response = array();
        foreach($data as $value){
            $response[] = array(
                "id"=>$value->id,
                "text"=>$value->description,
            );
        }

        return response()->json($response);
    }

    public function getBrands(Request $request){

        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            if($request->sap_connection_id == 5){
                $request->sap_connection_id = 1;
            }

            $data = ProductGroup::where('sap_connection_id',$request->sap_connection_id)
                                ->orderby('group_name','asc')
                                ->select('id','group_name')
                                ->where('is_active', true)
                                ->limit(50);

            if($search != ''){
                $data->where('group_name', 'like', '%' .$search . '%');
            }

            $data->whereNotIn('group_name', ['Items', 'MKTG. MATERIALS', 'OFFICIAL DOCUMENT']);
            
            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->id,
                    "text" => $value->group_name
                );
            }
        }

        return response()->json($response);
    }

    public function getMarketSector(Request $request){
        // dd($request->all());
        $response = array();
        if($request->sap_connection_id){
            $search = $request->search;

            // if($request->sap_connection_id == 5){
            //     $request->sap_connection_id = 1;
            // }

            $data = Customer::where('sap_connection_id',$request->sap_connection_id)
                                ->orderby('u_sector','asc')
                                ->select('u_sector')
                                ->limit(50)->groupBy('u_sector');

            if($search != ''){
                $data->where('group_name', 'like', '%' .$search . '%');
            }

            $data = $data->get();

            foreach($data as $value){
                $response[] = array(
                    "id" => $value->u_sector,
                    "text" => @$value->u_sector_sap_value->value ?? $value->u_sector
                );
            }
        }

        return response()->json($response);
    }

    public function getAllRole(Request $request){

        $data = NotificationConnection::with(['user.role'])->where('notification_id', $request->notification_id);

        if($request->filter_type!= ""){
            $data->where('type',$request->filter_type);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('user_name', function($row) {
                                if($row->user->role_id == 2){
                                    return $row->user->sales_specialist_name;
                                }
                                if($row->user->role_id == 4){
                                    return $row->user->first_name.' '.$row->user->last_name;
                                }
                                return '-';
                            })
                            ->addColumn('role', function($row) {
                                return $row->user->role->name;
                            })
                            ->addColumn('is_seen', function($row) {
                                if($row->is_seen){
                                    return '<span class="label label-lg label-light-success label-inline">Yes</span>';
                                } else {
                                    return '<span class="label label-lg label-light-danger label-inline">No</span>';
                                }
                            })
                            ->rawColumns(['is_seen'])
                            ->make(true);
    }

    public function getAllCustomer(Request $request){

        $data = NotificationConnection::with(['user.customer'])->where('notification_id', $request->notification_id);

        if($request->filter_type!= ""){
            $data->where('type',$request->filter_type);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        // dd($data->get());

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('user_name', function($row) {
                                if($row->user->role_id == 2){
                                    return $row->user->sales_specialist_name;
                                }
                                if($row->user->role_id == 4){
                                    return $row->user->first_name.' '.$row->user->last_name;
                                }
                                return '-';
                            })
                            ->addColumn('role', function($row) {
                                return 'Customer';
                            })
                            ->addColumn('is_seen', function($row) {
                                if($row->is_seen){
                                    return '<span class="label label-lg label-light-success label-inline">Yes</span>';
                                } else {
                                    return '<span class="label label-lg label-light-danger label-inline">No</span>';
                                }
                            })
                            ->rawColumns(['is_seen'])
                            ->make(true);
    }

    public function getAllSalesSpecialist(Request $request){

        $data = NotificationConnection::with(['user.customer'])->where('notification_id', $request->notification_id);

        if($request->filter_type!= ""){
            $data->where('type',$request->filter_type);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('user_name', function($row) {
                                if($row->user->role_id == 2){
                                    return $row->user->customer->card_code;
                                }
                                if($row->user->role_id == 4){
                                    return $row->user->first_name.' '.$row->user->last_name;
                                }
                                return '-';
                            })
                            ->addColumn('role', function($row) {
                                return $row->user->role->name;
                            })
                            ->addColumn('is_seen', function($row) {
                                if($row->is_seen){
                                    return '<span class="label label-lg label-light-success label-inline">Yes</span>';
                                } else {
                                    return '<span class="label label-lg label-light-danger label-inline">No</span>';
                                }
                            })
                            ->rawColumns(['is_seen'])
                            ->make(true);
    }

    public function getAllCustomerClass(Request $request){

        $data = NotificationConnection::with(['user.customer.classes'])->where('notification_id', $request->notification_id);
        // dd($data->get());

        if($request->filter_type!= ""){
            $data->where('type',$request->filter_type);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('user_name', function($row) {
                                if($row->user->role_id == 2){
                                    return $row->user->sales_specialist_name;
                                }
                                if($row->user->role_id == 4){
                                    return $row->user->first_name.' '.$row->user->last_name;
                                }
                                return '-';
                            })
                            ->addColumn('class_name', function($row) {
                                return @$row->user->customer->u_classification_sap_value->value ?? @$row->user->customer->classes->name;
                            })
                            ->addColumn('is_seen', function($row) {
                                if($row->is_seen){
                                    return '<span class="label label-lg label-light-success label-inline">Yes</span>';
                                } else {
                                    return '<span class="label label-lg label-light-danger label-inline">No</span>';
                                }
                            })
                            ->rawColumns(['is_seen'])
                            ->make(true);
    }

    public function getAllTerritory(Request $request){

        $data = NotificationConnection::with(['user.customer.territory'])->where('notification_id', $request->notification_id);
        // dd($data->get());

        if($request->filter_type!= ""){
            $data->where('type',$request->filter_type);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('user_name', function($row) {
                                if($row->user->role_id == 2){
                                    return $row->user->sales_specialist_name;
                                }
                                if($row->user->role_id == 4){
                                    return $row->user->first_name.' '.$row->user->last_name;
                                }
                                return '-';
                            })
                            ->addColumn('territory', function($row) {
                                return @$row->user->customer->territory->description;
                                return '-';
                            })
                            ->addColumn('is_seen', function($row) {
                                if($row->is_seen){
                                    return '<span class="label label-lg label-light-success label-inline">Yes</span>';
                                } else {
                                    return '<span class="label label-lg label-light-danger label-inline">No</span>';
                                }
                            })
                            ->rawColumns(['is_seen'])
                            ->make(true);
    }

    public function getAllMarketSector(Request $request){

        $data = NotificationConnection::with(['user.customer'])->where('notification_id', $request->notification_id);
        // dd($data->get());

        if($request->filter_type!= ""){
            $data->where('type',$request->filter_type);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('user_name', function($row) {
                                if($row->user->role_id == 2){
                                    return $row->user->sales_specialist_name;
                                }
                                if($row->user->role_id == 4){
                                    return $row->user->first_name.' '.$row->user->last_name;
                                }
                                return '-';
                            })
                            ->addColumn('market_sector', function($row) {
                                return @$row->user->customer->u_sector_sap_value->value ?? @$row->user->customer->u_sector;
                            })
                            ->addColumn('is_seen', function($row) {
                                if($row->is_seen){
                                    return '<span class="label label-lg label-light-success label-inline">Yes</span>';
                                } else {
                                    return '<span class="label label-lg label-light-danger label-inline">No</span>';
                                }
                            })
                            ->rawColumns(['is_seen'])
                            ->make(true);
    }

    public function getAllBrands(Request $request){

        $data = NotificationConnection::with(['user.customer.product_groups.product_group'])->where('notification_id', $request->notification_id);
        // dd($data->get());

        if($request->filter_type!= ""){
            $data->where('type',$request->filter_type);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('title','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('user_name', function($row) {
                                if($row->user->role_id == 2){
                                    return $row->user->sales_specialist_name;
                                }
                                if($row->user->role_id == 4){
                                    return $row->user->first_name.' '.$row->user->last_name;
                                }
                                return '-';
                            })
                            ->addColumn('brand', function($row) {
                                return @$row->user->customer->product_groups;
                                // return '-';
                            })
                            ->addColumn('is_seen', function($row) {
                                if($row->is_seen){
                                    return '<span class="label label-lg label-light-success label-inline">Yes</span>';
                                } else {
                                    return '<span class="label label-lg label-light-danger label-inline">No</span>';
                                }
                            })
                            ->rawColumns(['is_seen'])
                            ->make(true);
    }

    public function fetchNotifications(){
        $notifications = Notification::whereHas('connections', function($q){
            $q->where('user_id', Auth::user()->id);
        })->orderBy('created_at', 'desc')->get()->take(2);
        
        return view('news-and-announcement.customer-view', compact('notifications'));
    }


    public function fetchFeedPerId(Request $request){
        $notif_id = ($request->notif_id) ? $request->notif_id : 0;

        $notifications = Notification::with(['user', 'documents', 'connections'])
                            ->whereHas('connections', function($q){
                                $q->where('user_id', Auth::user()->id);
                            })
                            ->where('id', '<' , $notif_id)->orderBy('id', 'desc')->get()->take(2);

        return view('news-and-announcement.feed', compact('notifications'));
    }




}
