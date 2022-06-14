<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\HelpDesk;
use App\Models\HelpDeskComments;
use App\Models\HelpDeskFiles;
use App\Models\HelpDeskStatuses;
use App\Models\HelpDeskUrgencies;
use App\Models\HelpDeskDepartment;
use App\Models\Department;
use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationConnection;

use DataTables;
use Validator;
use Auth;
use OneSignal;

class HelpDeskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $urgencies = HelpDeskUrgencies::all();
        $status = HelpDeskStatuses::all();
        return view('help-desk.index',compact('status','urgencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(userrole() == 1 || userdepartment() == 1){
            return abort(404);
        }

        $departments = Department::where('is_active',true)->get();
        $urgencies = HelpDeskUrgencies::all();
        return view('help-desk.add',compact('departments','urgencies'));
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
                        'subject' => 'required|string|max:185',
                        'message' => 'required',
                        //'department_id' => 'required|exists:departments,id',
                        'help_desk_urgency_id' => 'nullable|exists:help_desk_urgencies,id',
                        'type_of_customer_request' => 'required',
                  );


        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            $input['user_id'] = Auth::id();
            $input['help_desk_status_id'] = 1;
            $input['ticket_number'] = '#EOMSH';

            $ticket = new HelpDesk();
            $message = "Help Desk ticket created successfully.";

            $ticket->fill($input)->save();

            $ticket->ticket_number = '#EOMSH'.$ticket->id;
            $ticket->save();

            add_log(50, $ticket->toArray());

            // Start  Images
            /*$help_images_ids = array();
            if(isset($input['help_images'])){
                foreach ($input['help_images'] as $key => $value) {
                    $value['help_desk_id'] = $ticket->id;

                    if(isset($value['image']) && is_object($value['image'])){
                        $file = $value['image'];

                        if(!in_array($file->extension(),['jpeg','jpg','png','eps','bmp','tif','tiff','webp'])){
                          continue;
                        }

                        if($file->getSize() <= 10 * 1024 * 1024){ //10MB
                            $name = date("YmdHis") . $file->getClientOriginalName() ;
                            $file->move(public_path() . '/sitebucket/help-desk/', $name);
                            $value['filename'] = $name;
                        }
                    }

                    if($value['filename']){
                        $file_obj = new HelpDeskFiles();

                        $file_obj->fill($value)->save();
                    }
                }
            }*/

            $insert = array();
            if(isset($input['images'])){
                foreach ($input['images'] as $key => $value) {
                    $insert['help_desk_id'] = $ticket->id;

                    if(isset($value) && is_object($value)){
                        $file = $value;

                        if(!in_array($file->extension(),['jpeg','jpg','png','eps','bmp','tif','tiff','webp'])){
                          continue;
                        }

                        if($file->getSize() <= 10 * 1024 * 1024){ //10MB
                            $name = date("YmdHis") . $file->getClientOriginalName() ;
                            $file->move(public_path() . '/sitebucket/help-desk/', $name);
                            $insert['filename'] = $name;
                        }
                    }

                    if($insert['filename']){
                        $file_obj = new HelpDeskFiles();

                        $file_obj->fill($insert)->save();
                    }
                }
            }

            // End  Images

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
        $data = HelpDesk::findOrFail($id);

        // Access only for admin and support department and created by 
        if(userrole() == 1 || $data->departments->firstWhere('user_id', userid()) || $data->user_id == Auth::id()){
            $status = HelpDeskStatuses::all();

            add_log(52, NULL);

            $help_desk_departments = $data->departments;

            return view('help-desk.view',compact('data','status','help_desk_departments'));
        }

        return abort(404);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = Product::findOrFail($id);
        return view('help-desk.add',compact('edit'));
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

    public function getAll(Request $request)
    {
        $data = HelpDesk::query();
        if(!(userrole() == 1)){
            $data->where(function($query){
                $query->orwhere(function($q){
                    $q->whereHas('departments', function($p){
                        $p->where('user_id', userid());
                    });
                });

                $query->orwhere(function($q1){
                    $q1->where('user_id',userid());
                });
            });

        }

        if($request->filter_user != ""){
            $data->where('user_id',$request->filter_user);
        }

        if($request->filter_status != ""){
            $data->where('help_desk_status_id',$request->filter_status);
        }

        if($request->filter_type_of_customer_request != ""){
            $data->where('type_of_customer_request',$request->filter_type_of_customer_request);
        }

        if($request->filter_urgency != ""){
            $data->where('help_desk_urgency_id',$request->filter_urgency);
        }


        if($request->filter_sales_specialist != ""){
            $data->whereHas('user', function($q) use ($request){
                $q->where('role_id', 2)->where('id',$request->filter_sales_specialist);
            });
        }

        // Start Check Only Customer and thier self users
        if($request->filter_territory != ""){
            $data->where(function($query) use ($request) {
                $query->orwhere(function($query1) use ($request) {
                    $query1->whereHas('user', function($q) use ($request){
                        $q->whereHas('customer.territories', function($q1) use ($request){
                            $q1->where('id', $request->filter_territory);
                        });
                    });
                });

                $query->orwhere(function($query1) use ($request) {
                    $query1->whereHas('user', function($q) use ($request){
                        $q->whereHas('created_by_user.customer.territories', function($q1) use ($request){
                            $q1->where('id', $request->filter_territory);
                        });
                    });
                });
            });
        }

        if($request->filter_customer_class != ""){
            $data->where(function($query) use ($request) {
                $query->orwhere(function($query1) use ($request) {
                    $query1->whereHas('user', function($q) use ($request){
                        $q->whereHas('customer', function($q1) use ($request){
                            $q1->where('u_classification', $request->filter_customer_class);
                        });
                    });
                });

                $query->orwhere(function($query1) use ($request) {
                    $query1->whereHas('user', function($q) use ($request){
                        $q->whereHas('created_by_user.customer', function($q1) use ($request){
                            $q1->where('u_classification', $request->filter_customer_class);
                        });
                    });
                });
            });
        }

        if($request->filter_market_sector != ""){
            $data->where(function($query) use ($request) {
                $query->orwhere(function($query1) use ($request) {
                    $query1->whereHas('user', function($q) use ($request){
                        $q->whereHas('customer', function($q1) use ($request){
                            $q1->where('u_sector', $request->filter_market_sector);
                        });
                    });
                });

                $query->orwhere(function($query1) use ($request) {
                    $query1->whereHas('user', function($q) use ($request){
                        $q->whereHas('created_by_user.customer', function($q1) use ($request){
                            $q1->where('u_sector', $request->filter_market_sector);
                        });
                    });
                });
            });
        }

        // End Check Only Customer and thier self users

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('subject','LIKE',"%".$request->filter_search."%");
                $q->orwhere('ticket_number','LIKE',"%".$request->filter_search."%");
                $q->orwhere('type_of_customer_request','LIKE',"%".$request->filter_search."%");
                $q->orwhere('other_type_of_customer_request_name','LIKE',"%".$request->filter_search."%");
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
                            ->addColumn('ticket_number', function($row) {
                                if(@$row->ticket_number != ""){
                                    return '<a href="' . route('help-desk.show',$row->id). '">'.@$row->ticket_number.'</a>';
                                }
                                return @$row->ticket_number ?? "";
                            })
                            ->addColumn('type_of_customer_request', function($row) {
                                $text = @$row->type_of_customer_request ?? "";

                                if(@$row->type_of_customer_request == "Other Matters" && @$row->other_type_of_customer_request_name){
                                    $text .= " (".@$row->other_type_of_customer_request_name.")";
                                }
                                return $text;
                            })
                            ->addColumn('subject', function($row) {
                                return @$row->subject ?? "";
                            })
                            ->addColumn('user', function($row) {
                                return @$row->user->sales_specialist_name ?? "";
                            })
                            ->addColumn('status', function($row) {
                                $btn = "";
                                if(@$row->status){
                                    $btn .= '<b style="color:'.convert_hex_to_rgba(@$row->status->color_code).';background-color:'.convert_hex_to_rgba(@$row->status->color_code,0.1).';"  class="btn btn-sm">'.@$row->status->name ??  "-".'</b>';
                                }

                                return $btn;
                            })
                            ->addColumn('urgency', function($row) {
                                $btn = "";
                                if(@$row->urgency){
                                    $btn .= '<b style="color:'.convert_hex_to_rgba(@$row->urgency->color_code).';background-color:'.convert_hex_to_rgba(@$row->urgency->color_code,0.1).';"  class="btn btn-sm">'.@$row->urgency->name ??  "-".'</b>';
                                }

                                return $btn;
                            })
                            ->addColumn('action', function($row) {
                                $btn = '';

                                $btn .= '<a href="' . route('help-desk.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm">
                                  <i class="fa fa-eye"></i>
                                </a>';

                                return $btn;
                            })
                            ->addColumn('created_at', function($row) {
                                return date('M d, Y',strtotime($row->created_at));
                            })
                            ->orderColumn('ticket_number', function ($query, $order) {
                                $query->orderBy('ticket_number', $order);
                            })
                            ->orderColumn('type_of_customer_request', function ($query, $order) {
                                $query->orderBy('type_of_customer_request', $order);
                            })
                            ->orderColumn('subject', function ($query, $order) {
                                $query->orderBy('subject', $order);
                            })
                            ->orderColumn('created_at', function ($query, $order) {
                                $query->orderBy('created_at', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->join('help_desk_statuses', 'help_desks.help_desk_status_id', '=', 'help_desk_statuses.id')->orderBy('help_desk_statuses.name', $order);
                            })
                            ->orderColumn('urgency', function ($query, $order) {
                                $query->join('help_desk_urgencies', 'help_desks.help_desk_urgency_id', '=', 'help_desk_urgencies.id')->orderBy('help_desk_urgencies.name', $order);
                            })
                            ->orderColumn('user', function ($query, $order) {
                                $query->join('users', 'help_desks.user_id', '=', 'users.id')->orderBy('users.sales_specialist_name', $order);
                            })
                            ->rawColumns(['status','action','urgency','ticket_number'])
                            ->make(true);
    }

    public function updateStatus(Request $request)
    {   
        $input = $request->all();

        $rules = array(
                        'status' => 'required|exists:help_desk_statuses,id',
                        'help_desk_id' => 'required|exists:help_desks,id',
                  );
        if($input['status'] == 4){
            $rules['closed_reason'] = 'required';
            $rules['closed_image'] = 'required|max:5000|mimes:jpeg,png,jpg,eps,bmp,tif,tiff,webp';
        }else{
            $input['closed_reason'] = null;
            $input['closed_image'] = null;
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $obj = HelpDesk::find($request->help_desk_id);
            
            // Access only for admin and support department
            if(userrole() == 1 || @$obj->departments->firstWhere('user_id', userid()) ){

                if(!is_null($obj) && $request->status != ""){

                    /*Upload Image*/
                    if (request()->hasFile('closed_image')) {
                        $file = $request->file('closed_image');
                        $name = date("YmdHis") . $file->getClientOriginalName();
                        request()->file('closed_image')->move(public_path() . '/sitebucket/help-desk/', $name);
                        $input['closed_image'] = $name;
                    }

                    $obj->help_desk_status_id = $request->status;
                    $obj->updated_by = Auth::id();
                    $obj->closed_reason = $input['closed_reason'];
                    $obj->closed_image = $input['closed_image'];
                    $obj->save();

                    add_log(53, $obj->toArray());

                    $response = ['status'=>true,'message'=>'Status update successfully !'];


                    // Start Push Notification to receiver
                        $link = route('help-desk.show', $obj->id);

                        // Create Local Notification
                        $notification = new Notification();
                        $notification->type = 'HD';
                        $notification->title = 'Help Desk ticket '.$obj->ticket_number.' status updated.';
                        $notification->module = 'help-desk';
                        $notification->sap_connection_id = null;
                        $notification->message = 'Your Help Desk ticket <a href="'.$link.'"><b>'.$obj->ticket_number.'</b></a> status has been updated to <b>'.@$obj->status->name.'</b>.';
                        $notification->user_id = userid();
                        $notification->save();

                        if($notification->id){
                            $connection = new NotificationConnection();
                            $connection->notification_id = $notification->id;
                            $connection->user_id = $obj->user_id;
                            $connection->record_id = null;
                            $connection->save();
                        }

                        // Send One Signal Notification.
                        $fields['filters'] = array(array("field" => "tag", "key" => "user", "relation"=> "=", "value"=> $obj->user_id));
                        $message_text = $notification->title;

                        $push = OneSignal::sendPush($fields, $message_text);
                    // End Push Notification to receiver

                }else{
                    $response = ['status'=>false,'message'=>'Record not found !'];
                }
            }else{
                $response = ['status'=>false,'message'=>'Access Denied !'];
            }
        }

        return $response;
    }

    public function storeComment(Request $request)
    {   
        $input = $request->all();

        $rules = array(
                        'comment' => 'required',
                        'help_desk_id' => 'required|exists:help_desks,id',
                  );


        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $obj = HelpDesk::find($input['help_desk_id']);

            // Access only for admin and support department and created by 
            if(userrole() == 1 || userdepartment() == 1 || $obj->user_id == Auth::id()){

                $input['user_id'] = Auth::id();

                $comment = new HelpDeskComments();
                $message = "Comment created successfully.";

                $comment->fill($input)->save();

                add_log(54, $comment->toArray());

                $response = ['status'=>true,'message'=>$message];


                if($obj->user_id != userid()){
                // Start Push Notification to receiver
                    $link = route('help-desk.show', $obj->id);

                    // Create Local Notification
                    $notification = new Notification();
                    $notification->type = 'HD';
                    $notification->title = Auth::user()->sales_specialist_name.' added new comment on Help Desk ticket '.$obj->ticket_number.'.';
                    $notification->module = 'help-desk';
                    $notification->sap_connection_id = null;
                    $notification->message = Auth::user()->sales_specialist_name.' added new comment on Help Desk ticket <a href="'.$link.'"><b>'.$obj->ticket_number.'</b></a>.';
                    $notification->user_id = userid();
                    $notification->save();

                    if($notification->id){
                        $connection = new NotificationConnection();
                        $connection->notification_id = $notification->id;
                        $connection->user_id = $obj->user_id;
                        $connection->record_id = null;
                        $connection->save();
                    }

                    // Send One Signal Notification.
                    $fields['filters'] = array(array("field" => "tag", "key" => "user", "relation"=> "=", "value"=> $obj->user_id));
                    $message_text = $notification->title;

                    $push = OneSignal::sendPush($fields, $message_text);
                // End Push Notification to receiver
                }


            }else{
                return $response = ['status'=>false,'message'=>'Access Denied !'];
            }
        }

        return $response;
    }

    public function getAllComment(Request $request)
    {
        if ($request->ajax()) {
            
            $where = array('help_desk_id' => $request->help_desk_id);

            if ($request->id > 0) {
                $comments = HelpDeskComments::where('id', '<', $request->id)->where($where)->orderBy('id', 'DESC')->limit(10)->get();
            } else {
                $comments = HelpDeskComments::where($where)->orderBy('id', 'DESC')->limit(10)->get();
            }

            $output = "";
            $button = "";
            $last_id = "";

            $last = HelpDeskComments::where($where)->select('id')->first();

            if (!$comments->isEmpty()) {

                foreach ($comments as $comment) {
                    $output .= view('help-desk.ajax.comment',compact('comment'))->render();
                }

                $last_id = $comments->last()->id;

                if ($last_id != $last->id) {
                    $button = '<a href="javascript:" class="btn btn-primary" data-id="' . $last_id . '" id="view_more_btn">View More Comments</a>';
                }

            } else {

                $button = '';

            }

            return response()->json(['output' => $output, 'button' => $button]);
        }
    }

    public function storeAssignment(Request $request)
    {   
        $input = $request->all();

        $rules = array(
                        'help_desk_id' => 'required|exists:help_desks,id',
                        'department_id' => 'required|exists:departments,id',
                        'user_id' => 'required|exists:users,id',
                  );


        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $obj = HelpDesk::find($input['help_desk_id']);

            // Access only for admin and support department and created by 
            if(userrole() == 1 || $obj->departments->firstWhere('user_id', userid())){

                // HelpDeskDepartment::where('help_desk_id', $obj->id)->delete();

                HelpDeskDepartment::updateOrCreate(
                                    [
                                        'help_desk_id' => $obj->id,
                                    ],
                                    [
                                        'help_desk_id' => $obj->id,
                                        'department_id' => $input['department_id'],
                                        'user_id' => $input['user_id'],
                                    ]
                                );

                // add_log(54, $comment->toArray());

                $message = "User assigned successfully.";
                $response = ['status'=>true,'message'=>$message];


                // Start Push Notification to receiver
                    $link = route('help-desk.show', $obj->id);

                    // Create Local Notification
                    $notification = new Notification();
                    $notification->type = 'HD';
                    $notification->title = 'Assigned a new help desk ticket.';
                    $notification->module = 'help-desk';
                    $notification->sap_connection_id = null;
                    $notification->message = 'You have been assigned a new help desk ticket <a href="'.$link.'"><b>'.$obj->ticket_number.'</b></a>.';
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

    // Get Department
    public function getDepartment(Request $request){
        $search = $request->search;

        $data = Department::select('id','name')->where('is_active',true);

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
            $data = User::with('role')->where('department_id', @$request->department_id)->where('is_active', true)->where('id','!=',@$request->user_id);

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
