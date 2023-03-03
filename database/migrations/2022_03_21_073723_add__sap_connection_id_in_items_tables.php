<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSapConnectionIdInItemsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('sap_connection_id')->nullable();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->unsignedBigInteger('sap_connection_id')->nullable();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->unsignedBigInteger('sap_connection_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items_tables', function (Blueprint $table) {
            //
        });
    }
}
