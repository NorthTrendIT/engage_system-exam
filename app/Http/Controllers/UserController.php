<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Location;
use App\Models\Department;
use App\Models\SapConnection;
use App\Models\User;
use Validator;
use DataTables;
use Auth;
use Hash;
use Mail;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportUser;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // If not admin then show only its
        if(userrole() != 1){
            $roles = Role::where('user_id',Auth::id())->get();
        }else{
            $roles = Role::where('id','!=',1)->whereNull('user_id')->get();
        }

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
        $provinces = Location::whereNull('parent_id')->where('is_active',true)->get();

        // If not admin then show only its
        if(userrole() != 1){
            $departments = Department::where('user_id',Auth::id())->where('is_active',true)->get();
        }else{
            $departments = Department::whereNull('user_id')->where('is_active',true)->get();
        }

        return view('user.add',compact('roles','provinces','departments'));
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
                    'department_id' => 'required|exists:departments,id',
                    'parent_id' => 'nullable|exists:users,id',
                    'role_id' => 'required|exists:roles,id',
                    'city_id' => 'nullable|exists:locations,id',
                    'province_id' => 'nullable|exists:locations,id',
                    'password' => 'required|max:20|regex:/^(?=.*\d)(?=.*[@$!%*#?&_-~<>;])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@$!%*#?&_-~<>;]{8,20}$/',                    
                    'confirm_password' => 'required|same:password',
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
                $input['default_profile_color'] = get_hex_color();

                $input['is_sap_user'] = false;
                if(userrole() != 1){
                    $input['created_by'] = Auth::id();
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

            $input['sales_specialist_name'] = $input['first_name']." ".$input['last_name'];

            $user->fill($input)->save();

            if(!isset($input['id'])){
                $mail_data = array(
                                'name' => $input['first_name'] . " ". $input['last_name'],
                                'email' => $input['email'],
                                // 'password' => $request->password,
                                'link' => route('login-by-link', encryptValue($user->id."-".time())),
                            );

                Mail::send('emails.user_welcome', $mail_data, function($message) use($mail_data) {
                    $message->to($mail_data['email'], $mail_data['name'])
                            ->subject('Welcome to B2B CRM');
                });
            }

            if($message == 'New User created successfully.'){
                // Add User Created log
                add_log(3, array('user_id' => $user->id));
            } else if($message == 'User details updated successfully.'){
                // Add User Updated log
                add_log(4, array('user_id' => $user->id));
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
        // If not admin then show only its
        if(userrole() != 1){
            $data = User::where('id','!=',1)->where('id',$id)->where('created_by',Auth::id())->firstOrFail();
        }else{
            $data = User::where('id','!=',1)->where('id',$id)->firstOrFail();
        }

        $tree = json_encode($this->getUserTreeData($id));

        $sap_connection_id = explode(',', @$data->multi_sap_connection_id);
        $sap_connections = SapConnection::whereIn('id', $sap_connection_id)->pluck('company_name')->toArray();
        $sap_connections = implode(", ", $sap_connections);

        return view('user.view',compact('data','tree','sap_connections'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // If not admin then show only its
        if(userrole() != 1){
            $edit = User::where('id','!=',1)->where('id',$id)->where('created_by',Auth::id())->firstOrFail();
        }else{
            $edit = User::where('id','!=',1)->where('id',$id)->firstOrFail();
        }

        $provinces = Location::whereNull('parent_id')->where('is_active',true)->get();
        $parents = User::where('id','!=',1)->where('parent_id',$edit->parent_id)->get();

        // If not admin then show only its
        if(userrole() != 1){
            $departments = Department::where('user_id',Auth::id())->where('is_active',true)->get();
        }else{
            $departments = Department::whereNull('user_id')->where('is_active',true)->get();
        }


        $cities = collect();
        if($edit->province_id){
            $cities = Location::where('parent_id',$edit->province_id)->where('is_active',true)->get();
        }

        return view('user.add',compact('edit','provinces','cities','departments'));
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
        $data = User::where('id', $id);
        // If not admin then show only its
        if(userrole() != 1){
            $data->where('created_by',Auth::id());
        }
        $data = $data->first();


        if(!is_null($data)){
            // Add user delete log
            add_log(5, array('user_data' => $data));

            $data->delete();

            $response = ['status'=>true,'message'=>'Record deleted successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function updateStatus($id)
    {
        $data = User::where('id', $id);
        // If not admin then show only its
        if(userrole() != 1){
            $data->where('created_by',Auth::id());
        }
        $data = $data->first();

        if(!is_null($data)){
            $data->is_active = !$data->is_active;
            $data->save();
            $response = ['status'=>true,'message'=>'Status update successfully !'];
        }else{
            $response = ['status'=>false,'message'=>'Record not found !'];
        }
        return $response;
    }

    public function getCity(Request $request)
    {
        $id = $request->id;
        $cities = collect();
        if($id != null){
            $cities = Location::where('parent_id',$id)->where('is_active',true)->get();
        }

        return $cities;
    }

    public function getRoles(Request $request)
    {
        $id = $request->department_id;
        $roles = collect();
        if($id != null){
            $department = Department::where('id',$id)->where('is_active',true)->first();

            if(@$department->roles){
                $roles = $department->roles()->whereNotIn('role_id',['4'])->with('role')->get();
            }
        }

        return $roles;
    }

    public function getParents(Request $request)
    {
        $role_id = $request->role_id;
        $users = collect();
        $parent_name = "";
        if($role_id != null){
            $role = Role::where('id',$role_id)->first();

            if(!is_null($role)){

                if(!is_null($role->parent_id)){

                    $parent_name = @$role->parent->name;
                    $users = User::where('role_id',$role->parent_id)->where('is_active',true);

                    if(isset($request->id)){
                        $users->where('id','!=',$request->id);
                    }
                    $users = $users->get();
                }
            }
        }


        return ['users' => $users, 'parent_name' => $parent_name];
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
                $q->orwhere('sales_specialist_name','LIKE',"%".$request->filter_search."%");
            });
        }

        if(userrole() != 1){
            $data->where('created_by',Auth::id());
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });

        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('action', function($row) {
                                $btn = "";

                                if( (is_null($row->created_by) && userrole() == 1) || (!is_null($row->created_by) && $row->created_by == Auth::id()) ){
                                    $btn .= '<a href="' . route('user.edit',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm mr-10" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                      </a>';
                                }

                                $btn .= ' <a href="javascript:void(0)" data-url="' . route('user.destroy',$row->id) . '" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete mr-10" title="Delete">
                                    <i class="fa fa-trash"></i>
                                  </a>';

                                $btn .= ' <a href="' . route('user.show',$row->id). '" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm mr-10" title="View">
                                    <i class="fa fa-eye"></i>
                                  </a>';


                                if( (is_null($row->created_by) && userrole() == 1) || (!is_null($row->created_by) && $row->created_by == Auth::id()) ){
                                    $btn .= '<a href="javascript:" data-href="' . route('login-by-link', encryptValue($row->id."-".time())). '" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm copy_login_link" title="Copy Login Link">
                                        <i class="fa fa-link"></i>
                                      </a>';
                                }

                                return $btn;
                            })
                            ->addColumn('territory', function($row) {
                                return @$row->territory->description ?? "-";
                            })
                            ->addColumn('role', function($row) {

                                if(@$row->role){
                                    return @$row->role->name;
                                }else{
                                    return "-";
                                }
                            })
                            ->addColumn('parent',function($row){
                                if(@$row->parent){
                                    return @$row->parent->first_name.' '.@$row->parent->last_name;
                                }else{
                                    return "-";
                                }
                            })
                            ->addColumn('status', function($row) {

                                $btn = "";
                                if($row->is_active){
                                    $btn .= '<div class="form-group">
                                    <div class="col-3">
                                     <span class="switch">
                                      <label>
                                       <input type="checkbox" checked="checked" name="status" class="status" data-url="' . route('user.status',$row->id) . '"/>
                                       <span></span>
                                      </label>
                                     </span>
                                    </div>';
                                }else{
                                    $btn .= '<div class="form-group">
                                    <div class="col-3">
                                     <span class="switch">
                                      <label>
                                       <input type="checkbox" name="status" class="status" data-url="' . route('user.status',$row->id) . '"/>
                                       <span></span>
                                      </label>
                                     </span>
                                    </div>';
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
                            ->orderColumn('territory', function ($query, $order) {
                                $query->select('users.*')->join('territories', 'users.territory_id', '=', 'territories.id')
                                    ->orderBy('territories.description', $order);

                            })
                            ->rawColumns(['action', 'role','status','territory'])
                            ->make(true);
    }

    public function changePasswordStore(Request $request){
        $input = $request->all();

        $rules = array(
                    'id' => 'required|exists:users,id',
                    'new_password' => 'required|max:20|regex:/^(?=.*\d)(?=.*[@$!%*#?&_-~<>;])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@$!%*#?&_-~<>;]{8,20}$/',
                    'confirm_password' => 'required|same:new_password',
                );
        if(userrole() != 1){
            $rules['id'] = "required|exists:users,id,created_by,".userid();
        }

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = ['status'=>false,'message'=>$validator->errors()->first()];
        }else{
            $user = User::find($input['id']);

            if($user){
                $user->password_text = $input['confirm_password'];
                $user->password = Hash::make($input['confirm_password']);
                $user->save();

                //save log
                add_log(44, $input);

                $response = ['status'=>true,'message'=>'Password changed successfully !'];
            }else{
                $response = ['status'=>false,'message'=>'User not found !'];
            }

        }

        return $response;
    }

    public function getUserTreeData($user_id){

        $result = array();
        $user = User::find($user_id);
        if($user){
            $temp = array(
                            'title' => @$user->first_name." ".@$user->last_name,
                            'name' => @$user->role->name,
                        );

            $result = array_merge($result,$temp);

            $child = $this->getUserChildData($user_id);

            if(count($child)){
                $result['children'] = $child;
            }
        }

        return $result;
    }

    public function getUserChildData($user_id)
    {
        $result = array();
        $users = User::where('parent_id',$user_id)->get();

        if(count($users)){
            foreach ($users as $key => $value) {

                $temp = array(
                            'title' => @$value->first_name." ".@$value->last_name,
                            'name' => @$value->role->name,
                        );

                $child = $this->getUserChildData($value->id);

                if(count($child)){
                    $temp['children'] = $child;
                }

                $result[$key] = $temp;
            }
        }

        return $result;
    }

    public function userChangePassword(Request $request){
        $filename = public_path('assets/files/NTMC3.csv');
        $file = fopen($filename, "r");
        $all_data = array();

        $i = 0;
        while ( ($data = fgetcsv($file, 30000, ",")) !==FALSE ) { 
            if($i > 0){
                
                $update = User::where('u_card_code',$data[6])
                                ->where('email',$data[4])
                                ->update(['password'=>Hash::make($data[5])]);
            }
            $i++;
        }
        return "complete";
    }

    public function userChangePasswordABPW(Request $request){
        $filename = public_path('assets/files/APBW2.csv');
        $file = fopen($filename, "r");
        $all_data = array();

        $i = 0;
        while ( ($data = fgetcsv($file, 30000, ",")) !==FALSE ) { 
            if($i > 0){
                
                $update = User::where('u_card_code',$data[6])
                                ->where('email',$data[4])
                                ->update(['password'=>Hash::make($data[5])]);
            }
            $i++;
        }
        return "complete";
    }

    public function userChangePasswordSOLID(Request $request){
        $filename = public_path('assets/files/SOLID.csv');
        $file = fopen($filename, "r");
        $all_data = array();

        $i = 0;
        while ( ($data = fgetcsv($file, 30000, ",")) !==FALSE ) { 
            if($i > 0){
                
                $update = User::where('u_card_code',$data[6])
                                ->where('email',$data[4])
                                ->update(['password'=>Hash::make($data[5])]);
            }
            $i++;
        }
        return "complete";
    }

    public function userChangePasswordPHILCREST(Request $request){
        $filename = public_path('assets/files/PHILCREST.csv');
        $file = fopen($filename, "r");
        $all_data = array();

        $i = 0;
        while ( ($data = fgetcsv($file, 30000, ",")) !==FALSE ) { 
            if($i > 0){
                
                $update = User::where('u_card_code',$data[6])
                                ->where('email',$data[4])
                                ->update(['password'=>Hash::make($data[5])]);
            }
            $i++;
        }
        return "complete";
    }

    public function userChangePasswordPHILSYN(Request $request){
        $filename = public_path('assets/files/PHILSYN.csv');
        $file = fopen($filename, "r");
        $all_data = array();

        $i = 0;
        while ( ($data = fgetcsv($file, 30000, ",")) !==FALSE ) { 
            if($i > 0){
                
                $update = User::where('u_card_code',$data[6])
                                ->where('email',$data[4])
                                ->update(['password'=>Hash::make($data[5])]);
            }
            $i++;
        }
        return "complete";
    }

    public function export(Request $request){
        $filter = collect();
        if(@$request->data){
          $filter = json_decode(base64_decode($request->data));
        }

        $data = User::where('users.id','!=',1);

        if($filter->filter_role != ""){
            $data->where('role_id',$filter->filter_role);
        }

        if($filter->filter_status != ""){
            $data->where('is_active',$filter->filter_status);
        }

        if($filter->filter_search != ""){
            $data->where(function($q) use ($request) {
                $q->orwhere('first_name','LIKE',"%".$filter->filter_search."%");
                $q->orwhere('last_name','LIKE',"%".$filter->filter_search."%");
                $q->orwhere('email','LIKE',"%".$filter->filter_search."%");
                $q->orwhere('sales_specialist_name','LIKE',"%".$filter->filter_search."%");
            });
        }

        if(userrole() != 1){
            $data->where('created_by',Auth::id());
        }

        $data->when(!isset($request->order), function ($q) {
            $q->orderBy('id', 'desc');
        });
        $data = $data->get();

        $records = array();
        foreach($data as $key => $value){
                                
            $records[] = array(
                            'no' => $key + 1,
                            'role' => @$value->role->name ?? "-",
                            'first_name' => @$value->first_name ?? "-",
                            'last_name' => @$value->last_name ?? "-",
                            'email' => @$value->email ?? "-",
                            'parent' => @$value->parent->first_name.' '.@$value->parent->last_name,
                            'status' => ($value->is_active == 1)?'Active':'Inactive',
                          );
        }
        if(count($records)){
            $title = 'User Report '.date('dmY').'.xlsx';
            return Excel::download(new ExportUser($records), $title);
        }

        \Session::flash('error_message', common_error_msg('excel_download'));
        return redirect()->back();
    }
}
