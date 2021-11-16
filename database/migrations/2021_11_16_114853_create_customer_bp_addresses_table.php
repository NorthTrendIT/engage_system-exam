<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerBpAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_bp_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('order')->index();

            $table->string('bp_code')->nullable()->index();
            $table->text('address')->nullable();
            $table->text('street')->nullable();
            $table->string('zip_code',20)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('federal_tax_id')->nullable();
            $table->string('tax_code')->nullable();
            $table->string('address_type')->nullable();
            $table->datetime('created_date')->nullable();

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
        Schema::dropIfExists('customer_bp_addresses');
    }
}
