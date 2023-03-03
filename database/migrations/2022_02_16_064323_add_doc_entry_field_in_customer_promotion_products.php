<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocEntryFieldInCustomerPromotionProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_promotion_products', function (Blueprint $table) {
            // $table->unsignedBigInteger('doc_entry')->nullable();
            // $table->boolean('is_sap_pushed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_promotion_products', function (Blueprint $table) {
            //
        });
    }
}
