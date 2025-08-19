<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accountings', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->decimal('exchange', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('price', 10, 2)->comment('Sadece Kdv Fiyatı');
            $table->string('file')->nullable();
            $table->enum('paymentStatus', ['unpaid', 'paid', 'paidoutofpocket'])->default('unpaid')->comment('paidOutOfPocket=~Personel Cebinden Ödedi');
            $table->date('paymentDate')->nullable();
            $table->string('paymentStaff')->nullable();
            $table->string('periodMounth', 10)->nullable();
            $table->year('periodYear')->nullable();
            $table->unsignedBigInteger('accounting_category_id');
            $table->double('currency')->comment('Vergiler Dahil');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('accounting_category_id')->references('id')->on('accounting_categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accountings');
    }
};
