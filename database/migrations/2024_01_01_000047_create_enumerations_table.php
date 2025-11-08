<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enumerations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('seller_id');
            $table->timestamp('start_date');
            $table->timestamp('finish_date');
            $table->longText('dataCollection');
            $table->longText('stockCollection');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('seller_id')->references('id')->on('sellers');
        });
    }

    public function down()
    {
        Schema::dropIfExists('enumerations');
    }
};
