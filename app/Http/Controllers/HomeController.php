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
use App\Models\SystemSetting;
use App\Models\CustomerPromotionProductDelivery;

use Auth;

class HomeController extends Controller
{
    public function index()
    {

        if(Auth::user()->role_id == 1){

            $promotion = collect([]);
            $not_pushed_promotion = CustomerPromotionProductDelivery::has('customer_promotion_product')
                                                ->where('is_sap_pushed', false)
                                                ->with('customer_promotion_product')
                                                ->whereHas('customer_promotion_product.customer_promotion', function($q){
                                                    $q->where('status', 'approved');
                                                })
                                                ->get();

            if(!empty($not_pushed_promotion)){

                $not_pushed_promotion = array_map( function ( $ar ) {
                           return $ar['customer_promotion_id'];
                        }, array_column( $not_pushed_promotion->toArray(), 'customer_promotion_product' ) );

                if(!empty($not_pushed_promotion)){
                    $promotion =  CustomerPromotion::whereIn('id', $not_pushed_promotion)->get();
                }
            }

            $local_order = LocalOrder::where('confirmation_status', 'ERR')->get();
            // $promotion =  CustomerPromotion::where(['is_sap_pushed' => 0, 'status' => 'approved'])->get();

            $sales_order_to_invoice_lead_time = SystemSetting::where('key', 'sales_order_to_invoice_lead_time')->first();
            $invoice_to_delivery_lead_time = SystemSetting::where('key', 'invoice_to_delivery_lead_time')->first();

            return view('dashboard.index', compact('sales_order_to_invoice_lead_time','invoice_to_delivery_lead_time','local_order','promotion'));
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

            $so_to_il_days_array = $i_to_dl_days_array = [];
            foreach($data as $value){

                //Sales Order to Invoice Lead Time
                $endDate = $value->created_at;
                $startDate = $value->order->created_at;

                $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
                if($days >= 0){
                    array_push($so_to_il_days_array, $days);
                }

                // Invoice to Delivery Lead Time
                $startDate = $value->created_at;
                $endDate = $value->u_delivery;

                if(!is_null($value->u_delivery)){
                    $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
                    if($days >= 0){
                        array_push($i_to_dl_days_array, $days);
                    }
                }
            }
            $sales_order_to_invoice_lead_time = round(array_sum($so_to_il_days_array) / count($so_to_il_days_array));
            $invoice_to_delivery_lead_time = round(array_sum($i_to_dl_days_array) / count($i_to_dl_days_array));


            SystemSetting::updateOrCreate(
                                        [
                                            'key' => 'sales_order_to_invoice_lead_time',
                                        ],
                                        [
                                            'key' => 'sales_order_to_invoice_lead_time',
                                            'value' => $sales_order_to_invoice_lead_time,
                                        ],
                                    );

            SystemSetting::updateOrCreate(
                                        [
                                            'key' => 'invoice_to_delivery_lead_time',
                                        ],
                                        [
                                            'key' => 'invoice_to_delivery_lead_time',
                                            'value' => $invoice_to_delivery_lead_time,
                                        ],
                                    );

            $data = compact('sales_order_to_invoice_lead_time','invoice_to_delivery_lead_time');
            return $response = [ 'status' => true, 'message' => "", 'data' => $data ];
            
        } catch (\Exception $e) {
            return $response = [ 'status' => false, 'message' => "Something went wrong !"];
        }
    }
}
