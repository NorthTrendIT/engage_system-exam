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
use App\Models\InvoiceItem;
use App\Support\SAPCustomer;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $dashboard = [];
        $data1 = [];
        $data2 = [];
        $data3 = [];
        $orders = [];
        $invoice_lead = [];
        $delivery_lead = [];
        $company = [];
        $notification = [];
        $local_order = [];
        $sales_order_to_invoice_lead_time = '';
        $invoice_to_delivery_lead_time = '';
        $promotion = '';
        $default_customer_top_products = [];
        $due_invoices = [];
        $quotation_date = ['startDate'=> date('m/d/Y'), 'endDate' => date('m/d/Y'), 'year' => date('Y')];

        $notification = getMyNotifications();
        $customerQuot = Customer::with(['user','sap_connection:id,company_name,db_name', 'quotation'])
                              ->whereNotIn('sap_connection_id', [4])
                              ->where('is_active', true)
                              ->has('user')
                              ->whereHas('quotation', function($q){
                                $q->where('cancelled', 'No');
                              });

        if(Auth::user()->role_id == 1){ //admin
            $local_order = LocalOrder::where('confirmation_status', 'ERR')->get();
            $company = SapConnection::all();
            $default_customer_top_products = $customerQuot->orderBy('card_name','asc')->first();
        }


        if(in_array(userrole(),[14])){ //agent
            $local_order = LocalOrder::where('confirmation_status', 'ERR')->where('sales_specialist_id', Auth::user()->id)->get();

            $customerQuot->whereHas('sales_specialist.sales_person', function($q) {
                return $q->where('ss_id', Auth::id());
            });

            $default_customer_top_products = $customerQuot->orderBy('card_name','asc')->first();
            $customer_ids = [@$default_customer_top_products['id']];
        } 

        if(userrole() == 4){ //customer
            $customers = Auth::user()->get_multi_customer_details();
            $cust_id = explode(',', Auth::user()->multi_customer_id);
            $customer_ids = $cust_id;

            $customerQuot->whereIn('id', $customer_ids);
            $default_customer_top_products = $customerQuot->orderBy('card_name','asc')->first();
            $local_order = LocalOrder::where('confirmation_status', 'ERR')->whereIn('customer_id', $cust_id)->get();
        }

        $quotation_brand    = null;
        $quotation_category = null;
        if((@$default_customer_top_products->quotation !== null)){
            $latest_quotation = $default_customer_top_products->quotation->last();
            $result_quot_date = $latest_quotation->doc_date; 

            $quotation_brand     = $latest_quotation->items->first()->product1->group;
            $quotation_category  = $latest_quotation->items->first()->product1->product_tires_category;

        }else{
            $result_quot_date =  date('Y-m-d');
        }
        
        $quot_date_parse = Carbon::parse($result_quot_date);
        $quotation_date = ['startDate' => $quot_date_parse->firstOfMonth()->format('m/d/Y'),
                           'endDate'   => $quot_date_parse->endOfMonth()->format('m/d/Y'),
                           'year'      => $quot_date_parse->format('Y')
                          ];

        return view('dashboard.index', compact('notification','dashboard','orders','invoice_lead','delivery_lead','company', 'local_order',
                                               'sales_order_to_invoice_lead_time','invoice_to_delivery_lead_time','local_order','promotion', 
                                               'company', 'default_customer_top_products', 'due_invoices', 'quotation_date', 'quotation_brand', 'quotation_category'
                                              ));
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

    public function getDueInvoicesAjax(Request $request){
        $customer_ids = $request->input('customer_id');

        return $this->getDueInvoices($customer_ids);
    }

    public function getDueInvoices($customer_ids){

        $w_due =  InvoiceItem::whereHas('invoice', function($q) use ($customer_ids){
                        $q->where('cancelled', 'No')
                        ->where('document_status', 'bost_Open')
                        ->whereDate('doc_due_date', '>=' , date("Y-m-d"));

                        $q->whereHas('customer', function($q) use ($customer_ids){
                            $q->whereIn('id', $customer_ids);
                        });
                    })->count();

        $o_due =  InvoiceItem::whereHas('invoice', function($q) use ($customer_ids){
                        $q->where('cancelled', 'No')
                        ->where('document_status', 'bost_Open')
                        ->whereDate('doc_due_date', '<' , date("Y-m-d"));

                        $q->whereHas('customer', function($q) use ($customer_ids){ 
                            $q->whereIn('id', $customer_ids);
                        });
                    })->count();
        $total = $w_due + $o_due;
        return [ 'within_due' => number_format($w_due, 2), 'over_due' => number_format($o_due, 2), 'total_due' => number_format($total, 2) ];
    }
    
}
