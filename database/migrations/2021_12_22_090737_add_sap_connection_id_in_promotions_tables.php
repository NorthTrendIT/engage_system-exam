<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSapConnectionIdInPromotionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_promotions', function (Blueprint $table) {
            $table->unsignedBigInteger('sap_connection_id')->nullable()->index();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('sap_connection_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotions_tables', function (Blueprint $table) {
            //
        });
    }
}
