<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert some stuff
        // DB::table('promotion_types')->insert(
        //     array(
        //         array('name' => 'Price Off Promo - single delivery'),
        //         array('name' => 'Price Off Promo - 3 deliveries'),
        //         array('name' => 'Price Off Promo - 5 deliveries'),
        //         array('name' => 'Price Off Promo - 8 deliveries'),
        //         array('name' => 'Bundle Discount Promo - 1 Delivery  per Product'),
        //         array('name' => 'Bundle Discount Promo - 3 Deliveries  per Product'),
        //         array('name' => 'Bundle Discount Promo - 5 Deliveries  per Product'),
        //         array('name' => 'Bundle Discount Promo - 8 Deliveries  per Product'),
        //         array('name' => 'Premium Reward Promo - 1 Delivery  per Product'),
        //         array('name' => 'Premium Reward Promo - 3 Deliveries  per Product'),
        //         array('name' => 'Premium Reward Promo - 5 Deliveries  per Product'),
        //         array('name' => 'Premium Reward Promo - 38Deliveries  per Product')
        //     )
        // );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_types');
    }
}
