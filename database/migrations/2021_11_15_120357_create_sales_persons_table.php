<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_persons', function (Blueprint $table) {
            $table->id();
            $table->string('sales_employee_code');
            $table->string('sales_employee_name');
            $table->string('remark')->nullable();
            $table->string('commission_for_sales_employee')->nullable();
            $table->string('commission_group')->nullable();
            $table->char('locked', 10)->default('tNo');
            $table->string('employee_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('u_manager')->nullable();
            $table->string('u_position')->nullable();
            $table->string('u_initials')->nullable();
            $table->string('u_warehouse')->nullable();
            $table->string('u_password');
            $table->string('u_area')->nullable();
            $table->longText('response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_persons');
    }
}
