<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
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
        				'name' => 'Super Admin'
        			)
        		);
        Role::insert($data);
    }
}
