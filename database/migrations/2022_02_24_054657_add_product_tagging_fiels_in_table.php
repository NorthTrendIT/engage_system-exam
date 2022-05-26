<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductTaggingFielsInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('u_loadindex')->nullable();
            $table->string('u_blength')->nullable();
            $table->string('u_bwidth')->nullable();
            $table->string('u_bheight')->nullable();
            $table->string('u_bthicknes')->nullable();
            $table->string('u_brsvdcapacity')->nullable();
            $table->string('u_bcoldcrankamps')->nullable();
            $table->string('u_bamperhour')->nullable();
            $table->string('u_bhandle')->nullable();
            $table->string('u_bpolarity')->nullable();
            $table->string('u_bterminal')->nullable();
            $table->string('u_bholddown')->nullable();
            $table->string('u_bleadweight')->nullable();
            $table->string('u_btotalweight')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
