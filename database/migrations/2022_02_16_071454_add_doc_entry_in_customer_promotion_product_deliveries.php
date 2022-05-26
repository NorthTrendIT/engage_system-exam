<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocEntryInCustomerPromotionProductDeliveries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_promotion_product_deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger('doc_entry')->nullable();
            $table->boolean('is_sap_pushed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_promotion_product_deliveries', function (Blueprint $table) {
            //
        });
    }
}
