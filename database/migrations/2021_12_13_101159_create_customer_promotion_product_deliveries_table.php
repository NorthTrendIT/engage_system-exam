<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPromotionProductDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_promotion_product_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_promotion_product_id');
            $table->date("delivery_date")->nullable();
            $table->unsignedBigInteger('delivery_quantity')->nullable();
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
        Schema::dropIfExists('customer_promotion_product_deliveries');
    }
}
