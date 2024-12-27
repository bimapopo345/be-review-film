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
    Schema::create('movie', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('title', 255);
        $table->text('summary');
        $table->string('poster', 255)->nullable();
        $table->uuid('genre_id');
        $table->string('year', 4);
        $table->timestamps();

        $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('movie');
}

};
