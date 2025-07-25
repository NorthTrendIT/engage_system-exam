<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_entry')->index();
            $table->unsignedBigInteger('doc_num')->index();
            $table->string('doc_type');
            $table->date('doc_date')->nullable();
            $table->date('doc_due_date')->nullable();
            $table->string('card_code');
            $table->string('card_name');
            $table->string('address')->nullable();
            $table->double('doc_total', 10, 3)->default(0.0);
            $table->string('doc_currency')->nullable();
            $table->string('journal_memo')->nullable();
            $table->unsignedBigInteger('payment_group_code');
            $table->string('sales_person_code');
            $table->string('u_brand')->nullable();
            $table->string('u_branch')->nullable();
            $table->string('u_commitment')->nullable();
            $table->string('u_time')->nullable();
            $table->string('u_posono')->nullable();
            $table->date('u_posodate')->nullable();
            $table->time('u_posotime')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
