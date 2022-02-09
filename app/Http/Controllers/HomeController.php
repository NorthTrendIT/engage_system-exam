<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalOrder;
use App\Models\CustomerPromotion;
use Auth;

class HomeController extends Controller
{
    public function index()
    {

        if(Auth::user()->role_id == 1){
            $local_order = LocalOrder::where('confirmation_status', 'ERR')->get();
            $promotion =  CustomerPromotion::where(['is_sap_pushed' => 0, 'status' => 'approved'])->get();
            return view('dashboard.index', compact('local_order', 'promotion'));
        }

        if(Auth::user()->role_id != 1){
            $notification = getMyNotifications();
            // dd($notification);
            return view('dashboard.index', compact('notification'));
        }
    	return view('dashboard.index');
    }

    public function ckeditorImageUpload(Request $request){
        if($request->hasFile('upload')) {
            $filePath = $request->file('upload');
            if ($filePath) {
                
                $url = "";
                $status = false;
                $extension = $filePath->getClientOriginalExtension();

                if($filePath->getSize() < 3145728){ //3MB
                    if(!in_array($extension,['jpg','png','jpeg'])){
                        $msg = 'Allow file type: .jpg, .png, .jpeg'; 
                    }else{

                        /*Upload Image*/
                        $name = date("YmdHis") . $filePath->getClientOriginalName();
                        request()->file('upload')->move(public_path() . '/sitebucket/media/', $name);

                        $url = asset('/sitebucket/media/'. $name);
                        if($url != ""){
                            $msg = 'Image uploaded successfully'; 
                            $status = true;
                        }else{
                            $msg = 'Image not uploaded !'; 
                        }
                    }
                }else{
                    $msg = "The image size must be less than 3MB.";
                }

                $CKEditorFuncNum = $request->input('CKEditorFuncNum');
                $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
                   
                @header('Content-type: text/html; charset=utf-8'); 
                echo $response;
            }

        }   
    }
}
