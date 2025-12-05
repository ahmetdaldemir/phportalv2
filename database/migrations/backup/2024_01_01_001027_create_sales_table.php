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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('stock_card_id')->nullable();
            $table->string('type');
            $table->integer('stock_card_movement_id')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->decimal('sale_price');
            $table->decimal('customer_price');
            $table->decimal('cash_price');
            $table->decimal('credit_card_pricredit_card_price');
            $table->decimal('instalment_price');
            $table->string('name', 255)->nullable();
            $table->foreignId('seller_id')->nullable()->default(1);
            $table->foreignId('user_id')->nullable()->default(1);
            $table->foreignId('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('serial', 255)->nullable();
            $table->decimal('discount');
            $table->dateTime('creates_dates')->nullable();
            $table->integer('technical_service_person_id')->nullable();
            $table->decimal('base_cost_price');
            $table->integer('delivery_personnel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
