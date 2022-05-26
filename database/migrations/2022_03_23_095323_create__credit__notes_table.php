<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('base_entry')->nullable()->index();
            $table->unsignedBigInteger('doc_entry')->index();
            $table->unsignedBigInteger('doc_num')->index();
            $table->string('num_at_card')->nullable();
            $table->string('doc_type')->nullable()->index();
            $table->date('doc_date')->nullable();
            $table->date('doc_due_date')->nullable();
            $table->string('card_code')->nullable();
            $table->string('card_name')->nullable();
            $table->string('address')->nullable();
            $table->double('doc_total', 10, 3)->default(0.0);
            $table->string('doc_currency')->nullable();
            $table->string('journal_memo')->nullable();
            $table->unsignedBigInteger('payment_group_code')->nullable();
            $table->string('sales_person_code');
            $table->string('u_brand')->nullable();
            $table->string('u_branch')->nullable();
            $table->string('u_commitment')->nullable();
            $table->string('u_time')->nullable();
            $table->string('u_posono')->nullable();
            $table->date('u_posodate')->nullable();
            $table->time('u_posotime')->nullable();
            $table->string('u_sostat')->nullable()->index();
            $table->string('document_status')->nullable()->index();

            $table->string('cancelled')->nullable();
            $table->date('cancel_date')->nullable();
            $table->date('updated_date')->nullable();
            $table->longText('comments')->nullable();

            $table->unsignedBigInteger('sap_connection_id')->nullable()->index();

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
        Schema::dropIfExists('credit_notes');
    }
}
