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

        $data = array(
        			array(
        				'title' => 'Role',
        				'slug' => 'role',
        				'model_name' => 'App\Models\Role',
        			),
        			array(
        				'title' => 'User',
        				'slug' => 'user',
        				'model_name' => 'App\Models\User',
        			),
                    array(
                        'title' => 'Customer',
                        'slug' => 'customer',
                        'model_name' => 'App\Models\Customer',
                    ),
                    array(
                        'title' => 'Sales Person',
                        'slug' => 'sales-person',
                        'model_name' => 'App\Models\SalesPerson',
                    ),
                    array(
                        'title' => 'Product',
                        'slug' => 'product',
                        'model_name' => 'App\Models\Product',
                    ),
                    array(
                        'title' => 'Invoice',
                        'slug' => 'invoice',
                        'model_name' => 'App\Models\Invoice',
                    ),
                    array(
                        'title' => 'Order',
                        'slug' => 'order',
                        'model_name' => 'App\Models\Order',
                    ),
        		);
        Module::insert($data);
    }
}
