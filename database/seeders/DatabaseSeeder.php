<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DepartmentSeeder::class);
    	$this->call(RoleSeeder::class);
    	$this->call(ModuleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ActivityMasterSeeder::class);
        $this->call(SapConnectionSeeder::class);
        $this->call(ClaimPointSeeder::class);
        $this->call(TireManifistationSeeder::class);
        // \App\Models\User::factory(10)->create();
    }
}
