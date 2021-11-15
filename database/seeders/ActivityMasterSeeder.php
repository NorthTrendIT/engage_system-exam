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
        				'name' => 'Login'
                    ),
                    array(
                        'name' => 'Place Order'
                    ),
        		);
        ActivityMaster::insert($data);
    }
}
