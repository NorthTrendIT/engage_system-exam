<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdFieldInPromotionInterests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotion_interests', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('is_interested');
        });
        
        Schema::table('promotion_interests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('promotion_id')->nullable();
            $table->boolean('is_interested')->default(false)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotion_interests', function (Blueprint $table) {
            //
        });
    }
}
