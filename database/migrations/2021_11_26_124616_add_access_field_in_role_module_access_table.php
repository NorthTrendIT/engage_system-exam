<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccessFieldInRoleModuleAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('role_module_access', function (Blueprint $table) {
            $table->dropColumn('add_access');
            $table->dropColumn('edit_access');
            $table->dropColumn('view_access');
            $table->dropColumn('delete_access');

            $table->boolean('access')->default(false)->after('module_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role_module_access', function (Blueprint $table) {
            //
        });
    }
}
