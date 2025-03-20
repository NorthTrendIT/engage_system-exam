<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelledByToQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Add cancelled_by column as unsigned integer (assuming user ID is unsigned)
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancelled');

            // Foreign key constraint (optional, but recommended)
            $table->foreign('cancelled_by')
                  ->references('id')->on('users')
                  ->onDelete('set null'); // This means if the user is deleted, cancelled_by will be set to null
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']); // Drop the foreign key constraint
            $table->dropColumn('cancelled_by'); // Drop the cancelled_by column
        });
    }
}
