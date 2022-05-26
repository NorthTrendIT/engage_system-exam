<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $result = $children = array();
            $result['children'] = array();
            $user = User::find(1);

            if($user){
                $temp = array(
                                'title' => @$user->first_name." ".@$user->last_name,
                                'name' => @$user->role->name,
                            );
                $result = array_merge($result,$temp);

                $parent_users = User::where('id','!=',1)->whereNull('parent_id')->where('is_sap_user',0)->whereNull('created_by')->get();
                if(count($parent_users)){

                    foreach ($parent_users as $key => $value) {
                        $temp = array(
                                    'title' => @$value->first_name." ".@$value->last_name,
                                    'name' => @$value->role->name,
                                );


                        $child = app(UserController::class)->getUserChildData($value->id);

                        if(count($child)){
                            $temp['children'] = $child;
                        }

                        $children[$key] = $temp;
                    }

                }
                
                $result['children'] = array_merge($result['children'],$children);
            }

            $tree = json_encode($result);

            return $tree;
        }

        return view('organisation.index');
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
        //
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
}
