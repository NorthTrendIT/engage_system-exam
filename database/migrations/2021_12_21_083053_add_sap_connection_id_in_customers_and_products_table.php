<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSapConnectionIdInCustomersAndProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('sap_connection_id')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
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
        Schema::table('customers_and_products', function (Blueprint $table) {
            //
        });
    }
}
