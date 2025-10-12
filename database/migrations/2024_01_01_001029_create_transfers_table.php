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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->tinyInteger('is_status')->default(1);
            $table->unsignedBigInteger('main_seller_id')->nullable();
            $table->unsignedBigInteger('comfirm_id')->nullable();
            $table->date('comfirm_date')->nullable();
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->text('stocks')->nullable();
            $table->unsignedBigInteger('delivery_seller_id');
            $table->foreignId('company_id');
            $table->text('description')->nullable();
            $table->text('number');
            $table->text('serial_list')->nullable();
            $table->unsignedBigInteger('reason_id')->nullable();
            $table->string('type', 255)->nullable();
            $table->text('detail')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('comfirm_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('delivery_id')->references('id')->on('users');
            $table->foreign('delivery_seller_id')->references('id')->on('sellers');
            $table->foreign('main_seller_id')->references('id')->on('sellers');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
