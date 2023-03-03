<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSapConnectionApiFieldValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sap_connection_api_field_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sap_connection_api_field_id')->nullable();
            $table->string('key')->nullable();
            $table->string('value')->nullable();
            $table->unsignedBigInteger('sap_connection_id')->nullable();
            $table->unsignedBigInteger('real_sap_connection_id')->nullable();
            $table->timestamps();
            $table->timestamp('last_sync_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sap_connection_api_field_values');
    }
}
