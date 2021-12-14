<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_entry');
            $table->unsignedBigInteger('doc_num');
            $table->string('doc_type');
            $table->string('document_status', 64)->nullable();
            $table->date('doc_date')->nullable();
            $table->date('doc_due_date')->nullable();
            $table->string('card_code');
            $table->string('card_name');
            $table->string('address')->nullable();
            $table->double('doc_total', 10, 3)->default(0.0);
            $table->string('doc_currency')->nullable();
            $table->string('journal_memo')->nullable();
            $table->string('payment_group_code')->nullable();
            $table->string('sales_person_code')->nullable();
            $table->string('u_brand')->nullable();
            $table->string('u_branch')->nullable();
            $table->string('u_commitment')->nullable();
            $table->string('u_time')->nullable();
            $table->string('u_posono')->nullable();
            $table->date('u_posodate')->nullable();
            $table->time('u_posotime')->nullable();
            $table->longText('response')->nullable();
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
        Schema::dropIfExists('quotations');
    }
}
