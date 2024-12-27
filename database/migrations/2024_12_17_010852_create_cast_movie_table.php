<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('cast_movie', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name', 255);
        $table->uuid('cast_id');
        $table->uuid('movie_id');
        $table->timestamps();

        $table->foreign('cast_id')->references('id')->on('casts')->onDelete('cascade');
        $table->foreign('movie_id')->references('id')->on('movie')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('cast_movie');
}

};
