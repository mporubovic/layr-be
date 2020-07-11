<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_user', function (Blueprint $table) {
            $table->id();

            $table->primary(['card_id', 'user_id']);
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('user_id');

            $table->text('permissions');

            $table->timestamps();

            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
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
        Schema::dropIfExists('card_user');
    }
}
