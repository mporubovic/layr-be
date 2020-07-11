<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardStackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_stack', function (Blueprint $table) {
            $table->id();
            $table->primary(['board_id', 'stack_id']);
            
            $table->unsignedBigInteger('board_id');
            $table->unsignedBigInteger('stack_id');

            $table->timestamps();

            $table->foreign('board_id')->references('id')->on('boards')->onDelete('cascade');
            $table->foreign('stack_id')->references('id')->on('stacks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_stack');
    }
}
