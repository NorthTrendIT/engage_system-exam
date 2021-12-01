<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpDesksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_desks', function (Blueprint $table) {
            $table->id();
            $table->string("ticket_number")->comments("auto generated ticket number like: OMS1011");
            $table->unsignedBigInteger('user_id')->index();
            $table->char("user_type")->comments("C=Customer, U=Backend Users who created the ticket")->default("C");
            $table->string("name")->comments("Name of the user/customer who creates the ticket");
            $table->string("email")->comments("Email of the user/customer who creates the ticket");
            $table->unsignedBigInteger('department_id')->index();
            $table->unsignedBigInteger("help_desk_urgency_id")->index()->comments("help_desk_urgencies table used for reference");
            $table->unsignedBigInteger("help_desk_status_id")->index()->comments("help_desk_statuses table used for reference");
            $table->string("subject");
            $table->longtext("message");
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
        Schema::dropIfExists('help_desks');
    }
}
