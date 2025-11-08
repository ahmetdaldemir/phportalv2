<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('remote_api_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('request_class', 512);
            $table->string('remote_path', 2048);
            $table->integer('http_status');
            $table->longText('request')->nullable();
            $table->longText('response')->nullable();
            $table->longText('errors')->nullable();
            $table->boolean('failed')->default(0);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('remote_api_logs');
    }
};
