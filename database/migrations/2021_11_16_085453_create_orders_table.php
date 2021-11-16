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
            $table->string('doc_enrty');
            $table->string('doc_num');
            $table->string('doc_type');
            $table->string('doc_date');
            $table->string('doc_due_date');
            $table->string('card_code');
            $table->string('card_name');
            $table->string('address');
            $table->string('doc_total');
            $table->string('doc_currency');
            $table->string('journal_memo');
            $table->string('payment_group_code');
            $table->string('sales_person_code');
            $table->string('creation_date');
            $table->string('update_date');
            $table->string('u_brand');
            $table->string('u_branch');
            $table->string('u_commitment');
            $table->string('u_time');
            $table->string('u_posono');
            $table->string('u_posodate');
            $table->string('u_posotime');
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
