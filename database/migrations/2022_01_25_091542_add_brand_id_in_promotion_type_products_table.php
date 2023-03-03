<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrandIdInPromotionTypeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotion_type_products', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->after('promotion_type_id')->comment('product_groups table id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotion_type_products', function (Blueprint $table) {
            //
        });
    }
}
