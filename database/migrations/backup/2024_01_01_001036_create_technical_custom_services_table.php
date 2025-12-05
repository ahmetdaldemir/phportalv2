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
        Schema::create('technical_custom_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->foreignId('user_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('version_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->double('total_price');
            $table->double('customer_price');
            $table->string('type', 255)->nullable();
            $table->text('coating_information')->nullable();
            $table->text('print_information')->nullable();
            $table->bigInteger('delivery_staff')->nullable();
            $table->foreignId('seller_id');
            $table->boolean('payment_status')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('delivery_staff')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_custom_services');
    }
};
