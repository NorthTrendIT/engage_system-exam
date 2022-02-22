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
use DataTables;
use Validator;
use Auth;

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

            // Assign Support Department
            HelpDeskDepartment::create(
                                    [
                                        'help_desk_id' => $ticket->id,
                                        'department_id' => 1,
                                    ]
                                );

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

                        if(!in_array($file->extension(),['jpeg','jpg','png'])){
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
        if(userrole() == 1 || userdepartment() == 1 || $data->user_id == Auth::id()){
            $status = HelpDeskStatuses::all();

            add_log(52, NULL);

            return view('help-desk.view',compact('data','status'));
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

        if(!(userrole() == 1 || userdepartment() == 1)){
            $data->where('user_id',Auth::id());
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

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('subject','LIKE',"%".$request->filter_search."%");
                $q->orwhere('ticket_number','LIKE',"%".$request->filter_search."%");
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
                                return @$row->ticket_number ?? "";
                            })
                            ->addColumn('type_of_customer_request', function($row) {
                                return @$row->type_of_customer_request ?? "";
                            })
                            ->addColumn('subject', function($row) {
                                return @$row->subject ?? "";
                            })
                            ->addColumn('status', function($row) {
                                $btn = "";
                                if(@$row->status){
                                    $btn .= '<b style="color: '.@$row->status->color_code.'" class="badge badge-light-dark">'.@$row->status->name ??  "-".'</b>';
                                }

                                return $btn;
                            })
                            ->addColumn('urgency', function($row) {
                                $btn = "";
                                if(@$row->urgency){
                                    $btn .= '<b style="color: '.@$row->urgency->color_code.'" class="badge badge-light-dark">'.@$row->urgency->name ??  "-".'</b>';
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
                                //$query->orderBy('help_desk_status_id', $order);
                                $query->join('help_desk_statuses', 'help_desks.help_desk_status_id', '=', 'help_desk_statuses.id')->orderBy('help_desk_statuses.name', $order);
                            })
                            ->orderColumn('urgency', function ($query, $order) {
                                //$query->orderBy('help_desk_urgency_id', $order);
                                $query->join('help_desk_urgencies', 'help_desks.help_desk_urgency_id', '=', 'help_desk_urgencies.id')->orderBy('help_desk_urgencies.name', $order);

                            })
                            ->rawColumns(['status','action','urgency'])
                            ->make(true);
    }

    public function updateStatus(Request $request)
    {   
        // Access only for admin and support department
        if(userrole() == 1 || userdepartment() == 1){
            
            $obj = HelpDesk::find($request->id);

            if(!is_null($obj) && $request->status != ""){
                $obj->help_desk_status_id = $request->status;
                $obj->save();

                add_log(53, $obj->toArray());

                $response = ['status'=>true,'message'=>'Status update successfully !'];
            }else{
                $response = ['status'=>false,'message'=>'Record not found !'];
            }

            return $response;
        }else{
            return $response = ['status'=>false,'message'=>'Access Denied !'];
        }
    }

    public function storeComment(Request $request)
    {   
        $input = $request->all();

        $rules = array(
                        'comment' => 'required',
                        'help_desk_id' => 'nullable|exists:help_desks,id',
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
}
