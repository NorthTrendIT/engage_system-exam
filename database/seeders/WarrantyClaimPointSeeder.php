<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WarrantyClaimPoint;

class WarrantyClaimPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Schema::disableForeignKeyConstraints();
        WarrantyClaimPoint::truncate();
        \Schema::enableForeignKeyConstraints();


        $data = array(
                    array(
                        'title' => 'Wearing (shoulder wear, center wear, one-sided wear, or any irregular wear on tread)',
                        'parent_id' => null,
                    ),
                    array(
                        'title' => 'Any sign of cuts or puncture on the tire - indicate if penetrated or not.',
                        'parent_id' => null,
                    ),
                    array(
                        'title' => 'If the tire is repaired, indicate if the repair is hot process or cold patch.',
                        'parent_id' => null,
                    ),
                    array(
                        'title' => 'Abrasion or impact marks near the complaint area of the tread/shoulder, sidewall, bead area.',
                        'parent_id' => null,
                    ),
                    array(
                        'title' => 'Check for any irregularities on the bead. Below are some irregularities:',
                        'parent_id' => null,
                    ),
                    array(
                        'title' => 'Inner liner manifestations:',
                        'parent_id' => null,
                    ),

                    array(
                        'title' => 'Shoulder',
                        'parent_id' => 1,
                    ),
                    array(
                        'title' => 'Center',
                        'parent_id' => 1,
                    ),
                    array(
                        'title' => 'On-Sided ',
                        'parent_id' => 1,
                    ),
                    array(
                        'title' => 'Irregular ',
                        'parent_id' => 1,
                    ),


                    array(
                        'title' => 'Penetrated',
                        'parent_id' => 2,
                    ),
                    array(
                        'title' => 'Not Penetrated',
                        'parent_id' => 2,
                    ),


                    array(
                        'title' => 'Hot Process',
                        'parent_id' => 3,
                    ),
                    array(
                        'title' => 'Cold Process',
                        'parent_id' => 3,
                    ),


                    array(
                        'title' => 'Tread',
                        'parent_id' => 4,
                    ),
                    array(
                        'title' => 'Shoulder',
                        'parent_id' => 4,
                    ),
                    array(
                        'title' => 'Sidewall',
                        'parent_id' => 4,
                    ),
                    array(
                        'title' => 'Bead',
                        'parent_id' => 4,
                    ),


                    array(
                        'title' => 'cracks on the bead area (circumferential or not)',
                        'parent_id' => 5,
                    ),
                    array(
                        'title' => 'deformation on the bead (triangulated or any sign of improper bead seating)',
                        'parent_id' => 5,
                    ),
                    array(
                        'title' => 'evidence of bead heating and bead rubber brittleness',
                        'parent_id' => 5,
                    ),
                    array(
                        'title' => 'sign of mounting / demounting damage on bead',
                        'parent_id' => 5,
                    ),
                    array(
                        'title' => 'bulge or separation on the bead area.',
                        'parent_id' => 5,
                    ),
                    array(
                        'title' => 'narrow rim (check/measure bead to bead clearance) - it should be within standard rim as indicated on the sidewall/tire.',
                        'parent_id' => 5,
                    ),
                    array(
                        'title' => 'traces of chemicals or oil used during tire mounting.',
                        'parent_id' => 5,
                    ),


                    array(
                        'title' => 'sign of crease or stress marks ',
                        'parent_id' => 6,
                    ),
                    array(
                        'title' => 'improper repair or repair failure',
                        'parent_id' => 6,
                    ),
                    array(
                        'title' => 'inner liner cracks near the bead area of the damage',
                        'parent_id' => 6,
                    ),
                    array(
                        'title' => 'any sign of running the tire in underinflated condition.',
                        'parent_id' => 6,
                    ),
                );
        WarrantyClaimPoint::insert($data);
    }
}
