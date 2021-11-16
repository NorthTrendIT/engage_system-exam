<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->string('zip_code',20)->nullable();
            $table->string('phone1',30)->nullable();
            $table->longtext('notes')->nullable();
            $table->string('credit_limit')->nullable();
            $table->string('max_commitment')->nullable();
            $table->string('federal_tax_id')->nullable();
            $table->string('current_account_balance')->nullable();
            $table->string('vat_group')->nullable();
            $table->string('u_regowner')->nullable();
            $table->string('u_mp')->nullable();
            $table->string('u_msec')->nullable();
            $table->string('u_tsec')->nullable();
            $table->string('u_class')->nullable();
            $table->string('u_rgn')->nullable();


            $table->datetime('created_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
}
