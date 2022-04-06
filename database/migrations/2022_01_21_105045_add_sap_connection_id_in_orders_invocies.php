<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSapConnectionIdInOrdersInvocies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('sap_connection_id')->nullable()->index();
        });

        Schema::table('invoices', function (Blueprint $table) {
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
        Schema::table('orders_invocies', function (Blueprint $table) {
            //
        });
    }
}
