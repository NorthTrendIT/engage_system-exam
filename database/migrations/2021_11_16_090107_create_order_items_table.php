<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->integer('line_num');
            $table->string('item_code');
            $table->string('item_description')->nullable();
            $table->double('quantity', 8, 2);
            $table->date('ship_date')->nullable();
            $table->double('price', 8, 2);
            $table->double('price_after_vat', 8, 2)->default(0.0);
            $table->string('currency')->nullable();
            $table->double('rate', 8, 2)->default(0.0);
            $table->double('discount_percent', 8, 2)->default(0.0);
            $table->string('werehouse_code')->nullable();
            $table->unsignedBigInteger('sales_person_code');
            $table->double('gross_price', 8, 2)->default(0.0);
            $table->double('gross_total', 8, 2)->default(0.0);
            $table->double('gross_total_fc', 8, 2)->default(0.0);
            $table->double('gross_total_sc', 8, 2)->default(0.0);
            $table->integer('ncm_code')->default(0);
            $table->string('ship_to_code')->nullable();
            $table->string('ship_to_description')->nullable();
            $table->longText('response')->nullable();
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
        Schema::dropIfExists('order_items');
    }
}
