<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginalSapConnectionIdInCustomersAndOtherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('credit_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('customer_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });

        Schema::table('customer_promotion_product_deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger('real_sap_connection_id')->after('sap_connection_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers_and_other', function (Blueprint $table) {
            //
        });
    }
}
