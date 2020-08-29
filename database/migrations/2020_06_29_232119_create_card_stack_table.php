<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardStackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_stack', function (Blueprint $table) {
            // $table->id();

            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('stack_id');

            $table->unsignedInteger('position');
            $table->boolean('open');
            
            $table->timestamps();
            
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
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
        Schema::dropIfExists('card_stack');
    }
}
