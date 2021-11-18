<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_type_id')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('discount_percentage');
            $table->string('promotion_for')->default("All");    // All / Limited
            $table->char('promotion_scope',4)->nullable();  // if user selects "Limited" in promotion_for field, then this field will have values like C=customers, CL=class, L=location, P=products and so on
            $table->string('promo_image')->nullable();
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
        Schema::dropIfExists('promotions');
    }
}
