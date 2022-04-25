<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRealSapConnectionIdInLocalOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable()->index('real_sap_connection_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_orders', function (Blueprint $table) {
            //
        });
    }
}
