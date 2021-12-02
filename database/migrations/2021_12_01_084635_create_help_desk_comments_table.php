<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpDeskCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_desk_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('help_desk_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->char("user_type")->comments("C=Customer, U=Backend Users who created the ticket")->default("C");
            $table->longtext("comment");
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
        Schema::dropIfExists('help_desk_comments');
    }
}
