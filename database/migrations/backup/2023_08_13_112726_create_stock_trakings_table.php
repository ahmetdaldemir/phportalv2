<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_trakings', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->unsignedBigInteger('process_seller_id');
            $table->foreign('process_seller_id')->references('id')->on('sellers')->onDelete('cascade');

            $table->unsignedBigInteger('stock_seller_id');
            $table->foreign('stock_seller_id')->references('id')->on('sellers')->onDelete('cascade');

            $table->string('serial_number');

            $table->unsignedBigInteger('stock_card_id');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_trakings');
    }
};
