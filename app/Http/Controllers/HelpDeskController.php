<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\HelpDesk;
use App\Models\HelpDeskComments;
use App\Models\HelpDeskFiles;
use App\Models\HelpDeskStatuses;
use App\Models\HelpDeskUrgencies;
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
        return view('help-desk.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
                        'department_id' => 'required|exists:departments,id',
                        'help_desk_urgency_id' => 'nullable|exists:help_desk_urgencies,id',
                  );


        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            $input['user_id'] = Auth::id();
            $input['help_desk_status_id'] = 2;
            $input['ticket_number'] = '#OMS';

            $ticket = new HelpDesk();
            $message = "Help Desk ticket created successfully.";

            $ticket->fill($input)->save();

            $ticket->ticket_number = '#OMS'.$ticket->id;
            $ticket->save();

            // Start  Images
            $help_images_ids = array();
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
        return view('help-desk.view',compact('data'));
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
}
