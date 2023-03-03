<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTireSerialNoInWarrantyVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warranty_vehicles', function (Blueprint $table) {
            $table->string('lt_tire_serial_no')->after('lt_tire_mileage')->nullable();
            $table->string('tb_tire_serial_no')->after('tb_tire_mileage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warranty_vehicles', function (Blueprint $table) {
            //
        });
    }
}
