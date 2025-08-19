<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('version_children', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('version_id');
            $table->string('name');
            $table->timestamps();
            
            $table->foreign('version_id')->references('id')->on('versions');
        });
    }

    public function down()
    {
        Schema::dropIfExists('version_children');
    }
};
