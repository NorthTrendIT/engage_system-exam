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
use App\Support\SAPQuotations;
use App\Models\CustomerProductGroup;
use App\Models\CustomerProductItemLine;
use App\Models\CustomerProductTiresCategory;
use App\Models\Product;
use App\Models\Customer;

use App\Support\SAPCustomer;

class HomeController extends Controller
{
    public function index(Request $request)
    {

        if(Auth::user()->role_id == 1){

            // $promotion = collect([]);
            // $not_pushed_promotion = CustomerPromotionProductDelivery::has('customer_promotion_product')
            //                                     ->where('is_sap_pushed', false)
            //                                     ->with('customer_promotion_product')
            //                                     ->whereHas('customer_promotion_product.customer_promotion', function($q){
            //                                         $q->where('status', 'approved');
            //                                     })
            //                                     ->get();

            // if(!empty($not_pushed_promotion)){

            //     $not_pushed_promotion = array_map( function ( $ar ) {
            //                return $ar['customer_promotion_id'];
            //             }, array_column( $not_pushed_promotion->toArray(), 'customer_promotion_product' ) );

            //     if(!empty($not_pushed_promotion)){
            //         $promotion =  CustomerPromotion::whereIn('id', $not_pushed_promotion)->get();
            //     }
            // }

            $local_order = LocalOrder::where('confirmation_status', 'ERR')->get();
            $company = SapConnection::all();
            // // $promotion =  CustomerPromotion::where(['is_sap_pushed' => 0, 'status' => 'approved'])->get();

            // $sales_order_to_invoice_lead_time = SystemSetting::where('key', 'sales_order_to_invoice_lead_time')->first();
            // $invoice_to_delivery_lead_time = SystemSetting::where('key', 'invoice_to_delivery_lead_time')->first();

            $sales_order_to_invoice_lead_time = '';
            $invoice_to_delivery_lead_time = '';
            // $local_order = [];
            $promotion = '';
            
            return view('dashboard.index', compact('sales_order_to_invoice_lead_time','invoice_to_delivery_lead_time','local_order','promotion', 'company'));
        }

        if(Auth::user()->role_id != 1){
            $notification = getMyNotifications();

            $dashboard = [];
            $data1 = [];
            $data2 = [];
            $data3 = [];
            $orders = [];
            $invoice_lead = [];
            $delivery_lead = [];
            $company = [];
            $local_order = [];

            if(userrole() == 4){
                $customers = Auth::user()->get_multi_customer_details();
                $cust_id = explode(',', Auth::user()->multi_customer_id);

                $local_order = LocalOrder::where('confirmation_status', 'ERR')->whereIn('customer_id', $cust_id)->get();
                // $total_pending_order = Quotation::whereNotNull('u_omsno')->where('cancelled','No')->doesntHave('order')->whereIn('card_code', array_column($customers->toArray(), 'card_code'))->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))->count();

                // $total_on_process_order = Order::whereNotNull('u_omsno')->where('cancelled','No')->doesntHave('invoice')->whereIn('card_code', array_column($customers->toArray(), 'card_code'))->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))->where('document_status', 'bost_Open')->count();

                // $total_for_delivery_order = Quotation::whereNotNull('u_omsno')
                //                     ->where('cancelled','No')
                //                     ->whereIn('card_code', array_column($customers->toArray(), 'card_code'))
                //                     ->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))
                //                     ->whereHas('order',function($q){
                //                          $q->where('cancelled', 'No');
                //                     })
                //                     ->whereHas('order.invoice',function($q){
                //                          $q->where('cancelled', 'No')->where('u_sostat','!=', 'DL')->where('u_sostat','!=', 'CM');
                //                     })                                    
                //                     ->count();

                // $total_delivered_order = Quotation::whereNotNull('u_omsno')
                //                     ->where('cancelled','No')
                //                     ->whereIn('card_code', array_column($customers->toArray(), 'card_code'))
                //                     ->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))
                //                     ->whereHas('order.invoice',function($q){
                //                          $q->where('cancelled', 'No')->where('document_status', 'bost_Open')->where('u_sostat', 'DL');
                //                     })->count();

                // $total_completed_order = Quotation::whereNotNull('u_omsno')
                //                     ->where('cancelled','No')
                //                     ->whereIn('card_code', array_column($customers->toArray(), 'card_code'))
                //                     ->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'))
                //                     ->whereHas('order.invoice',function($q){
                //                          $q->where('cancelled', 'No')->where('u_sostat', 'CM');
                //                     })->count();


                // $total_back_order = OrderItem::where('remaining_open_quantity', '>', 0);
                // $total_back_order->whereHas('order', function($q){
                //     $q->where('document_status', 'bost_Open');
                //     $q->where('cancelled', 'No');
                //     $q->whereIn('u_sostat', ['OP']);
                //     $q->whereNotNull('u_omsno');               
                // });
                // $customers = Auth::user()->get_multi_customer_details();            
                // $total_back_order->whereHas('order', function($q) use ($customers) {
                //     $q->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                //     $q->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
                // });

                // $total_back_order = $total_back_order->sum('remaining_open_quantity');

                // $total = $this->getReportResultData();
                // $t1 = $total->get();
                // $number_of_overdue_invoices = $total_amount_of_overdue_invoices = 0;
                // foreach($t1 as $key=>$val){
                //     $sub = (int)$val->Days - (int)$val->customer->payTerm->number_of_additional_days; 
                //     if($sub > 0){
                //         $number_of_overdue_invoices = $number_of_overdue_invoices +1;
                //         $total_amount_of_overdue_invoices = $total_amount_of_overdue_invoices + $val->doc_total;
                //     }
                // }

                // $dashboard['total_pending_order'] = $total_pending_order;
                // $dashboard['total_on_process_order'] = $total_on_process_order;
                // $dashboard['total_for_delivery_order'] = $total_for_delivery_order;
                // $dashboard['total_delivered_order'] = $total_delivered_order;
                // $dashboard['total_back_order'] = $total_back_order;
                // $dashboard['total_overdue_invoice'] = $number_of_overdue_invoices;
                // $dashboard['total_amount_of_overdue_invoices'] = number_format_value($total_amount_of_overdue_invoices); 
                // $dashboard['total_completed_order'] = $total_completed_order;

                // //Recent Orders
                // $orders = Quotation::whereNotNull('u_omsno');
                // $customers = Auth::user()->get_multi_customer_details();
                // $orders->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                // $orders->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
                // $orders = $orders->orderBy('created_at','DESC')->take(5)->get();

                // //Order to invoice lead time
                // $invoice_lead = Invoice::has('order')->orderby('doc_date', 'desc')->whereNotNull('u_omsno')->orderBy('id','DESC')->take(5);
                // $customers = Auth::user()->get_multi_customer_details();
                // $invoice_lead->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                // $invoice_lead->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
                // $invoice_lead = $invoice_lead->get();

                // //invoice to delivery lead time
                // $delivery_lead = Invoice::has('order')->orderby('doc_date', 'desc')->whereNotNull('u_omsno')->whereNotNull('u_delivery')->orderBy('id','DESC')->take(5);
                // $customers = Auth::user()->get_multi_customer_details();
                // $delivery_lead->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
                // $delivery_lead->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
                // $delivery_lead = $delivery_lead->get();
            }
            if(userrole() == 14){
                $local_order = LocalOrder::where('confirmation_status', 'ERR')->where('sales_specialist_id', Auth::user()->id)->get();
            }
            return view('dashboard.index', compact('notification','dashboard','orders','invoice_lead','delivery_lead','company', 'local_order'));
        }

    	return view('dashboard.index');
    }

