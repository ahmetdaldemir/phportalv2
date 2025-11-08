<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('stock_card_id');
            $table->unsignedBigInteger('reason_id');
            $table->unsignedBigInteger('color_id')->nullable();
            $table->unsignedBigInteger('seller_id');
            $table->string('serial_number')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->text('description')->nullable();
            $table->datetime('service_send_date')->nullable();
            $table->datetime('service_return_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards');
            $table->foreign('reason_id')->references('id')->on('reasons');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->foreign('seller_id')->references('id')->on('sellers');
        });
    }

    public function down()
    {
        Schema::dropIfExists('refunds');
    }
};
