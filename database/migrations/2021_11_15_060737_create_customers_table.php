<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('card_code')->nullable()->index();
            $table->string('card_type')->nullable()->index();
            $table->string('card_name')->nullable();
            $table->unsignedBigInteger('group_code')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('city')->nullable();
            $table->date('created_date')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('customers');
    }
}
