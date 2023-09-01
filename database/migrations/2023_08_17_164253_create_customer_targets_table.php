<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_targets', function (Blueprint $table) {
            $table->id();
            $table->integer('b_unit');
            $table->integer('customer_id');
            $table->integer('brand_id');
            $table->integer('category_id')->nullable();
            $table->decimal('january', 10,2)->default(0);
            $table->decimal('february', 10,2)->default(0);
            $table->decimal('march', 10,2)->default(0);
            $table->decimal('april', 10,2)->default(0);
            $table->decimal('may', 10,2)->default(0);
            $table->decimal('june', 10,2)->default(0);
            $table->decimal('july', 10,2)->default(0);
            $table->decimal('august', 10,2)->default(0);
            $table->decimal('september', 10,2)->default(0);
            $table->decimal('october', 10,2)->default(0);
            $table->decimal('november', 10,2)->default(0);
            $table->decimal('december', 10,2)->default(0);
            $table->integer('year');
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
        Schema::dropIfExists('customer_targets');
    }
}
