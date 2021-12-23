<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInCustomersAndProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('u_mkt_segment')->nullable();
            $table->string('u_cust_segment')->nullable();
            $table->string('u_subsector')->nullable();
            $table->string('u_province')->nullable();
            $table->string('u_card_code')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('u_mobil_sc')->nullable();
            $table->string('u_item_type')->nullable();
            $table->string('u_item_application')->nullable();
            $table->string('u_pattern_type')->nullable();
            $table->string('u_tire_size')->nullable();
            $table->string('u_tire_diameter')->nullable();
            $table->string('u_speed_symbol')->nullable();
            $table->string('u_ply_rating')->nullable();
            $table->string('u_tire_const')->nullable();
            $table->string('u_fitment_conf')->nullable();
            $table->string('u_business_group')->nullable();
            $table->string('u_section_width')->nullable();
            $table->string('u_series')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers_and_products', function (Blueprint $table) {
            //
        });
    }
}
