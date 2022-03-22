<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalOrder;
use App\Models\CustomerPromotion;

use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SapConnection;

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


    public function getReportData(Request $request){

        try {
            $data = Invoice::has('order')->get();

            $days_array = [];
            foreach($data as $value){
                $endDate = $value->created_at;
                $startDate = $value->order->created_at;

                $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
                if($days >= 0){
                  array_push($days_array, $days);
                }
            }
            $sales_order_to_invoice_lead_time = round(array_sum($days_array) / count($days_array));


            $data = compact('sales_order_to_invoice_lead_time');
            return $response = [ 'status' => true, 'message' => "", 'data' => $data ];
            
        } catch (\Exception $e) {
            return $response = [ 'status' => false, 'message' => "Something went wrong !"];
        }
    }
}
