<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('stock_card_id');
            $table->enum('type', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'])->comment('1 = Telefon↲2 = Aksesuar↲3 = Teknik Ürün↲4 = Diğer');
            $table->integer('stock_card_movement_id');
            $table->integer('invoice_id');
            $table->decimal('sale_price', 10, 2);
            $table->decimal('customer_price', 10, 2);
            $table->decimal('cash_price', 10, 2);
            $table->decimal('credit_card_pricredit_card_price', 10, 2);
            $table->decimal('instalment_price', 10, 2);
            $table->string('name');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('serial');
            $table->decimal('discount', 10, 2);
            $table->datetime('creates_dates');
            $table->integer('technical_service_person_id');
            $table->decimal('base_cost_price', 10, 2);
            $table->integer('delivery_personnel');
            $table->timestamps();
            
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards');
            $table->foreign('stock_card_movement_id')->references('id')->on('stock_card_movements');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
