<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Form;
use DB;
use Redirect;
use App\Models\User;

class Userupload extends Controller
{
    public function index()
    {
        return view('user/backdoor_ssupload');
    }

    public function showUploadFile(Request $request) {
        $file = $request->file('csv');

        $role = DB::table('roles')
                    ->select('id')
                    ->where('name','like','%Sales Personnel%')->get()->toArray();
        
        $dept = DB::table('departments')
                    ->select('id')
                    ->where('name','like','%Sales%')->get()->toArray();
        
        // dd($role);
        if($role){
            if(in_array($file->getClientOriginalExtension(), ['csv'])){ //check file extension..
                $start_row = 1;
                if(($csv_file = fopen($file->getRealPath(), 'r')) !== FALSE) {
                    while (($read_data = fgetcsv($csv_file)) !== FALSE) {

                        if($start_row != 1){ //not to read header

                            $request['first_name'] = $read_data[0]; 
                            $request['last_name'] = $read_data[1]; 
                            $request['email'] =     $read_data[2]; 
                            $request['password'] =  $read_data[3]; 

                            $request->validate([
                                "first_name"    => "required",
                                "last_name"    => "required",
                                "email"    => "required|email|unique:users",
                                "password"    => "required",
                            ]);
                            
                            User::create([
                                'department_id' => $dept[0]->id,
                                'role_id'       => $role[0]->id,
                                'first_name'    => $request['first_name'],
                                'last_name'     => $request['last_name'],
                                'email'         => $request['email'],
                                'password'      => bcrypt($request['password']),
                                'is_active'     => 1,
                                'sales_specialist_name' => $request['first_name'].' '.$request['last_name'],
                                'password_text' => $request['password'],
                                'default_profile_color' => get_hex_color(),
                                'first_login' => 1
                            ]);
                        }
                        $start_row++;
                    } 
                    fclose($csv_file);
                    return back()->with('success', 'Sales Personnel uploaded successfully!');
                }
            }else{
                return Redirect::back()->withErrors(['msg' => "File extension is not CSV"]);
            }
        }
        else{
            return Redirect::back()->withErrors(['msg' => "Sales Personnel role not found."]); ;
        }
        
     }

}
