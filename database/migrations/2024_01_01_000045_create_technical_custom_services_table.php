<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('technical_custom_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('version_id');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('total_price', 10, 2);
            $table->decimal('customer_price', 10, 2);
            $table->string('type');
            $table->text('coating_information')->nullable();
            $table->text('print_information')->nullable();
            $table->unsignedBigInteger('delivery_staff');
            $table->unsignedBigInteger('seller_id');
            $table->boolean('payment_status')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('version_id')->references('id')->on('versions');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('delivery_staff')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('sellers');
        });
    }

    public function down()
    {
        Schema::dropIfExists('technical_custom_services');
    }
};
