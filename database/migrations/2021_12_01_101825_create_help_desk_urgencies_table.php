<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpDeskUrgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_desk_urgencies', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("color_code")->default("#0362fc");
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert some stuff
        DB::table('help_desk_urgencies')->insert(
            array(
                array('name' => 'Low', 'color_code' => '#008000' ),
                array('name' => 'Medium', 'color_code' => '#0000ff' ),
                array('name' => 'High', 'color_code' => '#ef5a5ad1' ),
                array('name' => 'Critical', 'color_code' => '#ff0202' )
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
        Schema::dropIfExists('help_desk_urgencies');
    }
}
