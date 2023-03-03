<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionTypeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_type_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("promotion_type_id")->nullable();
            $table->unsignedBigInteger("product_id")->nullable();
            $table->double("discount_percentage")->nullable();
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
        Schema::dropIfExists('promotion_type_products');
    }
}
