<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\DepartmentRole;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Schema::disableForeignKeyConstraints();
        Role::truncate();
        \Schema::enableForeignKeyConstraints();

        $data = array(
        			array(
                        'id' => 1,
        				'name' => 'Super Admin'
                    ),
                    array(
                        'id' => 2,
        				'name' => 'Sales Specialist'
        			),
                    array(
                        'id' => 3,
                        'name' => 'Support'
                    ),
                    array(
                        'id' => 4,
                        'name' => 'Customer'
                    )
        		);
        Role::insert($data);


        $data = array(
                    array(
                        'role_id' => 3,
                        'department_id' => 1,
                    ),
                    array(
                        'role_id' => 4,
                        'department_id' => 3,
                    ),
                    array(
                        'role_id' => 2,
                        'department_id' => 2,
                    ),
                );
        DepartmentRole::insert($data);
    }
}
