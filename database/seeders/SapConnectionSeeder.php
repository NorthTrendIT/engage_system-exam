<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SapConnection;
use App\Models\SapApiUrl;

class SapConnectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Schema::disableForeignKeyConstraints();
        SapConnection::truncate();
        SapApiUrl::truncate();
        \Schema::enableForeignKeyConstraints();

        $data = array(
        			array(
                        'company_name' => 'APBW',
        				'user_name' => 'manager',
                        'db_name' => 'TEST-APBW',
                        'password' => 'test',
                    ),
                    array(
                        'company_name' => 'NTMC',
        				'user_name' => 'manager',
                        'db_name' => 'TEST-NTMC',
                        'password' => 'test',
                    ),
                    array(
                        'company_name' => 'PHILCREST',
        				'user_name' => 'manager',
                        'db_name' => 'TEST-PHILCREST',
                        'password' => 'test',
                    ),
                    array(
                        'company_name' => 'PHILSYN',
        				'user_name' => 'manager',
                        'db_name' => 'TEST-PHILSYN',
                        'password' => 'test',
                    ),
                    array(
                        'company_name' => 'SOLID TREND',
                        'user_name' => 'manager',
                        'db_name' => 'TEST-APBW',
                        'password' => 'test',
                    ),
        		);
        SapConnection::insert($data);

        SapApiUrl::create(['url'=>'https://project.northtrend.com:50000']);
    }
}
