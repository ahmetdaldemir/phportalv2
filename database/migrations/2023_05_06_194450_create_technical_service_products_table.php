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
        Schema::create('technical_service_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('technical_service_id');
            $table->unsignedBigInteger('stock_card_id');
            $table->unsignedBigInteger('stock_card_movement_id');
            $table->string('serial_number');
            $table->string('quantity');
            $table->decimal('sale_price', 10, 2);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('technical_service_id')->references('id')->on('technical_services')->onDelete('cascade');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('cascade');
            $table->foreign('stock_card_movement_id')->references('id')->on('stock_card_movements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_service_products');
    }
};
