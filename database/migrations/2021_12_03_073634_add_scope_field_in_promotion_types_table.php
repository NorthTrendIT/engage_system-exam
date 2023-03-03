<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScopeFieldInPromotionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotion_types', function (Blueprint $table) {
            $table->dropColumn("name");

            $table->string("title")->nullable()->after('id');
            $table->string("scope",2)->nullable()->after('name')->comment('P=Price Off in percentage, R=percentage Range, U=percentage Discount + Up to Amount limit');
            $table->double("percentage")->nullable()->after('scope');
            $table->double("min_percentage")->nullable()->after('percentage');
            $table->double("max_percentage")->nullable()->after('min_percentage');
            $table->double("fixed_price")->nullable()->after('max_percentage');
            $table->unsignedBigInteger("fixed_quantity")->nullable()->after('fixed_price');
            $table->unsignedBigInteger("number_of_delivery")->nullable()->after('fixed_quantity');
            $table->boolean('is_active')->default(true)->after('number_of_delivery');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotion_types', function (Blueprint $table) {
            //
        });
    }
}
