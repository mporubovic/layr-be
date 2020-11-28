<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_card', function (Blueprint $table) {
            // $table->id();

            $table->unsignedBigInteger('board_id');
            $table->unsignedBigInteger('card_id');

            $table->timestamps();
            
            $table->foreign('board_id')->references('id')->on('boards')->onDelete('cascade');
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_card');
    }
}
