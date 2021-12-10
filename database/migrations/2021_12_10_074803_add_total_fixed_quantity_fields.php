<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalFixedQuantityFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotion_types', function (Blueprint $table) {
            $table->boolean('is_total_fixed_quantity')->default(false)->after('is_fixed_quantity');
            $table->unsignedBigInteger("total_fixed_quantity")->after('is_total_fixed_quantity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotion_types', function (Blueprint $table) {
            //
        });
    }
}
