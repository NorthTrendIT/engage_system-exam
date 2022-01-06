<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityMaster;

class ActivityMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Schema::disableForeignKeyConstraints();
        ActivityMaster::truncate();
        \Schema::enableForeignKeyConstraints();

        $data = array(
        			array(
                        'id' => 1,
        				'name' => 'Login'
                    ),
                    array(
                        'id' => 2,
                        'name' => 'Logout'
                    ),
                    array(
                        'id' => 3,
                        'name' => 'User Created'
                    ),
                    array(
                        'id' => 4,
                        'name' => 'User Updated'
                    ),
                    array(
                        'id' => 5,
                        'name' => 'User Deleted'
                    ),
                    array(
                        'id' => 6,
                        'name' => 'Role Created'
                    ),
                    array(
                        'id' => 7,
                        'name' => 'Role Updated'
                    ),
                    array(
                        'id' => 8,
                        'name' => 'Role Deleted'
                    ),
                    array(
                        'id' => 9,
                        'name' => 'Department Created'
                    ),
                    array(
                        'id' => 10,
                        'name' => 'Department Updated'
                    ),
                    array(
                        'id' => 11,
                        'name' => 'Department Deleted'
                    ),
                    array(
                        'id' => 12,
                        'name' => 'Location Created'
                    ),
                    array(
                        'id' => 13,
                        'name' => 'Location Updated'
                    ),
                    array(
                        'id' => 14,
                        'name' => 'Location Deleted'
                    ),
                    array(
                        'id' => 15,
                        'name' => 'Sync Customer Data'
                    ),
                    array(
                        'id' => 16,
                        'name' => 'Sync Orders Data'
                    ),
                    array(
                        'id' => 17,
                        'name' => 'Sync Invoices Data'
                    ),
                    array(
                        'id' => 18,
                        'name' => 'Sync Products Data'
                    ),
                    array(
                        'id' => 19,
                        'name' => 'Promotion Created'
                    ),
                    array(
                        'id' => 20,
                        'name' => 'Promotion Updated'
                    ),
                    array(
                        'id' => 21,
                        'name' => 'Promotion Deleted'
                    ),
                    array(
                        'id' => 22,
                        'name' => 'Sync Territories Data'
                    ),
                    array(
                        'id' => 23,
                        'name' => 'Promotion Type Created'
                    ),
                    array(
                        'id' => 24,
                        'name' => 'Promotion Type Updated'
                    ),
                    array(
                        'id' => 25,
                        'name' => 'Promotion Type Deleted'
                    ),
                    array(
                        'id' => 26,
                        'name' => 'My Promotion View'
                    ),
                    array(
                        'id' => 27,
                        'name' => 'My Promotion Claimed'
                    ),
                    array(
                        'id' => 28,
                        'name' => 'My Promotion Status Update'
                    ),
                    array(
                        'id' => 29,
                        'name' => 'My Promotion Interest'
                    ),
                    array(
                        'id' => 30,
                        'name' => 'My Promotion Claimed Details Update'
                    ),
                    array(
                        'id' => 31,
                        'name' => 'Sync Customer Group Data'
                    ),
                    array(
                        'id' => 32,
                        'name' => 'Sync Product Brands Data'
                    ),
                    array(
                        'id' => 33,
                        'name' => 'Sync Sales Specialist Data'
                    ),
                    array(
                        'id' => 34,
                        'name' => 'Sync Orders Data'
                    ),
                    array(
                        'id' => 35,
                        'name' => 'Sync Quotations Data'
                    ),
                    array(
                        'id' => 36,
                        'name' => 'Sync Invoices Data'
                    ),

        		);
        ActivityMaster::insert($data);
    }
}
