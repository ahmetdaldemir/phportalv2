<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('technical_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->text('brand_id')->nullable();
            $table->text('version_id')->nullable();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->unsignedBigInteger('stock_card_movement_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->string('serial_number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->decimal('customer_price', 10, 2);
            $table->text('physical_condition')->nullable();
            $table->text('fault_information')->nullable();
            $table->text('accessories')->nullable();
            $table->string('device_password')->nullable();
            $table->unsignedBigInteger('delivery_staff')->nullable();
            $table->text('products')->nullable();
            $table->unsignedBigInteger('seller_id');
            $table->text('accessory_category')->nullable();
            $table->text('physically_category')->nullable();
            $table->text('fault_category')->nullable();
            $table->boolean('payment_status')->default(0);
            $table->unsignedBigInteger('payment_person')->nullable();
            $table->unsignedBigInteger('technical_person')->nullable();
            $table->string('imei')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('stock_id')->references('id')->on('stock_cards');
            $table->foreign('stock_card_movement_id')->references('id')->on('stock_card_movements');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('delivery_staff')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->foreign('payment_person')->references('id')->on('users');
            $table->foreign('technical_person')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('technical_services');
    }
};
