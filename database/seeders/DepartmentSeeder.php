<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Schema::disableForeignKeyConstraints();
        Department::truncate();
        \Schema::enableForeignKeyConstraints();

        $data = array(
        			array(
                        'id' => 1,
        				'name' => 'Support'
                    )
        		);
        Department::insert($data);
        
    }
}
