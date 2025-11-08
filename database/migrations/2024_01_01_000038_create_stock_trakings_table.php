<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_trakings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('process_seller_id');
            $table->unsignedBigInteger('stock_seller_id');
            $table->string('serial_number');
            $table->unsignedBigInteger('stock_card_id');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('process_seller_id')->references('id')->on('sellers');
            $table->foreign('stock_seller_id')->references('id')->on('sellers');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_trakings');
    }
};
