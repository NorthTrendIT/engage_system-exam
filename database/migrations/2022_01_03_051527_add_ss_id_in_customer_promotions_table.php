<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSsIdInCustomerPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_promotions', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_specialist_id')->nullable()->after('user_id')->comment('Sales Specialist who placed order.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_promotions', function (Blueprint $table) {
            //
        });
    }
}
