<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVatGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->text('name')->nullable();
            $table->text('category')->nullable();
            $table->text('tax_account')->nullable();
            $table->text('eu')->nullable();
            $table->text('triangular_deal')->nullable();
            $table->text('acquisition_reverse')->nullable();
            $table->double('non_deduct', 10, 3)->default(0.0);
            $table->text('acquisition_tax')->nullable();
            $table->text('goods_shipment')->nullable();
            $table->text('non_deduct_acc')->nullable();
            $table->text('deferred_tax_acc')->nullable();
            $table->text('correction')->nullable();
            $table->text('vat_correction')->nullable();
            $table->text('equalization_tax_account')->nullable();
            $table->text('service_supply')->nullable();
            $table->text('inactive')->nullable();
            $table->text('tax_type_black_list')->nullable();
            $table->text('report_349_code')->nullable();
            $table->text('vat_in_revenue_account')->nullable();
            $table->text('down_payment_tax_offset_account')->nullable();
            $table->text('cash_discount_account')->nullable();
            $table->text('vat_deductible_account')->nullable();
            $table->text('tax_region')->nullable();
            $table->longText('vatgroups_lines')->nullable();
            $table->integer('sap_connection_id');
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
        Schema::dropIfExists('vat_groups');
    }
}
