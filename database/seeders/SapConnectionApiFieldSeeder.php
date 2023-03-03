<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SapConnectionApiField;

class SapConnectionApiFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Schema::disableForeignKeyConstraints();
        SapConnectionApiField::truncate();
        \Schema::enableForeignKeyConstraints();


        $data = array(
                    // Start - Customers
                        array(
                            'sap_connection_id' => 1,
                            'real_sap_connection_id' => 1,
                            'field' => 'segment',
                            'sap_field_id' => 134,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 2,
                            'real_sap_connection_id' => 2,
                            'field' => 'segment',
                            'sap_field_id' => 101,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 3,
                            'real_sap_connection_id' => 3,
                            'field' => 'segment',
                            'sap_field_id' => 96,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 4,
                            'real_sap_connection_id' => 4,
                            'field' => 'segment',
                            'sap_field_id' => 89,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 5,
                            'real_sap_connection_id' => 1,
                            'field' => 'segment',
                            'sap_field_id' => 134,
                            'sap_table_name' => 'OCRD',
                        ),


                        array(
                            'sap_connection_id' => 1,
                            'real_sap_connection_id' => 1,
                            'field' => 'subsector',
                            'sap_field_id' => 135,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 2,
                            'real_sap_connection_id' => 2,
                            'field' => 'subsector',
                            'sap_field_id' => 102,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 3,
                            'real_sap_connection_id' => 3,
                            'field' => 'subsector',
                            'sap_field_id' => 90,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 4,
                            'real_sap_connection_id' => 4,
                            'field' => 'subsector',
                            'sap_field_id' => 97,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 5,
                            'real_sap_connection_id' => 1,
                            'field' => 'subsector',
                            'sap_field_id' => 135,
                            'sap_table_name' => 'OCRD',
                        ),


                        array(
                            'sap_connection_id' => 1,
                            'real_sap_connection_id' => 1,
                            'field' => 'province',
                            'sap_field_id' => 136,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 2,
                            'real_sap_connection_id' => 2,
                            'field' => 'province',
                            'sap_field_id' => 103,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 3,
                            'real_sap_connection_id' => 3,
                            'field' => 'province',
                            'sap_field_id' => 91,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 4,
                            'real_sap_connection_id' => 4,
                            'field' => 'province',
                            'sap_field_id' => 98,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 5,
                            'real_sap_connection_id' => 1,
                            'field' => 'province',
                            'sap_field_id' => 136,
                            'sap_table_name' => 'OCRD',
                        ),


                        array(
                            'sap_connection_id' => 1,
                            'real_sap_connection_id' => 1,
                            'field' => 'sector',
                            'sap_field_id' => 141,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 2,
                            'real_sap_connection_id' => 2,
                            'field' => 'sector',
                            'sap_field_id' => 108,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 3,
                            'real_sap_connection_id' => 3,
                            'field' => 'sector',
                            'sap_field_id' => 103,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 4,
                            'real_sap_connection_id' => 4,
                            'field' => 'sector',
                            'sap_field_id' => 96,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 5,
                            'real_sap_connection_id' => 1,
                            'field' => 'sector',
                            'sap_field_id' => 141,
                            'sap_table_name' => 'OCRD',
                        ),


                        array(
                            'sap_connection_id' => 1,
                            'real_sap_connection_id' => 1,
                            'field' => 'classification',
                            'sap_field_id' => 140,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 2,
                            'real_sap_connection_id' => 2,
                            'field' => 'classification',
                            'sap_field_id' => 107,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 3,
                            'real_sap_connection_id' => 3,
                            'field' => 'classification',
                            'sap_field_id' => 102,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 4,
                            'real_sap_connection_id' => 4,
                            'field' => 'classification',
                            'sap_field_id' => 95,
                            'sap_table_name' => 'OCRD',
                        ),
                        array(
                            'sap_connection_id' => 5,
                            'real_sap_connection_id' => 1,
                            'field' => 'classification',
                            'sap_field_id' => 140,
                            'sap_table_name' => 'OCRD',
                        ),
                    // End - Customers


                    // Start - Products
                        array(
                            'sap_connection_id' => 1,
                            'real_sap_connection_id' => 1,
                            'field' => 'product-line',
                            'sap_field_id' => 24,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 2,
                            'real_sap_connection_id' => 2,
                            'field' => 'product-line',
                            'sap_field_id' => 17,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 3,
                            'real_sap_connection_id' => 3,
                            'field' => 'product-line',
                            'sap_field_id' => 16,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 4,
                            'real_sap_connection_id' => 4,
                            'field' => 'product-line',
                            'sap_field_id' => 16,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 5,
                            'real_sap_connection_id' => 1,
                            'field' => 'product-line',
                            'sap_field_id' => 24,
                            'sap_table_name' => 'OITM',
                        ),


                        array(
                            'sap_connection_id' => 1,
                            'real_sap_connection_id' => 1,
                            'field' => 'product-type',
                            'sap_field_id' => 25,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 2,
                            'real_sap_connection_id' => 2,
                            'field' => 'product-type',
                            'sap_field_id' => 18,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 3,
                            'real_sap_connection_id' => 3,
                            'field' => 'product-type',
                            'sap_field_id' => 17,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 4,
                            'real_sap_connection_id' => 4,
                            'field' => 'product-type',
                            'sap_field_id' => 14,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 5,
                            'real_sap_connection_id' => 1,
                            'field' => 'product-type',
                            'sap_field_id' => 25,
                            'sap_table_name' => 'OITM',
                        ),


                        array(
                            'sap_connection_id' => 1,
                            'real_sap_connection_id' => 1,
                            'field' => 'product-application',
                            'sap_field_id' => 26,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 2,
                            'real_sap_connection_id' => 2,
                            'field' => 'product-application',
                            'sap_field_id' => 19,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 3,
                            'real_sap_connection_id' => 3,
                            'field' => 'product-application',
                            'sap_field_id' => 18,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 4,
                            'real_sap_connection_id' => 4,
                            'field' => 'product-application',
                            'sap_field_id' => 18,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 5,
                            'real_sap_connection_id' => 1,
                            'field' => 'product-application',
                            'sap_field_id' => 26,
                            'sap_table_name' => 'OITM',
                        ),


                        array(
                            'sap_connection_id' => 1,
                            'real_sap_connection_id' => 1,
                            'field' => 'product-pattern',
                            'sap_field_id' => 38,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 2,
                            'real_sap_connection_id' => 2,
                            'field' => 'product-pattern',
                            'sap_field_id' => 31,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 3,
                            'real_sap_connection_id' => 3,
                            'field' => 'product-pattern',
                            'sap_field_id' => 30,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 4,
                            'real_sap_connection_id' => 4,
                            'field' => 'product-pattern',
                            'sap_field_id' => 30,
                            'sap_table_name' => 'OITM',
                        ),
                        array(
                            'sap_connection_id' => 5,
                            'real_sap_connection_id' => 1,
                            'field' => 'product-pattern',
                            'sap_field_id' => 38,
                            'sap_table_name' => 'OITM',
                        ),
                    // End - Products
                );

        SapConnectionApiField::insert($data);
    }
}
