<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastSyncAtFieldInSapTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable();
        });

        Schema::table('credit_notes', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable();
        });

        Schema::table('customer_groups', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable();
        });

        Schema::table('product_groups', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable();
        });

        Schema::table('territories', function (Blueprint $table) {
            $table->timestamp('last_sync_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sap_tables', function (Blueprint $table) {
            //
        });
    }
}
