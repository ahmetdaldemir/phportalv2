<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_card_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stock_card_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('color_id')->nullable();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('reason_id');
            $table->mediumInteger('type');
            $table->mediumInteger('quantity');
            $table->string('serial_number', 50)->nullable();
            $table->string('tax')->nullable();
            $table->decimal('cost_price', 10, 2);
            $table->decimal('base_cost_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->float('discount', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('imei')->nullable();
            $table->boolean('assigned_accessory')->default(0);
            $table->boolean('tracking_quantity')->default(1);
            $table->boolean('assigned_device')->default(0);
            $table->unsignedBigInteger('company_id');
            $table->string('prefix', 3)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('stock_card_id')->references('id')->on('stock_cards');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->foreign('reason_id')->references('id')->on('reasons');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_card_movements');
    }
};
