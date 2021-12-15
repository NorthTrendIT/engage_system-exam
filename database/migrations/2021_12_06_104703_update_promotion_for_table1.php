<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePromotionForTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotion_for', function (Blueprint $table) {
            $table->unsignedBigInteger('territory_id')->after('promotion_id')->nullable();
            $table->dropColumn('area_id');
            $table->dropColumn('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotion_for', function (Blueprint $table) {
            //
        });
    }
}
