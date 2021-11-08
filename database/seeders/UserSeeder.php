<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Schema::disableForeignKeyConstraints();
        User::truncate();
        \Schema::enableForeignKeyConstraints();

        $data = array(
        			'role_id' => 1,
			        'name' => 'Super Admin',
			        'is_active' => true,
			        'email' => 'admin@admin.com',
			        'password' => Hash::make('admin'),
        		);
        User::insert($data);
    }
}
