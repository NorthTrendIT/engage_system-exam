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

        // $promotion = Module::create(
        //                     array(
        //                         'title' => 'Promotion',
        //                         'slug' => 'promotion',
        //                         'parent_id' => null,
        //                     )
        //                 );

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

        		);
        Module::insert($data);
    }
}
