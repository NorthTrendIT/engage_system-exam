<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductBenefits;

class ProductBenefitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            [
                'code' => 'B1',
                'name' => 'High Speed Performance',
            ],
            [
                'code' => 'B2',
                'name' => 'Long Tread Life',
            ],
            [
                'code' => 'B3',
                'name' => 'Off Road Performance',
            ],
            [
                'code' => 'B4',
                'name' => 'Ride Comfort',
            ],
            [
                'code' => 'B5',
                'name' => 'Wet Performance',
            ],
            [
                'code' => 'B6',
                'name' => '',
            ],
            [
                'code' => 'B7',
                'name' => '',
            ],
            [
                'code' => 'B8',
                'name' => '',
            ],
            [
                'code' => 'B9',
                'name' => '',
            ],
            [
                'code' => 'B10',
                'name' => '',
            ]
        );

        ProductBenefits::insert($data);
    }
}
