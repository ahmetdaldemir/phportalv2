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
        Schema::create('technical_service_products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->unsignedBigInteger('technical_service_id');
            $table->foreign('technical_service_id')->references('id')->on('technical_services')->onDelete('cascade');

            $table->unsignedBigInteger('stock_card_id');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('cascade');

            $table->unsignedBigInteger('stock_card_movement_id');
            $table->foreign('stock_card_movement_id')->references('id')->on('stock_card_movements')->onDelete('cascade');


            $table->string('serial_number');
            $table->string('quantity');
            $table->double('sale_price',10,2);
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
        Schema::dropIfExists('technical_service_products');
    }
};
