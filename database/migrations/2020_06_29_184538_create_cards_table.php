<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();            
            
            $table->unsignedBigInteger('user_id');
            
            $table->text('title');
            $table->text('type');
            $table->text('program');
            
            $table->smallInteger('x');
            $table->smallInteger('y');
            $table->smallInteger('width');
            $table->smallInteger('height');
            
            $table->json('settings')->nullable();

            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
