<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_targets', function (Blueprint $table) {
            $table->id();
            $table->integer('b_unit');
            $table->integer('customer_id');
            $table->integer('brand_id');
            $table->integer('category_id')->nullable();
            $table->integer('january');
            $table->integer('february');
            $table->integer('march');
            $table->integer('april');
            $table->integer('may');
            $table->integer('june');
            $table->integer('july');
            $table->integer('august');
            $table->integer('september');
            $table->integer('october');
            $table->integer('november');
            $table->integer('december');
            $table->integer('year');
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
        Schema::dropIfExists('customer_targets');
    }
}
