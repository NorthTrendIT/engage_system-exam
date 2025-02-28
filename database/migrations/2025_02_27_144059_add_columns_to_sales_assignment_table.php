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
            $table->json('brand_ids')->nullable()->after('assignment_name'); 
            $table->json('line_ids')->nullable()->after('brand_ids');  
            $table->json('category_ids')->nullable()->after('line_ids'); 
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
