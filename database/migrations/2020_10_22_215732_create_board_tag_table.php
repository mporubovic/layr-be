<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_tag', function (Blueprint $table) {
            $table->primary(['board_id', 'tag_id']);
            
            $table->unsignedBigInteger('board_id');
            $table->unsignedBigInteger('tag_id');

            $table->timestamps();

            $table->foreign('board_id')->references('id')->on('boards')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_tag');
    }
}
