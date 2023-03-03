<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarrantyClaimPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warranty_claim_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warranty_id');
            $table->unsignedBigInteger('claim_point_id');
            $table->boolean('is_yes')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warranty_claim_points');
    }
}
