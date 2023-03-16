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
                    ->where('name', 'Sales Personnel')->get()->toArray();
        
        // dd($role);
        if($role){
            if(in_array($file->getClientOriginalExtension(), ['csv'])){ //check file extension..
                $start_row = 1;
                if(($csv_file = fopen($file->getRealPath(), 'r')) !== FALSE) {
                    while (($read_data = fgetcsv($csv_file)) !== FALSE) {

                        if($start_row != 1){ //not to read header

                            $request['name'] = $read_data[0]; 
                            $request['email'] = $read_data[1]; 
                            $request['password'] = $read_data[2]; 
                            $request->validate([
                                "name"    => "required",
                                "email"    => "required|email|unique:users",
                                "password"    => "required",
                            ]);

                            $name = (explode(" ", $read_data[0]));
                            $sliced = array_slice($name, 0, -1);
                            $firstname = implode(" ", $sliced);
                            $lastname  = end($name);

                            // echo '<br><br>name: '.$firstname.' lastname: '.$lastname .'<br> username: '.$read_data[1]. ' password: '.$read_data[2].'<br>' ;
                            
                            User::create([
                                'department_id' => '',
                                'role_id'       => $role[0]->id,
                                'first_name'    => $firstname,
                                'last_name'     => $lastname,
                                'email'         => $request['email'],
                                'password'      => bcrypt($request['password']),
                                'is_active'     => 1,
                                'sales_specialist_name' => $request['name'],
                                'password_text' => $request['password']
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
