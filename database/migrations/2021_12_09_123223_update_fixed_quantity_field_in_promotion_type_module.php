<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFixedQuantityFieldInPromotionTypeModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotion_types', function (Blueprint $table) {
            $table->dropColumn('fixed_quantity');
        });

        Schema::table('promotion_type_products', function (Blueprint $table) {
           $table->unsignedBigInteger("fixed_quantity")->after('product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotion_type_module', function (Blueprint $table) {
            //
        });
    }
}
