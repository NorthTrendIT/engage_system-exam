<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Validator;
use App\Models\User;
use App\Models\Customer;
use App\Models\Invoice;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        

        if(Auth::user()->role_id == 4){
            $data = User::where('u_card_code',Auth::user()->u_card_code)->firstOrFail();
            $apbw = Customer::where('u_card_code',$data->u_card_code)->where('sap_connection_id',1)->first();
            $ntmc = Customer::where('u_card_code',$data->u_card_code)->where('sap_connection_id',2)->first();
            $PHILCREST = Customer::where('u_card_code',$data->u_card_code)->where('sap_connection_id',3)->first();
            $PHILSYN = Customer::where('u_card_code',$data->u_card_code)->where('sap_connection_id',4)->first();            

            $totalOverdueAmount = Invoice::where(['card_code'=>@$data->card_code,'document_status'=>'bost_Open'])->sum('doc_entry');

            return view('profile.index',compact('data', 'totalOverdueAmount','apbw','ntmc','PHILCREST','PHILSYN'));
        }else{
            return view('profile.index');
        }        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                    'first_name' => 'required|string|max:185',
                    'last_name' => 'required|string|max:185',
                    'email' => 'required|max:185|unique:users,email,'.Auth::id().',id,deleted_at,NULL|regex:/(.+)@(.+)\.(.+)/i'
                );

        if(request()->hasFile('profile')){
            $rules['profile'] = "required|max:5000|mimes:jpeg,png,jpg,eps,bmp,tif,tiff,webp";
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $user = User::find(Auth::id());

            if($user->first_login == 1 && $user->email == $request->email){
                $check = User::where('email',$request->email)->where('id','!=',Auth::id())->first();
                if($check){
                    return $response = ['status'=>false,'message'=>"Oops ! please add new email address."];
                }
                
            }
            
            $old_profile = file_exists(public_path('sitebucket/users/') . "/" . $user->profile);
            if(request()->hasFile('profile') && $user->profile && $old_profile){
                unlink(public_path('sitebucket/users/') . "/" . $user->profile);
                $input['profile'] = null;
            }

            /*Upload Image*/
            if (request()->hasFile('profile')) {
                $file = $request->file('profile');
                $name = date("YmdHis") . $file->getClientOriginalName();
                request()->file('profile')->move(public_path() . '/sitebucket/users/', $name);
                $input['profile'] = $name;
            }

            $input['first_login'] = 0;
            
            $user->fill($input)->save();

            $response = ['status'=>true,'message'=>'Profile details update successfully !'];
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

    public function changePasswordIndex()
    {
        return view('profile.change_password');
    }

    public function changePasswordStore(Request $request){
        $input = $request->all();

        $rules = array(
                    'current_password' => 'required|string',
                    //'new_password' => 'required|max:20|regex:/^(?=.*\d)(?=.*[@$!%*#?&_-~<>;])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@$!%*#?&_-~<>;]{8,20}$/',
                    'new_password' => 'required',
                    'confirm_password' => 'required|same:new_password',
                );
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $user = User::find(Auth::id());
            if(Hash::check($input['current_password'], $user->password)){
                $user->password = Hash::make($input['confirm_password']);
                $user->password_text = NULL;
                $user->save();

                $response = ['status'=>true,'message'=>'Password changed successfully !'];
            }else{
                $response = ['status'=>false,'message'=>'Current password is wrong !'];
            }
        }

        return $response;
    }
}
