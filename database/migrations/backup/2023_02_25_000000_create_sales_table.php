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
            $table->timestamps();
            $table->integer('stock_card_id')->nullable();
            $table->enum('type', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'])->default('2')->comment('1 = Telefon\n2 = Aksesuar\n3 = Teknik Ürün\n4 = Diğer');
            $table->integer('stock_card_movement_id')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('customer_price', 10, 2)->nullable();
            $table->decimal('cash_price', 10, 2)->nullable();
            $table->decimal('credit_card_pricredit_card_price', 10, 2)->nullable();
            $table->decimal('instalment_price', 10, 2)->nullable();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('seller_id')->default(1);
            $table->unsignedBigInteger('user_id')->default(1);
            $table->unsignedBigInteger('company_id')->default(1);
            $table->bigInteger('customer_id')->nullable();
            $table->string('serial')->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->datetime('creates_dates')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->integer('technical_service_person_id')->nullable();
            $table->decimal('base_cost_price', 10, 2)->default(0.00);
            $table->integer('delivery_personnel')->nullable();
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
