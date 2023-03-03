<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpenAmountAndRemainingOpenQuantityInOrderAndInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->double('open_amount', 10, 3)->default(0.0)->nullable();
            $table->double('remaining_open_quantity', 10, 3)->default(0.0)->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->double('open_amount', 10, 3)->default(0.0)->nullable();
            $table->double('remaining_open_quantity', 10, 3)->default(0.0)->nullable();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->double('open_amount', 10, 3)->default(0.0)->nullable();
            $table->double('remaining_open_quantity', 10, 3)->default(0.0)->nullable();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->double('open_amount', 10, 3)->default(0.0)->nullable();
            $table->double('remaining_open_quantity', 10, 3)->default(0.0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_and_invoices', function (Blueprint $table) {
            //
        });
    }
}
