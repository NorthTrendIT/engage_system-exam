<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationConnection;
use App\Models\NotificationDocument;
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
        return view('news-and-announcement.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('news-and-announcement.add');
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
        $input = $request->all();

        $rules = array(
                    'title' => 'required',
                    'type' => 'required',
                    'message' => 'required',
                );


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
            $notification->type = $input['type'];
            $notification->title = $input['title'];
            $notification->message = $input['message'];
            $notification->user_id = Auth::user()->id;
            $notification->save();

            // Start Notification Document
            $docs_ids = array();
            if(isset($input['documents'])){
                foreach ($input['documents'] as $key => $value) {
                    $value['notification_id'] = $notification->id;

                    if(isset($value['file']) && is_object($value['file'])){
                        $file = $value['file'];

                        if(!in_array($file->extension(),['jpeg','jpg','png','eps','bmp','tif','tiff','webp', 'pdf','doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods'])){
                        continue;
                        }

                        if($file->getSize() <= 10 * 1024 * 1024){ //10MB
                            $name = date("YmdHis") . $file->getClientOriginalName() ;
                            $file->move(public_path() . '/sitebucket/news-and-announcement/', $name);
                            $value['file'] = $name;
                        }
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
        $edit = Notification::with(['user', 'documents'])->where('id', $id)->firstOrFail();
        // dd($edit);
        return view('news-and-announcement.add', compact('edit'));
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

    public function getAll(Request $request){

        $data = Notification::with(['user', 'documents']);

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
                            ->addColumn('title', function($row) {
                                return $row->title;
                            })
                            ->addColumn('user_name', function($row) {
                                return $row->user->first_name;
                            })
                            ->addColumn('type', function($row) {
                                if($row->type == 'A'){
                                    return 'Announcement';
                                }
                                if($row->type == 'N'){
                                    return 'News';
                                }
                            })
                            ->addColumn('is_important', function($row) {
                                if($row->is_important){
                                    return "Yes";
                                } else {
                                    return "No";
                                }
                            })
                            ->orderColumn('title', function ($query, $order) {
                                $query->orderBy('company_name', $order);
                            })
                            ->orderColumn('user_name', function ($query, $order) {
                                $query->orderBy('user_name', $order);
                            })
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('news-and-announcement.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                return $btn;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
    }
}
