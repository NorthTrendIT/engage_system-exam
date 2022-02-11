<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarrantyVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warranty_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warranty_id');
            $table->string('vehicle_maker')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_mileage')->nullable();
            $table->unsignedInteger('year')->nullable();
            $table->string('license_plate')->nullable();
            $table->text('lt_tire_position')->nullable();
            $table->string('lt_tire_mileage')->nullable();
            $table->text('tb_tire_position')->nullable();
            $table->string('tb_tire_mileage')->nullable();
            $table->text('reason_for_tire_return')->nullable();
            $table->text('location_of_damage')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warranty_vehicles');
    }
}
