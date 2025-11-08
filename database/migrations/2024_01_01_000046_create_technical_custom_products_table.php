<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('technical_custom_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('technical_custom_id');
            $table->unsignedBigInteger('stock_card_id');
            $table->unsignedBigInteger('stock_card_movement_id');
            $table->string('serial_number');
            $table->string('quantity');
            $table->decimal('sale_price', 10, 2);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('technical_custom_id')->references('id')->on('technical_custom_services');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards');
            $table->foreign('stock_card_movement_id')->references('id')->on('stock_card_movements');
        });
    }

    public function down()
    {
        Schema::dropIfExists('technical_custom_products');
    }
};
