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
            $table->double('quantity', 10, 3);
            $table->date('ship_date')->nullable();
            $table->double('price', 10, 3);
            $table->double('price_after_vat', 10, 3)->default(0.0);
            $table->string('currency')->nullable();
            $table->double('rate', 10, 3)->default(0.0);
            $table->double('discount_percent', 10, 2)->default(0.0);
            $table->string('werehouse_code')->nullable();
            $table->string('sales_person_code');
            $table->double('gross_price', 10, 3)->default(0.0);
            $table->double('gross_total', 10, 3)->default(0.0);
            $table->double('gross_total_fc', 10, 3)->default(0.0);
            $table->double('gross_total_sc', 10, 3)->default(0.0);
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
