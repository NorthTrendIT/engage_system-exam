<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->nullable()->index();
            $table->text('item_name')->nullable()->index();
            $table->string('foreign_name')->nullable();
            $table->bigInteger('items_group_code')->nullable();
            $table->bigInteger('customs_group_code')->nullable();
            $table->string('sales_vat_group')->nullable();
            $table->string('purchase_vat_group')->nullable();
            $table->datetime('created_date')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('products');
    }
}
