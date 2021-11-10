<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Validator;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('profile.index');
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
                    'name' => 'required|string|max:185',
                    'email' => 'required|max:185|unique:users,email,'.Auth::id().',id,deleted_at,NULL|regex:/(.+)@(.+)\.(.+)/i'
                );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $user = User::find(Auth::id());

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
                    'new_password' => 'required|string|min:8',
                    'confirm_password' => 'required|string|min:8|same:new_password',
                );
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $user = User::find(Auth::id());
            if(Hash::check($input['current_password'], $user->password)){
                $user->password = Hash::make($input['confirm_password']);
                $user->save();

                $response = ['status'=>true,'message'=>'Password changed successfully !'];
            }else{
                $response = ['status'=>false,'message'=>'Current password is wrong !'];
            }
        }

        return $response;
    }
}
