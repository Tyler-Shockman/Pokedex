<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('captures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('pokemon_id');
            $table->timestamps();

            $table->unique(['user_id', 'pokemon_id']); // A user (trainer) can only mark a pokemon as captured once.
        });
        Schema::enableForeignKeyConstraints('captures', function (Blueprint $table){
            $table->foreign('user_id')
            ->references('id')
            ->on('users');
            $table->foreign('pokemon_id')
            ->references('id')
            ->on('pokemon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('captures');
    }
}
