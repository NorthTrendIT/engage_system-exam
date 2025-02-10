<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalToLocalOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_orders', function (Blueprint $table) {
            $table->enum('approval', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamp('approved_at')->nullable();
            $table->integer('approved_by')->nullable();
            $table->longText('disapproval_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_orders', function (Blueprint $table) {
            $table->dropColumn('approval');
            $table->dropColumn('approved_at');
            $table->dropColumn('approved_by');
            $table->dropColumn('disapproval_remarks');
        });
    }
}
