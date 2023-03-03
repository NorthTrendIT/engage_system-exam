<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('total_quantity')->nullable();
            $table->double("total_price")->nullable();
            $table->double("total_discount")->nullable();
            $table->double("total_amount")->nullable();
            $table->string("status")->default('pending');
            $table->text("cancel_reason")->nullable();
            $table->longText("last_data")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_promotions');
    }
}
