<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('demands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('stock_card_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('color_id')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_status')->default(1);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards');
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->foreign('color_id')->references('id')->on('colors');
        });
    }

    public function down()
    {
        Schema::dropIfExists('demands');
    }
};
