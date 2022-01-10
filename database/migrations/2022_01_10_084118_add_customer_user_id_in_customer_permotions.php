<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerUserIdInCustomerPermotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_promotions', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_user_id')->nullable()->after('sales_specialist_id')->comment('User who placed order behalf of customer.');
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
