<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpDeskStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_desk_statuses', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("color_code")->default("#fc5603");
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('help_desk_statuses')->insert(
            array(
                array('name' => 'Open', 'color_code' => '#008000' ),
                array('name' => 'In Progress', 'color_code' => '#ffa500' ),
                array('name' => 'Answered', 'color_code' => '#b3b30f' ),
                array('name' => 'Closed', 'color_code' => '#ff0000' )
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_desk_statuses');
    }
}
