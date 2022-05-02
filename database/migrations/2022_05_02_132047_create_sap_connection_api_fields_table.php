<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSapConnectionApiFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sap_connection_api_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sap_connection_id')->nullable();
            $table->string('field')->nullable();
            $table->string('field_id')->nullable();
            $table->string('table_name')->nullable();
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
        Schema::dropIfExists('sap_connection_api_fields');
    }
}
