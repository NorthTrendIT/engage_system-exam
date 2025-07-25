<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentStatusFiledInOrdersAndInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('document_status')->nullable()->index();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('document_status')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders_and_invoices', function (Blueprint $table) {
            //
        });
    }
}
