<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSapConnectionIdToTerritoriesTable extends Migration
{
    public function up()
    {
        Schema::table('territories', function (Blueprint $table) {
            $table->integer('sap_connection_id')->after('response')->nullable();  // Adjust type as needed
        });
    }

    public function down()
    {
        Schema::table('territories', function (Blueprint $table) {
            $table->dropColumn('sap_connection_id');
        });
    }

}