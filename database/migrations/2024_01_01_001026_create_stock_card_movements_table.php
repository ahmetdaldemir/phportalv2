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
        Schema::create('stock_card_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_card_id');
            $table->foreignId('user_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreignId('seller_id')->nullable();
            $table->unsignedBigInteger('reason_id')->nullable();
            $table->mediumInteger('type')->nullable();
            $table->mediumInteger('quantity');
            $table->string('serial_number', 50)->nullable();
            $table->string('barcode', 50)->nullable();
            $table->string('tax', 255)->default(18);
            $table->double('cost_price');
            $table->double('base_cost_price');
            $table->double('sale_price');
            $table->float('discount');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('imei', 255)->nullable();
            $table->boolean('assigned_accessory')->default(0);
            $table->boolean('tracking_quantity')->default(0);
            $table->boolean('assigned_device')->nullable();
            $table->foreignId('company_id')->nullable()->default(1);
            $table->string('prefix', 3)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('id');
            $table->index('serial_number');
            $table->index('company_id');
            $table->index('serial_number');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('reason_id')->references('id')->on('reasons');
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_card_movements');
    }
};
