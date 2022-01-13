<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomersSalesSpecialist;
use App\Models\Conversation;

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
        return view('conversation.add');
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
                    'user_id' => 'nullable|exists:users,id,deleted_at,NULL,is_active,1',
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{


            $conversation = Conversation::query();

            $conversation->where(function($q) use ($input) {
                $q->orwhere(['sender_id' => $input['user_id'], 'receiver_id' => userid()]);
                $q->orwhere(['sender_id' => userid(), 'receiver_id' => $input['user_id']]);
            });

            $conversation = $conversation->first();

            if(is_null($conversation)){
                $conversation = new Conversation();
                $conversation->fill([ 'sender_id' => userid(), 'receiver_id' => $input['user_id'] ])->save();
            }

            
            if(!empty($conversation->id)){
                $conversation->receiver_delete = false;
                $conversation->sender_delete = false;
                $conversation->timestamps = false;
                $conversation->updated_at = date("Y-m-d H:i:s");
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
        //
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
        }

        if(@$request->search != ""){
            $users->where(function($q) use ($request) {
                $q->orwhere('first_name','LIKE',"%".$request->search."%");
                $q->orwhere('last_name','LIKE',"%".$request->search."%");
                $q->orwhere('email','LIKE',"%".$request->search."%");
                $q->orwhere('sales_specialist_name','LIKE',"%".$request->search."%");
            });
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
