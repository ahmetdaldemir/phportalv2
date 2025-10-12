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
        Schema::create('accountings', function (Blueprint $table) {
            $table->double('exchange');
            $table->double('tax');
            $table->double('price');
            $table->string('file', 255)->nullable();
            $table->string('paymentStatus');
            $table->date('paymentDate')->nullable()->default('2023-03-26');
            $table->string('paymentStaff', 255)->nullable();
            $table->string('periodMounth', 10)->nullable();
            $table->year('periodYear')->nullable();
            $table->unsignedBigInteger('accounting_category_id');
            $table->double('currency')->comment('Vergiler Dahil');
            $table->text('description');
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('accounting_category_id')->references('id')->on('accounting_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accountings');
    }
};
