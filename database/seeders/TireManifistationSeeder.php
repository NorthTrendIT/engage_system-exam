<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TireManifistation;

class TireManifistationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Schema::disableForeignKeyConstraints();
        TireManifistation::truncate();
        \Schema::enableForeignKeyConstraints();


        \File::deleteDirectory(public_path('/sitebucket/tire-manifistation'));
        \File::makeDirectory(public_path('/sitebucket/tire-manifistation'));

        $old_path =  public_path()."/assets/assets/media/tm_image_1.png";
        $new_path =  public_path().'/sitebucket/tire-manifistation/tm_image_1.png';
        \File::copy($old_path, $new_path);
        

        $data = array(
                    array(
                        'image' => 'tm_image_1.png',
                        'manifistation' => '<p>
                                                <ul>
                                                    <li>Deformation or bulging of the tread area.</li>
                                                </ul>
                                            </p>',
                        'probable_cause' => '<p>
                                                <ul>
                                                    <li>Decohession or loss of bond between tread plies & rubber element caused by excessive stresses and heat build up.</li>
                                                    <li>Oxidation of the tread plies.</li>
                                                    <li>Unrepaired tread cuts or puntures.</li>
                                                    <li>Faulty repair</li>
                                                </ul>
                                            </p>',
                    )
                );
        TireManifistation::insert($data);
    }
}
