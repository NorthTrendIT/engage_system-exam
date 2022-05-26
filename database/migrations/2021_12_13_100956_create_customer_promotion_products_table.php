<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPromotionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_promotion_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_promotion_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('quantity')->nullable();
            $table->double("price")->nullable();
            $table->double("discount")->nullable();
            $table->double("amount")->nullable();
            $table->longText("last_data")->nullable();
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
        Schema::dropIfExists('customer_promotion_products');
    }
}
