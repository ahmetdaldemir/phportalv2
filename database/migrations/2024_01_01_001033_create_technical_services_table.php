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
        Schema::create('technical_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->foreignId('user_id');
            $table->text('brand_id')->nullable();
            $table->text('version_id')->nullable();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->unsignedBigInteger('stock_card_movement_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('serial_number', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->double('total_price');
            $table->double('customer_price');
            $table->text('physical_condition')->nullable();
            $table->text('fault_information')->nullable();
            $table->text('accessories')->nullable();
            $table->string('device_password', 255)->nullable();
            $table->bigInteger('delivery_staff')->nullable();
            $table->text('products')->nullable();
            $table->foreignId('seller_id');
            $table->text('accessory_category')->nullable();
            $table->text('physically_category')->nullable();
            $table->text('fault_category')->nullable();
            $table->boolean('payment_status')->default(0);
            $table->bigInteger('payment_person')->nullable();
            $table->bigInteger('technical_person')->nullable();
            $table->string('imei', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('delivery_staff')->references('id')->on('users');
            $table->foreign('payment_person')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->foreign('stock_card_movement_id')->references('id')->on('stock_card_movements');
            $table->foreign('stock_id')->references('id')->on('stock_cards');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_services');
    }
};
