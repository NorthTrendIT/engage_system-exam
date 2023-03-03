<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFieldsHelpDesksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('help_desks', function (Blueprint $table) {
            $table->dropColumn("department_id");
            $table->dropColumn("user_type");
            $table->dropColumn("name");
            $table->dropColumn("email");
        });

        Schema::table('help_desk_comments', function (Blueprint $table) {
            $table->dropColumn("user_type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
