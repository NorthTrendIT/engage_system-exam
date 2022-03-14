<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarrantyDiagnosticReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warranty_diagnostic_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warranty_id')->nullable();
            $table->boolean('result')->nullable();
            $table->text('tire_manifistations')->nullable();
            $table->string('tire_size')->nullable();
            $table->float('tire_size_selling_price')->nullable();
            $table->string('remaining_tread_depth')->nullable();
            $table->float('warranty_claim_adjustment')->nullable();
            $table->float('payment_for_the_new_tire_replacement')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warranty_diagnostic_reports');
    }
}
