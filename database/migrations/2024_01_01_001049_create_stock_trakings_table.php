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
        Schema::create('stock_trakings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('company_id');
            $table->unsignedBigInteger('process_seller_id');
            $table->unsignedBigInteger('stock_seller_id');
            $table->string('serial_number', 255);
            $table->unsignedBigInteger('stock_card_id');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('process_seller_id')->references('id')->on('sellers')->onDelete('cascade');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('cascade');
            $table->foreign('stock_seller_id')->references('id')->on('sellers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_trakings');
    }
};
