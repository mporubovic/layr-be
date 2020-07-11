<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStackUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stack_user', function (Blueprint $table) {
            $table->id();
            $table->primary(['stack_id', 'user_id']);

            $table->unsignedBigInteger('stack_id');
            $table->unsignedBigInteger('user_id');

            $table->text('permissions');
          
            $table->timestamps();
          
            $table->foreign('stack_id')->references('id')->on('stacks')->onDelete('cascade');
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
        Schema::dropIfExists('stack_user');
    }
}
