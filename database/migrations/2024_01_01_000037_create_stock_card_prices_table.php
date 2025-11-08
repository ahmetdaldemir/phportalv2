<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_card_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('stock_card_id');
            $table->decimal('cost_price', 10, 2);
            $table->decimal('base_cost_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_card_prices');
    }
};
