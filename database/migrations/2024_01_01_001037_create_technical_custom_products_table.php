<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('technical_custom_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('company_id');
            $table->unsignedBigInteger('technical_custom_id');
            $table->unsignedBigInteger('stock_card_id');
            $table->unsignedBigInteger('stock_card_movement_id');
            $table->string('serial_number', 255);
            $table->string('quantity', 255);
            $table->double('sale_price');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('cascade');
            $table->foreign('stock_card_movement_id')->references('id')->on('stock_card_movements')->onDelete('cascade');
            $table->foreign('technical_custom_id')->references('id')->on('technical_custom_services')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_custom_products');
    }
};
