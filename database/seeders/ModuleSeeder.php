<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Schema::disableForeignKeyConstraints();
        Module::truncate();
        \Schema::enableForeignKeyConstraints();

        $usermgt = Module::create(
                            array(
                                'title' => 'User Management',
                                'slug' => 'user-management',
                                'parent_id' => null,
                            )
                        );

        $customermgt = Module::create(
                            array(
                                'title' => 'Customer Management',
                                'slug' => 'customer-management',
                                'parent_id' => null,
                            )
                        );
        $productmgt = Module::create(
                            array(
                                'title' => 'Product Management',
                                'slug' => 'product-management',
                                'parent_id' => null,
                            )
                        );

        $role = Module::create(
                            array(
                                'title' => 'Role',
                                'slug' => 'role',
                                'parent_id' => null,
                            )
                        );

        $user = Module::create(
                            array(
                                'title' => 'User',
                                'slug' => 'user',
                                'parent_id' => null,
                            )
                        );

        $location = Module::create(
                            array(
                                'title' => 'Location',
                                'slug' => 'location',
                                'parent_id' => null,
                            )
                        );

        $department = Module::create(
                            array(
                                'title' => 'Department',
                                'slug' => 'department',
                                'parent_id' => null,
                            )
                        );

        $customer = Module::create(
                            array(
                                'title' => 'Customer',
                                'slug' => 'customer',
                                'parent_id' => null,
                            )
                        );

        $class = Module::create(
                            array(
                                'title' => 'Class',
                                'slug' => 'class',
                                'parent_id' => null,
                            )
                        );

        $customer_group = Module::create(
                            array(
                                'title' => 'Customer Group',
                                'slug' => 'customer-group',
                                'parent_id' => null,
                            )
                        );

        $order = Module::create(
                            array(
                                'title' => 'Order',
                                'slug' => 'order',
                                'parent_id' => null,
                            )
                        );

        $invoice = Module::create(
                            array(
                                'title' => 'Invoice',
                                'slug' => 'invoice',
                                'parent_id' => null,
                            )
                        );

        $product = Module::create(
                            array(
                                'title' => 'Product',
                                'slug' => 'product',
                                'parent_id' => null,
                            )
                        );

        $product_list = Module::create(
                            array(
                                'title' => 'Product List',
                                'slug' => 'product-list',
                                'parent_id' => null,
                            )
                        );

        $my_promotions = Module::create(
                            array(
                                'title' => 'My Promotions',
                                'slug' => 'my-promotions',
                                'parent_id' => null,
                            )
                        );

        $quotation = Module::create(
                            array(
                                'title' => 'Quotation',
                                'slug' => 'quotation',
                                'parent_id' => null,
                            )
                        );

        $product_group = Module::create(
                            array(
                                'title' => 'Product Brand',
                                'slug' => 'product-group',
                                'parent_id' => null,
                            )
                        );

        $warranty = Module::create(
                            array(
                                'title' => 'Warranty',
                                'slug' => 'warranty',
                                'parent_id' => null,
                            )
                        );

        // $promotion = Module::create(
        //                     array(
        //                         'title' => 'Promotion',
        //                         'slug' => 'promotion',
        //                         'parent_id' => null,
        //                     )
        //                 );

        $customer_delivery_schedule = Module::create(
                            array(
                                'title' => 'Customer Delivery Schedule',
                                'slug' => 'customer-delivery-schedule',
                                'parent_id' => null,
                            )
                        );

        $data = array(

                    array(
                        'title' => 'Add',
                        'slug' => 'add-role',
                        'parent_id' => $role->id,
                    ),
                    array(
                        'title' => 'Edit',
                        'slug' => 'edit-role',
                        'parent_id' => $role->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-role',
                        'parent_id' => $role->id,
                    ),
                    array(
                        'title' => 'Delete',
                        'slug' => 'delete-role',
                        'parent_id' => $role->id,
                    ),

        			array(
                        'title' => 'Add',
                        'slug' => 'add-user',
                        'parent_id' => $user->id,
                    ),
                    array(
                        'title' => 'Edit',
                        'slug' => 'edit-user',
                        'parent_id' => $user->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-user',
                        'parent_id' => $user->id,
                    ),
                    array(
                        'title' => 'Delete',
                        'slug' => 'delete-user',
                        'parent_id' => $user->id,
                    ),


                    array(
                        'title' => 'Add',
                        'slug' => 'add-department',
                        'parent_id' => $department->id,
                    ),
                    array(
                        'title' => 'Edit',
                        'slug' => 'edit-department',
                        'parent_id' => $department->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-department',
                        'parent_id' => $department->id,
                    ),
                    array(
                        'title' => 'Delete',
                        'slug' => 'delete-department',
                        'parent_id' => $department->id,
                    ),

                    array(
                        'title' => 'Add',
                        'slug' => 'add-location',
                        'parent_id' => $location->id,
                    ),
                    array(
                        'title' => 'Edit',
                        'slug' => 'edit-location',
                        'parent_id' => $location->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-location',
                        'parent_id' => $location->id,
                    ),
                    array(
                        'title' => 'Delete',
                        'slug' => 'delete-location',
                        'parent_id' => $location->id,
                    ),

                    array(
                        'title' => 'View',
                        'slug' => 'view-class',
                        'parent_id' => $class->id,
                    ),

                    array(
                        'title' => 'Add/Sync',
                        'slug' => 'add-customer',
                        'parent_id' => $customer->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-customer',
                        'parent_id' => $customer->id,
                    ),

                    array(
                        'title' => 'Add/Sync',
                        'slug' => 'add-customer-group',
                        'parent_id' => $customer_group->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-customer-group',
                        'parent_id' => $customer_group->id,
                    ),

                    array(
                        'title' => 'Add/Sync',
                        'slug' => 'add-order',
                        'parent_id' => $order->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-order',
                        'parent_id' => $order->id,
                    ),

                    array(
                        'title' => 'Add/Sync',
                        'slug' => 'add-invoice',
                        'parent_id' => $invoice->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-invoice',
                        'parent_id' => $invoice->id,
                    ),

                    array(
                        'title' => 'Add/Sync',
                        'slug' => 'add-product',
                        'parent_id' => $product->id,
                    ),
                    array(
                        'title' => 'Edit',
                        'slug' => 'edit-product',
                        'parent_id' => $product->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-product',
                        'parent_id' => $product->id,
                    ),

                    array(
                        'title' => 'View',
                        'slug' => 'view-product-list',
                        'parent_id' => $product_list->id,
                    ),

                    array(
                        'title' => 'View',
                        'slug' => 'view-my-promotions',
                        'parent_id' => $my_promotions->id,
                    ),

                    array(
                        'title' => 'Add/Sync',
                        'slug' => 'add-quotation',
                        'parent_id' => $quotation->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-quotation',
                        'parent_id' => $quotation->id,
                    ),

                    // array(
                    //     'title' => 'Add',
                    //     'slug' => 'add-promotion',
                    //     'parent_id' => $promotion->id,
                    // ),
                    // array(
                    //     'title' => 'Edit',
                    //     'slug' => 'edit-promotion',
                    //     'parent_id' => $promotion->id,
                    // ),
                    // array(
                    //     'title' => 'View',
                    //     'slug' => 'view-promotion',
                    //     'parent_id' => $promotion->id,
                    // ),
                    // array(
                    //     'title' => 'Delete',
                    //     'slug' => 'delete-promotion',
                    //     'parent_id' => $promotion->id,
                    // ),


                    array(
                        'title' => 'Add/Sync',
                        'slug' => 'add-product-group',
                        'parent_id' => $product_group->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-product-group',
                        'parent_id' => $product_group->id,
                    ),


                    array(
                        'title' => 'Add',
                        'slug' => 'add-warranty',
                        'parent_id' => $warranty->id,
                    ),
                    array(
                        'title' => 'Edit',
                        'slug' => 'edit-warranty',
                        'parent_id' => $warranty->id,
                    ),
                    array(
                        'title' => 'View',
                        'slug' => 'view-warranty',
                        'parent_id' => $warranty->id,
                    ),
                    array(
                        'title' => 'Delete',
                        'slug' => 'delete-warranty',
                        'parent_id' => $warranty->id,
                    ),


                    array(
                        'title' => 'View All',
                        'slug' => 'view-all-customer-delivery-schedule',
                        'parent_id' => $customer_delivery_schedule->id,
                    ),
        		);
        Module::insert($data);

        $reports = Module::create(
            array(
                'title' => 'Reports',
                'slug' => 'reports',
                'parent_id' => null,
            )
        );

        $data = array(
            array(
                'title' => 'Promotion Report',
                'slug' => 'promotion-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Sales Report',
                'slug' => 'sales-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Sales Order Report ',
                'slug' => 'sales-order-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Overdue Sales Invoice Report ',
                'slug' => 'overdue-sales-invoice-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Back Order Report',
                'slug' => 'back-order-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Product Sales Report',
                'slug' => 'product-sales-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Product Report',
                'slug' => 'product-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Credit Memo Report',
                'slug' => 'credit-memo-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Debit Memo Report',
                'slug' => 'debit-memo-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Return Order Report',
                'slug' => 'return-order-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Sales Order to Invoice Lead Time',
                'slug' => 'sales-order-to-invoice-lead-time-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Invoice to Delivery Lead Time',
                'slug' => 'invoice-to-delivery-lead-time-report',
                'parent_id' => $reports->id,
            ),
            array(
                'title' => 'Invoice Status Report',
                'slug' => 'invoice-status-report',
                'parent_id' => $reports->id,
            ),
        );
        Module::insert($data);

        $salesSpecialist = Module::create(
            array(
                'title' => 'Sales Specialist Assignment',
                'slug' => 'sales-specialist-assignment',
                'parent_id' => null,
            )
        );

        $insertSub = array(
            array(
                'title' => 'Add',
                'slug' => 'add-sales-specialist-assignment',
                'parent_id' => $salesSpecialist->id,
            ),
            array(
                'title' => 'Edit',
                'slug' => 'edit-sales-specialist-assignment',
                'parent_id' => $salesSpecialist->id,
            ),
            array(
                'title' => 'View',
                'slug' => 'view-sales-specialist-assignment',
                'parent_id' => $salesSpecialist->id,
            ),
            array(
                'title' => 'Delete',
                'slug' => 'delete-sales-specialist-assignment',
                'parent_id' => $salesSpecialist->id,
            ),
            
        );

        Module::insert($insertSub);

        $deliverySchedule = Module::create(
            array(
                'title' => 'Delivery Schedule',
                'slug' => 'delivery-schedule',
                'parent_id' => null,
            )
        );

        $insertSub1 = array(
            array(
                'title' => 'Add',
                'slug' => 'add-schedule',
                'parent_id' => $deliverySchedule->id,
            ),
            array(
                'title' => 'Edit',
                'slug' => 'edit-schedule',
                'parent_id' => $deliverySchedule->id,
            ),
            array(
                'title' => 'View',
                'slug' => 'view-schedule',
                'parent_id' => $deliverySchedule->id,
            ),
            array(
                'title' => 'Delete',
                'slug' => 'delete-schedule',
                'parent_id' => $deliverySchedule->id,
            ),

            array(
                'title' => 'View All',
                'slug' => 'view-all-schedule',
                'parent_id' => $deliverySchedule->id,
            ),
            
        );

        Module::insert($insertSub1);

        $activity_log = Module::create(
            array(
                'title' => 'Activity Log',
                'slug' => 'activity-log',
                'parent_id' => null,
            )
        );

        $activity_log_access = array(
            array(
                'title' => 'View',
                'slug' => 'view-activity-log',
                'parent_id' => $activity_log->id,
            ),            
        );

        Module::insert($activity_log_access);


        $recommend_product = Module::create(
            array(
                'title' => 'Recommended Products',
                'slug' => 'recommended-products',
                'parent_id' => null,
            )
        );

        $recommended_prods = array(
            array(
                'title' => 'Add',
                'slug' => 'add-recommended-product',
                'parent_id' => $recommend_product->id,
            ),
            array(
                'title' => 'Edit',
                'slug' => 'edit-recommended-product',
                'parent_id' => $recommend_product->id,
            ),
            array(
                'title' => 'View',
                'slug' => 'view-recommended-product',
                'parent_id' => $recommend_product->id,
            ),
        );

        Module::insert($recommended_prods);


        $customer_target = Module::create(
            array(
                'title' => 'Customer Target',
                'slug' => 'customer-target',
                'parent_id' => null,
            )
        );

        $customer_target_module = array(
            array(
                'title' => 'Add',
                'slug' => 'add-customer-target',
                'parent_id' => $customer_target->id,
            ),
            array(
                'title' => 'Edit',
                'slug' => 'edit-customer-target',
                'parent_id' => $customer_target->id,
            ),
            array(
                'title' => 'View',
                'slug' => 'view-customer-target',
                'parent_id' => $customer_target->id,
            ),
        );

        Module::insert($customer_target_module);



        $product_benefits = Module::create(
            array(
                'title' => 'Product Benefits',
                'slug' => 'product-benefits',
                'parent_id' => null,
            )
        );

        $product_benefits_module = array(
            array(
                'title' => 'Add',
                'slug' => 'add-product-benefits',
                'parent_id' => $product_benefits->id,
            ),
            array(
                'title' => 'Edit',
                'slug' => 'edit-product-benefits',
                'parent_id' => $product_benefits->id,
            ),
            array(
                'title' => 'View',
                'slug' => 'view-product-benefits',
                'parent_id' => $product_benefits->id,
            ),
        );

        Module::insert($product_benefits_module);



        $maintenance = Module::create(
            array(
                'title' => 'Maintenance',
                'slug' => 'maintenance',
                'parent_id' => null,
            )
        );

        $user_mntnce = Module::create(
            array(
                'title' => 'User',
                'slug' => 'user-maintenance',
                'parent_id' => null,
            )
        );

        $user_mntnce_module = array(
            array(
                'title' => 'Edit',
                'slug' => 'edit-user-maintenance',
                'parent_id' => $user_mntnce->id,
            ),
            array(
                'title' => 'Delete',
                'slug' => 'delete-user-maintenance',
                'parent_id' => $user_mntnce->id,
            ),
        );

        Module::insert($user_mntnce_module);




    }
}
