<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPatternCategoryFieldInPromotionTypeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotion_type_products', function (Blueprint $table) {
            $table->string('product_option')->nullable()->default('product')->after('promotion_type_id');
            $table->string('category')->nullable()->after('product_id');
            $table->string('pattern')->nullable()->after('category');
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
