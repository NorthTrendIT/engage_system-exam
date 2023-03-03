<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeOfCustomerRequestFieldInHelpDesks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_desks', function (Blueprint $table) {
            $table->string('type_of_customer_request')->default('Others')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('help_desks', function (Blueprint $table) {
            //
        });
    }
}
