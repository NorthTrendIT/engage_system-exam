<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\CustomersSalesSpecialist;
use App\Models\User;
use App\Models\Customer;
use App\Models\ProductItemLine;
use App\Models\ProductGroup;
use App\Models\ProductTiresCategory;
use App\Models\CustomerProductItemLine;
use App\Models\CustomerProductGroup;
use App\Models\CustomerProductTiresCategory;
use Log;
use App\Models\salesAssignment;
use DB;
use App\Models\SapConnection;
use App\Models\CustomerGroup;

class CustomerSalesSpecialistAssignImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        if(count($collection)){

            foreach($collection as $key=>$data){
                if($key > 0){

                    $assignment = DB::table('sales_assignment')->insertGetId(
                                        array(
                                            'assignment_name' => $data[0], 
                                        )
                                    );
                    
                    $business_unit = $data[1];
                    $real_sap_connection_id = SapConnection::where('company_name',$data[1])->first();                                       
                    
                    if($real_sap_connection_id){
                        $customers = '';
                        $sales_agent = '';
                        $brands = '';
                        $lines = '';
                        $product_lines = '';
                        $categories = '';
                        $groups = [];

                        $group_code = explode(",", $data[2]);
                        foreach ($group_code as $key => $value) {
                            $gr_code = CustomerGroup::where('sap_connection_id',$real_sap_connection_id->id)->where('name',$value)->select('code')->first();
                            array_push($groups, $gr_code->code);
                        }

                        if(!is_null($data[7])){
                            $card_code = explode(",", $data[7]); 
                            $customers = DB::table('customers')->whereIn('card_code',$card_code)
                                ->where('sap_connection_id',$real_sap_connection_id->id)
                                ->pluck('id')->toArray();
                        }
                        if(!empty($customers)){      
                            $sap_connection_id = $real_sap_connection_id;
                            if($sap_connection_id->id == 5){ //Solid Trend
                                $sap_connection_id->id = 1;
                            } 
                                                   
                            if(!is_null($data[3])){
                                $emails = explode(",", $data[3]);
                                // Add SS
                                $where = array(
                                            //'sap_connection_id' => $sap_connection_id->id,
                                            //'role_id' => 2,
                                        );

                                $sales_agent = User::where($where)->whereIn('email',$emails)->pluck('id')->toArray();
                            } 

                            if(!is_null($data[4])){
                                $brand = explode(",", $data[4]);
                                // Add SS
                                $where = array(
                                            'sap_connection_id' => $sap_connection_id->id,
                                        );

                                $brands = ProductGroup::where($where)->whereIn('group_name',$brand)->pluck('id')->toArray();
                            }

                            if(!is_null($data[5])){
                                $line = explode(",", $data[5]);
                                // Add SS
                                $where = array(
                                            'sap_connection_id' => $sap_connection_id->id,
                                        );

                                // $lines = ProductItemLine::where($where)->whereIn('u_item_line',$line)->pluck('id')->toArray();
                                $data = ProductItemLine::where('sap_connection_id',$sap_connection_id->id)
                                                        ->orderby('u_item_line','asc')
                                                        ->select('id','u_item_line')
                                                        ->limit(50);

                                $data = $data->get();
                                $product_lines = [];
                                
                                foreach($data as $value){
                                    $item_line = @$value->u_item_line_sap_value->value ?? @$value->u_item_line;
                                    if(in_array($item_line, $line)){
                                        $product_lines[] = array(
                                            "id" => $value->id,
                                            "text" => $item_line
                                        );

                                    }
                                }
                            }

                            if(!is_null($data[6])){
                                $category = explode(",", $data[6]);
                                // Add SS
                                $where = array(
                                            'sap_connection_id' => $sap_connection_id->id,
                                        );

                                $categories = ProductTiresCategory::where($where)->whereIn('u_tires',$category)->pluck('id')->toArray();
                            }  
                            
                            CustomersSalesSpecialist::where('assignment_id', $assignment)->delete();
                            CustomerProductGroup::where('assignment_id', $assignment)->delete();
                            CustomerProductItemLine::where('assignment_id', $assignment)->delete();
                            CustomerProductTiresCategory::where('assignment_id', $assignment)->delete();
                           
                            foreach($customers as $key => $customer){
                                if(!empty($sales_agent)){
                                    foreach($sales_agent as $value){
                                        $ss = DB::table('customers_sales_specialists')->insert(
                                                     array(
                                                            'assignment_id'     =>   $assignment, 
                                                            'customer_id'   =>   $customer,
                                                            'ss_id'   =>   $value,
                                                     )
                                                );
                                    }
                                }
                                
                                if(!empty($brands)){
                                    foreach($brands as $value){
                                        $ss =DB::table('customer_product_groups')->insert(
                                                     array(
                                                            'assignment_id'     =>   $assignment, 
                                                            'customer_id'   =>   $customer,
                                                            'product_group_id'   =>   $value,
                                                     )
                                                );
                                    }
                                }
                                
                                // if(!empty($lines)){
                                //     Log::info(print_r('lines',true));
                                //     foreach($lines as $value){
                                        
                                //         $ss =DB::table('customer_product_item_lines')->insert(
                                //                     array(
                                //                         'assignment_id'     =>   $assignment, 
                                //                         'customer_id'   =>   $customer,
                                //                         'product_item_line_id'   =>   $value,
                                //                     )
                                //                 );
                                //     }
                                // }

                                if(!empty($product_lines)){
                                    foreach($product_lines as $value){
                                        
                                        $ss =DB::table('customer_product_item_lines')->insert(
                                                    array(
                                                        'assignment_id'     =>   $assignment, 
                                                        'customer_id'   =>   $customer,
                                                        'product_item_line_id'   =>   $value['id'],
                                                    )
                                                );
                                    }
                                }
                                
                                if(!empty($categories)){
                                    foreach($categories as $value){
                                        
                                        $ss =DB::table('customer_product_tires_categories')->insert(
                                                    array(
                                                        'assignment_id'     =>   $assignment, 
                                                        'customer_id'   =>   $customer,
                                                        'product_tires_category_id'   =>   $value,
                                                    )
                                                );
                                    }
                                }
                            } 
                        }
                    }
                }
            }

        }
    }


}
