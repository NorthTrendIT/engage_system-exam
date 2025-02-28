<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSalesAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_assignment', function (Blueprint $table) {
            $table->json('brand')->nullable()->after('assignment_name'); 
            $table->json('line')->nullable()->after('brand');  
            $table->json('category')->nullable()->after('line'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_assignment', function (Blueprint $table) {
            $table->dropColumn(['brand', 'line', 'category']); 
        });
    }
}
