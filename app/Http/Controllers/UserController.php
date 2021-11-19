<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Validator;
use DataTables;
use Auth;
use Hash;
use Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::where('id','!=',1)->get();
        return view('user.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('id','!=',1)->get();
        return view('user.add',compact('roles'));
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
                    'email' => 'required|max:185|unique:users,email,NULL,id,deleted_at,NULL|regex:/(.+)@(.+)\.(.+)/i',
                    'role_id' => 'required|exists:roles,id',
                    'password' => 'required|string|min:8',
                    'confirm_password' => 'required|string|min:8|same:password',
                );

        if(isset($input['id'])){
            $rules['email'] = 'required|max:185|unique:users,email,'.$input['id'].',id,deleted_at,NULL|regex:/(.+)@(.+)\.(.+)/i';
            unset($rules['password']);
            unset($rules['confirm_password']);
        }

        if(request()->hasFile('profile')){
            $rules['profile'] = "required|max:5000|mimes:jpeg,png,jpg,eps,bmp,tif,tiff,webp";
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{

            if(isset($input['id'])){
                $user = User::find($input['id']);
                $message = "User details updated successfully.";
            }else{
                $user = new User();
                $message = "New User created successfully.";
                $input['password'] = Hash::make($input['confirm_password']);
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

            $user->fill($input)->save();

            if(!isset($input['id'])){
                $mail_data = array(
                                'name' => $input['first_name'] . " ". $input['last_name'],
                                'email' => $input['email'],
                                'password' => $request->password,
                            );

                Mail::send('emails.user_welcome', $mail_data, function($message) use($mail_data) {
                    $message->to($mail_data['email'], $mail_data['name'])
                            ->subject('Welcome to B2B CRM');
                });
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
        $edit = User::where('id','!=',1)->where('id',$id)->firstOrFail();
        $roles = Role::where('id','!=',1)->get();

        return view('user.add',compact('roles','edit'));
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
        $data = User::find($id);
        if(!is_null($data)){
            $data->delete();
            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function updateStatus($id)
    {
        $data = User::find($id);
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

        $data = User::where('users.id','!=',1);

        if($request->filter_role != ""){
            $data->where('role_id',$request->filter_role);
        }

        if($request->filter_status != ""){
            $data->where('is_active',$request->filter_status);
        }

        if($request->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('first_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('last_name','LIKE',"%".$request->filter_search."%");
                $q->orwhere('email','LIKE',"%".$request->filter_search."%");
            });
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {
                                $btn = '<a href="' . route('user.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <i class="fa fa-pencil"></i>
                                  </a>';
                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('user.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';
                                
                                return $btn;
                            })
                            ->addColumn('role', function($row) {
                                
                                if(@$row->role){
                                    return @$row->role->name;
                                }else{
                                    return "-";
                                }
                            })
                            ->addColumn('status', function($row) {
                                
                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<a href="javascript:"  data-url="' . route('user.status',$row->id) . '" class="btn btn-sm btn-light-success btn-inline status">Active</a>';
                                }else{
                                    $btn .= '<a href="javascript:"  data-url="' . route('user.status',$row->id) . '" class="btn btn-sm btn-light-danger btn-inline status">Inctive</a>';
                                }

                                return $btn;
                            })
                            ->orderColumn('role', function ($query, $order) {
                                $query->join('roles', 'users.role_id', '=', 'roles.id')
                                    ->orderBy('roles.name', $order);

                                // $query->whereHas('role',function($q) use ($order) {
                                //     $q->orderBy('name', $order);
                                // });
                            })
                            ->orderColumn('first_name', function ($query, $order) {
                                $query->orderBy('first_name', $order);
                            })
                            ->orderColumn('last_name', function ($query, $order) {
                                $query->orderBy('last_name', $order);
                            })
                            ->orderColumn('email', function ($query, $order) {
                                $query->orderBy('email', $order);
                            })
                            ->orderColumn('status', function ($query, $order) {
                                $query->orderBy('is_active', $order);
                            })
                            ->rawColumns(['action', 'role','status'])
                            ->make(true);
    }
}
