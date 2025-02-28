<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignmentIdToTerritorySalesSpecialistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('territory_sales_specialists', function (Blueprint $table) {
            $table->unsignedBigInteger('assignment_id')->nullable()->after('id');
            $table->unsignedBigInteger('sap_connection_id')->nullable()->after('assignment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('territory_sales_specialists', function (Blueprint $table) {
            $table->dropColumn(['assignment_id', 'sap_connection_id']);
        });
    }
}
