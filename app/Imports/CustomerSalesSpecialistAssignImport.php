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


class CustomerSalesSpecialistAssignImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        if(count($collection)){

            foreach($collection as $data){

                $u_card_code = @$data[6];

                if($u_card_code){

                    $customer = Customer::where('u_card_code', $u_card_code)->whereNotNull('sap_connection_id')->first();

                    if(!is_null($customer)){
                        $sap_connection_id = $customer->sap_connection_id;

                        // Add SS
                        $where = array(
                                    'sap_connection_id' => $sap_connection_id,
                                    'role_id' => 2,
                                    'sales_employee_code' => @$data[1],
                                );

                        $ss = User::where($where)->first();

                        if(!is_null($ss)){
                            CustomersSalesSpecialist::updateOrCreate(
                                                        [
                                                            'customer_id' => $customer->id,
                                                            'ss_id' => $ss->id,
                                                        ],
                                                        [
                                                            'customer_id' => $customer->id,
                                                            'ss_id' => $ss->id,
                                                        ]
                                                    );
                        }else{
                            continue;
                        }


                        // Add product group
                        $where = array(
                                    'sap_connection_id' => $sap_connection_id,
                                    'group_name' => @$data[2],
                                );

                        $product_group = ProductGroup::where($where)->first();
                        if(!is_null($product_group)){
                            CustomerProductGroup::updateOrCreate(
                                                        [
                                                            'customer_id' => $customer->id,
                                                            'product_group_id' => $product_group->id,
                                                        ],
                                                        [
                                                            'customer_id' => $customer->id,
                                                            'product_group_id' => $product_group->id,
                                                        ]
                                                    );
                        }


                        // Add product item line
                        $where = array(
                                    'sap_connection_id' => $sap_connection_id,
                                    'u_item_line' => @$data[3],
                                );

                        $product_item_line = ProductItemLine::where($where)->first();
                        if(!is_null($product_item_line)){
                            CustomerProductItemLine::updateOrCreate(
                                                        [
                                                            'customer_id' => $customer->id,
                                                            'product_item_line_id' => $product_item_line->id,
                                                        ],
                                                        [
                                                            'customer_id' => $customer->id,
                                                            'product_item_line_id' => $product_item_line->id,
                                                        ]
                                                    );
                        }


                        // Add product tires category
                        $where = array(
                                    'sap_connection_id' => $sap_connection_id,
                                    'u_tires' => @$data[4],
                                );

                        $product_tires_category = ProductTiresCategory::where($where)->first();
                        if(!is_null($product_tires_category)){
                            CustomerProductTiresCategory::updateOrCreate(
                                                        [
                                                            'customer_id' => $customer->id,
                                                            'product_tires_category_id' => $product_tires_category->id,
                                                        ],
                                                        [
                                                            'customer_id' => $customer->id,
                                                            'product_tires_category_id' => $product_tires_category->id,
                                                        ]
                                                    );
                        }


                    }
                }
            }

        }
    }


}
