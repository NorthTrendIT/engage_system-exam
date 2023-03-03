<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('sales_specialist_id')->nullable()->comment('Sales Specialist who placed order.');
            $table->char('placed_by',4)->comment('C = By Customer, S = By Sales Specialist');
            $table->char('confirmation_status')->default('C')->comment('C = Confirm, P = Pending, This status get set to P only whene sales specialist place order for customer.');
            $table->date('due_date')->nullable();
            $table->string('card_code')->nullable();
            $table->string('card_name')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
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
        Schema::dropIfExists('local_orders');
    }
}
