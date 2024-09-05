<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeAndLineRemarksToLocalOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_order_items', function (Blueprint $table) {
            $table->enum('type', ['product', 'promo'])->default('product')->after('total');
            $table->text('line_remarks')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_order_items', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('line_remarks');
        });
    }
}
