<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomersSalesSpecialist;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Department;

use Auth;
use Validator;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('conversation.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::where('id','!=',1)->where('is_active',true)->orderby('name','ASC')->get();
        return view('conversation.add', compact('departments'));
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
                    'user_id' => 'required|exists:users,id,deleted_at,NULL,is_active,1',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{


            $conversation = Conversation::where(['sender_id' => $input['user_id'], 'receiver_id' => userid()])->first();

            if(is_null($conversation)){
                $conversation = Conversation::where(['sender_id' => userid(), 'receiver_id' => $input['user_id']])->first();
            }


            if(is_null($conversation)){
                $conversation = new Conversation();
                $conversation->fill([ 'sender_id' => userid(), 'receiver_id' => $input['user_id'] ])->save();
            
            }else if(!empty($conversation->id)){
                $conversation->receiver_delete = false;
                $conversation->sender_delete = false;
                $conversation->save();
            }
            
            $response = ['status'=>true, 'message'=> 'Conversation created successfully'];
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
        $conversation = Conversation::find($id);
        if($conversation){            
            if($conversation->sender_id == userid()){
                $set = ['sender_delete'=>true];
                $conversation->sender_delete = true;

            }else if($conversation->receiver_id == userid()){

                $set = ['receiver_delete'=>true];
                $conversation->receiver_delete = true;
            }else{
                return $response = ['status'=>false,'message'=>"Conversation not found"];
            }

            $conversation->timestamps = false;
            if($conversation->save()){
                ConversationMessage::where('conversation_id',$id)->update($set);

                $response = ['status'=>true,'message'=>"Conversation deleted successfully !"];
            }else{
                $response = ['status'=>false,'message'=>"Something went to wrong"];
            }
        }else{
            $response = ['status'=>false,'message'=>"Conversation not found"];
        }

        return $response;
    }


    public function storeMessage(Request $request){
        $input = $request->all();

        $rules = array(
                    'user_id' => 'required|exists:users,id,deleted_at,NULL,is_active,1',
                    'conversation_id' => 'required|exists:conversations,id,deleted_at,NULL',
                    'message' => 'required',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $conversation = Conversation::find($request->conversation_id);
            if(!is_null($conversation)){
                if( !($conversation->sender_id == userid() || $conversation->receiver_id == userid() )){
                    return $response = ['status'=>false, 'message'=> '', 'html' => ""];
                }

                $conversation->updated_at = date("Y-m-d H:i:s");
                $conversation->save();

            }

            $input['user_id'] = userid();
            
            $message = new ConversationMessage();
            $message->fill($input)->save();

            $html = view('conversation.ajax.message_list',compact('message'))->render();

            $response = ['status'=>true, 'message'=> '', 'html' => $html];
        }
        return $response;
    }

    public function updateMessage(Request $request){
        $input = $request->all();

        $rules = array(
                    'conversation_id' => 'required|exists:conversations,id,deleted_at,NULL',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            $conversation = Conversation::find($request->conversation_id);
            if(!is_null($conversation)){
                if( !($conversation->sender_id == userid() || $conversation->receiver_id == userid() )){
                    return $response = ['status'=>false, 'message'=> '', 'html' => ""];
                }  
            }

            $where = array('conversation_id' => $conversation->id);
            ConversationMessage::where($where)->where('user_id','<>', userid())->update(['is_read'=>true]);

            $message = ConversationMessage::where($where)->where('user_id','<>', userid())->orderby('id','desc')->first();

            $html = view('conversation.ajax.message_list',compact('message'))->render();

            $response = ['status'=>true, 'message'=> '', 'html' => $html];
        }
        return $response;
    }

    public function getConversationList(Request $request){

        $idr = Conversation::where('receiver_id', userid())->where('receiver_delete',false);
        if($request->search != ""){
            $idr->whereHas('sender', function($q) use ($request) {
                $q->where('first_name','LIKE',"%".$request->search."%");
                $q->orwhere('last_name','LIKE',"%".$request->search."%");
                $q->orwhere('email','LIKE',"%".$request->search."%");
                $q->orwhere('sales_specialist_name','LIKE',"%".$request->search."%");
            });
        }
        $idr = $idr->pluck('id')->toArray();


        $ids = Conversation::where('sender_id', userid())->where('sender_delete',false);
        if($request->search != ""){
            $ids->whereHas('receiver', function($q) use ($request) {
                $q->where('first_name','LIKE',"%".$request->search."%");
                $q->orwhere('last_name','LIKE',"%".$request->search."%");
                $q->orwhere('email','LIKE',"%".$request->search."%");
                $q->orwhere('sales_specialist_name','LIKE',"%".$request->search."%");
            });
        }
        $ids = $ids->pluck('id')->toArray();


        $ids = array_merge($ids,$idr);

        $data = Conversation::with('sender','receiver')
                            ->whereIn('id', $ids)
                            ->orderBy('updated_at','DESC')
                            ->get();

        $html = "";     
        if(count($data)){
            $html .= view('conversation.ajax.list', compact('data'))->render();
        }else{
            $html = "<div class='text-center'><h2>Result Not Found !</h2></div>";
        }

        return $response = [ 'html' => $html ];
    }

    public function getConversationMessageList(Request $request){
        if ($request->ajax()) {

            $user = false;
            $conversation = Conversation::find($request->conversation_id);
            if(!is_null($conversation)){
                if($conversation->sender_id == userid()){
                    $user = $conversation->receiver;
                }else if($conversation->receiver_id == userid()){
                    $user = $conversation->sender;
                }  
            }

            if($conversation && $user){

                $where = array('conversation_id' => $conversation->id);
                
                ConversationMessage::where($where)->where('user_id','<>', userid())->update(['is_read'=>true]);


                if ($request->id > 0) {
                    $data = ConversationMessage::has('user')->where('id', '<', $request->id)->where($where)->orderBy('id', 'DESC')->limit(20)->get();
                } else {
                    $data = ConversationMessage::has('user')->where($where)->orderBy('id', 'DESC')->limit(20)->get();
                }



                $html = "";
                $button = "";
                $last_id = "";

                $last = ConversationMessage::has('user')->where($where)->select('id')->first();
                if (!$data->isEmpty()) {


                    foreach ($data->reverse() as $message) {
                        if($conversation->sender_id == userid()){
                            $is_deleted = $message->sender_delete;
                        }else{
                            $is_deleted = $message->receiver_delete;
                        }

                        if(!$is_deleted){
                            $html .= view('conversation.ajax.message_list',compact('message'))->render();
                        }
                    }


                    $last_id = $data->last()->id;

                    if ($last_id != $last->id) {
                        $button = '<div class="d-flex flex-center pb-1">
                                       <a href="javascript:" class="btn btn-info font-weight-bolder font-size-sm py-1 px-3 view_more_message" data-id="' . $last_id . '">View More</a>
                                    </div>';
                    }


                } else {
                    $button = '';
                }

                return response()->json(['html' => $html, 'button' => $button, 'user' => $user]);
            }else{
                return response()->json(['html' => "", 'button' => "", 'user' => ""]);
            }
        }
    }


    public function searchNewUser(Request $request){
        
        $user_ids = $self_user_ids = $ss_ids = $customer_ids = [];
        if(userrole() == 4){
            //Is Customer

            if(in_array(@$request->category,['', 'self-users'])){
                $self_user_ids = User::where('created_by', userid())->pluck('id')->toArray();
            }

            if(in_array(@$request->category,['', 'sales-specialist'])){
                $ss_ids = CustomersSalesSpecialist::where('customer_id', @Auth::user()->customer_id)->pluck('ss_id')->toArray();
            }

            $user_ids = array_merge($self_user_ids, $ss_ids);

        }elseif(userrole() == 2){
            //Is SS
            if(in_array(@$request->category,['', 'customers'])){
                $customer_ids = CustomersSalesSpecialist::where('ss_id', userid())->pluck('customer_id')->toArray();
            }

            $user_ids = array_merge($customer_ids);


        }elseif(@Auth::user()->created_by && @Auth::user()->created_by_user->customer_id){
            //Is Customer User

            if(in_array(@$request->category,['', 'sales-specialist'])){
                $ss_ids = CustomersSalesSpecialist::where('customer_id', @Auth::user()->created_by_user->customer_id)->pluck('ss_id')->toArray();
                $user_ids = $ss_ids;
            }

            if(in_array(@$request->category,['', 'parent-customer'])){
                $user_ids = array_merge($user_ids, [ @Auth::user()->parent_id ]);
            }

            if(in_array(@$request->category,['', 'parent-user'])){
                $user_ids = array_merge($user_ids, [ @Auth::user()->created_by ]);
            }

        }else{
            //Other User

            if(in_array(@$request->category,['', 'self-users'])){
                $user_ids = array_merge([@Auth::user()->parent_id]);
            }

        }


        $users = User::where('is_active', true)->orderby('first_name', 'ASC');

        if(!empty($user_ids)){
            $users->whereIn('id', $user_ids);
        }else{
            $html = "<div class='text-center'><h2>Result Not Found !</h2></div>";
            return $response = [ 'html' => $html ];
        }

        if(@$request->search != ""){
            $users->where(function($q) use ($request) {
                $q->orwhere('first_name','LIKE',"%".$request->search."%");
                $q->orwhere('last_name','LIKE',"%".$request->search."%");
                $q->orwhere('email','LIKE',"%".$request->search."%");
                $q->orwhere('sales_specialist_name','LIKE',"%".$request->search."%");
            });
        }

        if(@$request->department != ""){
            $users->where('department_id', $request->department);
        }

        $users = $users->get();

        $html = "";
        if(count($users)){
            foreach ($users as $user) {
                $html .= view('conversation.ajax.search_user',compact('user'))->render();
            }
        }else{
            $html = "<div class='text-center'><h2>Result Not Found !</h2></div>";
        }

        return $response = [ 'html' => $html ];
    }
}
