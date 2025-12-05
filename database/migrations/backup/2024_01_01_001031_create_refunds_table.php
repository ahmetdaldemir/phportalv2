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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->foreignId('user_id');
            $table->unsignedBigInteger('stock_card_id')->nullable();
            $table->unsignedBigInteger('reason_id')->nullable();
            $table->unsignedBigInteger('color_id')->nullable();
            $table->string('serial_number', 255)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->foreignId('seller_id')->default(1);
            $table->text('description')->nullable();
            $table->dateTime('service_send_date')->nullable();
            $table->dateTime('service_return_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('seller_id');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('reason_id')->references('id')->on('reasons')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
            $table->foreign('stock_card_id')->references('id')->on('stock_cards')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
