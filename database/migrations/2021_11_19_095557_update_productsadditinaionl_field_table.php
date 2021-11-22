<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsadditinaionlFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_features_id');
            $table->dropColumn('product_benefits_id');
            $table->dropColumn('product_sell_sheets_id');
            //$table->dropColumn('image');

            $table->longText('product_features')->nullable();
            $table->longText('product_benefits')->nullable();
            $table->longText('product_sell_sheets')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
