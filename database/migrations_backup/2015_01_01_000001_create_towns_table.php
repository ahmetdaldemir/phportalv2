<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('towns', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name');
            $table->unsignedInteger('city_id');
            
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    public function down()
    {
        Schema::dropIfExists('towns');
    }
};
