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
        		);
        Module::insert($data);
    }
}