    public function getReportResultData(){

        $data = Invoice::selectRaw('*, datediff(now(), doc_date) AS Days');

        $data->where(function($query){
            $query->orwhere(function($q){
                $q->where('cancelled', '!=','No')->where('document_status', '!=', 'Cancelled');
            });

            $query->orwhere(function($q){
                $q->where('u_sostat', '!=','CM')->where('document_status', 'bost_Open');
            });
        });

        $data->where(function($query) {
            $customers = Auth::user()->get_multi_customer_details();
            $query->whereIn('card_code', array_column($customers->toArray(), 'card_code'));
            $query->whereIn('sap_connection_id', array_column($customers->toArray(), 'sap_connection_id'));
        });
        return $data;
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

    public function comments(Request $request){
        ini_set('memory_limit', '10240M');
        ini_set('max_execution_time', 18000);
        $sap_connections = SapConnection::all();
        if(!empty($sap_connections)){
            foreach($sap_connections as $connections){
                $sap = new SAPQuotations($connections->db_name, $connections->user_name , $connections->password, $connections->id);

                $quotation = Quotation::where('sap_connection_id',$connections->id)->orderBy('id','DESC')->get();
                foreach ($quotation as $key => $value) {
                   $sap->addComments(@$value->doc_entry);
                }                
            }
        }
    }

    public function paymentTerms(Request $request){
        ini_set('memory_limit', '10240M');
        ini_set('max_execution_time', 18000);
        $sap_connections = SapConnection::all();
        if(!empty($sap_connections)){
            foreach($sap_connections as $connections){
                $sap = new SAPCustomer($connections->db_name, $connections->user_name , $connections->password, $connections->id);
                   $sap->getPaymentTermTypeData();
                                
            }
        }
    }

    public function customerPayment(Request $request){
        ini_set('memory_limit', '10240M');
        ini_set('max_execution_time', 18000);
            
        $sap = new SAPCustomer('APBW', 'API' , 'AP@46amb', '1', '');
        $sap->addSpecificCustomerData('C14135');
                
        return "complete"; 
    }
    
}
